<?php
/**
 * @package         Cache Cleaner
 * @version         9.3.0PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Plugin\System\CacheCleaner\Cache;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Cache\Cache as JCache;
use Joomla\CMS\Cache\CacheController;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Filesystem\Folder as JFolder;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Plugin\System\CacheCleaner\Params;
use RuntimeException;

class Joomla extends Cache
{
    public static function checkIn()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $tables = $db->getTableList();

        foreach ($tables as $table)
        {
            // make sure we get the right tables based on prefix
            if ( ! str_starts_with($table, $db->getPrefix()))
            {
                continue;
            }

            $columns = RL_DB::getTableColumns($table, false);

            if ( ! (isset($columns['checked_out']) && isset($columns['checked_out_time'])))
            {
                continue;
            }

            $query->clear()->update(RL_DB::quoteName($table));
            self::checkInAddCheckoutWheres($query, $columns);
            self::checkInAddCheckoutSets($query, $columns);

            try
            {
                $db->setQuery($query);
                $db->execute();

                JFactory::getApplication()->triggerEvent('onAfterCheckin', [$table]);
            }
            catch (RuntimeException $e)
            {
                continue;
            }
        }
    }

    public static function checkInAddCheckoutSets(&$query, $fields)
    {
        $query->set(RL_DB::quoteName('checked_out') . ' = DEFAULT');

        if (isset($fields['editor']))
        {
            $query->set('editor = NULL');
        }

        if ($fields['checked_out_time']->Null === 'YES')
        {
            $query->set(RL_DB::quoteName('checked_out_time') . ' = NULL');

            return;
        }

        $null_date = RL_DB::getNullDate();
        $query->set(RL_DB::quoteName('checked_out_time') . ' = :checkouttime')
            ->bind(':checkouttime', $null_date);
    }

    public static function checkInAddCheckoutWheres(&$query, $fields)
    {
        if ($fields['checked_out']->Null === 'YES')
        {
            $query->where(RL_DB::quoteName('checked_out') . ' IS NOT NULL');

            return;
        }

        $query->where(RL_DB::quoteName('checked_out') . ' > 0');
    }

    public static function invalidateMediaVersions()
    {
        // Refresh versionable assets cache
        JFactory::getApplication()->flushAssets();

        self::clearMediaVersionCache();
        self::updateMediaVersionInJsonFiles();
    }

    public static function purge()
    {
        $cache = self::getCache();

        if (isset($cache->options['storage']) && $cache->options['storage'] != 'file')
        {
            foreach ($cache->getAll() as $group)
            {
                $cache->clean($group->group);
            }

            return;
        }

        $cache_path = JFactory::getConfig()->get('cache_path', JPATH_SITE . '/cache');

        $min_age = Params::get()->clean_cache_min_age;

        self::emptyFolder($cache_path, $min_age);
        self::emptyFolder(JPATH_ADMINISTRATOR . '/cache', $min_age);
    }

    public static function purgeDisabledRedirects()
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->delete('#__redirect_links')
            ->where('published = ' . $db->quote(0));

        $min_age_in_days = (int) Params::get()->purge_disabled_redirects_min_age;

        if ($min_age_in_days)
        {
            $to_date = JFactory::getDate()
                ->modify('-' . $min_age_in_days . ' days')
                ->toSql();

            $query->where('modified_date < ' . $db->quote($to_date));
        }

        $db->setQuery($query);
        $db->execute();
    }

    public static function purgeExpired()
    {
        $min_age = JFactory::getConfig()->get('cachetime');

        if ( ! $min_age)
        {
            return;
        }

        $cache_path = JFactory::getConfig()->get('cache_path', JPATH_SITE . '/cache');

        self::emptyFolder($cache_path, $min_age);
    }

    public static function purgeLiteSpeed()
    {
        header('X-LiteSpeed-Purge: *');
    }

    public static function purgeOPcache()
    {
        if (
            function_exists('opcache_reset')
            && function_exists('opcache_get_status')
            && ! empty(@opcache_get_status()['opcache_enabled'])
        )
        {
            @opcache_reset();

            return;
        }

        if (function_exists('apc_clear_cache'))
        {
            @apc_clear_cache();

            return;
        }
    }

    public static function purgeMemcached()
    {
        if ( ! class_exists('Memcached'))
        {
            return;
        }

        $params = Params::get();

        $memcached = new \Memcached();
        $memcached->addServer($params->memcached_host, $params->memcached_port);

        $memcached->flush();
    }

    public static function purgeRedis()
    {
        if ( ! class_exists('Redis'))
        {
            return;
        }

        $params = Params::get();

        $redis = new \Redis();
        try
        {
            $redis->pconnect($params->redis_host, $params->redis_port);
            $redis->flushAll();
        }
        catch (Exception $e)
        {
            // Do nothing
        }
    }

    public static function purgeUpdates()
    {
        $db = JFactory::getDbo();

        $db->setQuery('TRUNCATE TABLE #__updates');

        if ( ! $db->execute())
        {
            return;
        }

        // Reset the last update check timestamp
        $query = $db->getQuery(true)
            ->update('#__update_sites')
            ->set('last_check_timestamp = ' . $db->quote(0));
        $db->setQuery($query);
        $db->execute();
    }

    private static function clearFileInOPCache($file)
    {
        $hasOpCache = ini_get('opcache.enable')
            && function_exists('opcache_invalidate')
            && (
                ! ini_get('opcache.restrict_api')
                || stripos(realpath($_SERVER['SCRIPT_FILENAME']), ini_get('opcache.restrict_api')) === 0
            );

        if ( ! $hasOpCache)
        {
            return false;
        }

        return opcache_invalidate($file, true);
    }

    private static function clearMediaVersionCache()
    {
        $cache_component = JFactory::getApplication()->bootComponent('com_cache');

        if ( ! $cache_component)
        {
            return;
        }

        $cache_model = $cache_component->getMVCFactory()
            ->createModel('Cache', 'Administrator', ['ignore_request' => true]);

        try
        {
            $cache_model->cleanlist(['_media_version']);
        }
        catch (Exception $e)
        {
            // Do nothing
        }
    }

    /**
     * @return CacheController
     */
    private static function getCache()
    {
        $config = JFactory::getConfig();

        $options = [
            'defaultgroup' => '',
            'storage'      => $config->get('cache_handler', ''),
            'caching'      => true,
            'cachebase'    => $config->get('cache_path', JPATH_SITE . '/cache'),
        ];

        $cache = JCache::getInstance('callback', $options);

        return $cache;
    }

    public static function recreateNamespaceMap()
    {
        // Remove the administrator/cache/autoload_psr4.php file
        $filename = JPATH_ADMINISTRATOR . '/cache/autoload_psr4.php';

        if (file_exists($filename))
        {
            self::clearFileInOPCache($filename);
            clearstatcache(true, $filename);

            @unlink($filename);
        }

        JFactory::getApplication()->createExtensionNamespaceMap();
    }

    private static function updateMediaVersionInJsonFile($file, $new_version)
    {
        if ($file === JPATH_ROOT . '/media/vendor/joomla.asset.json')
        {
            return;
        }

        $json = file_get_contents($file);

        if ( ! $json)
        {
            return;
        }

        $json = json_decode($json);

        if ( ! $json)
        {
            return;
        }

        if (empty($json->assets))
        {
            return;
        }

        foreach ($json->assets as &$asset)
        {
            if (empty($asset->version))
            {
                continue;
            }

            $asset->version = $new_version;
        }

        $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ( ! $json)
        {
            return;
        }

        file_put_contents($file, $json);
    }

    private static function updateMediaVersionInJsonFiles()
    {
        $new_version = md5(JFactory::getDate()->toSql());

        $files = JFolder::files(JPATH_ROOT . '/media', 'joomla\.asset\.json$', true, true);

        foreach ($files as $file)
        {
            self::updateMediaVersionInJsonFile($file, $new_version);
        }
    }
}
