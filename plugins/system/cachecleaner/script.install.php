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

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File as JFile;
use Joomla\CMS\Filesystem\Folder as JFolder;

class PlgSystemCacheCleanerInstallerScript
{
    public function postflight($install_type, $adapter)
    {
        if ( ! in_array($install_type, ['install', 'update']))
        {
            return true;
        }

        self::deleteJoomla3Files();
        self::deleteOldFiles();

        return true;
    }

    private static function delete($files = [])
    {
        foreach ($files as $file)
        {
            if (is_dir($file))
            {
                JFolder::delete($file);
            }

            if (is_file($file))
            {
                JFile::delete($file);
            }
        }
    }

    private static function deleteJoomla3Files()
    {
        self::delete(
            [
                JPATH_SITE . '/media/cachecleaner/images',
                JPATH_SITE . '/media/cachecleaner/less',
                JPATH_SITE . '/plugins/system/cachecleaner/vendor',
                JPATH_SITE . '/plugins/system/cachecleaner/src/Cache/JotCache.php',
                JPATH_SITE . '/plugins/system/cachecleaner/src/Cache/JotCacheMainModelMain.php',
            ]
        );
    }

    private static function deleteOldFiles()
    {
        self::delete(
            [
                JPATH_SITE . '/plugins/system/cachecleaner/src/Clean.php',
                JPATH_SITE . '/plugins/system/cachecleaner/src/Helper.php',
                JPATH_SITE . '/plugins/system/cachecleaner/src/Plugin.php',
                JPATH_SITE . '/plugins/system/cachecleaner/src/Protect.php',
            ]
        );
    }
}
