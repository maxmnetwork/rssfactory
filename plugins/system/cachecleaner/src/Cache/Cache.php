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

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Filesystem\File as JFile;
use Joomla\CMS\Filesystem\Folder as JFolder;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\File as RL_File;
use RegularLabs\Plugin\System\CacheCleaner\Cache as CC_Cache;
use RegularLabs\Plugin\System\CacheCleaner\Params;

class Cache
{
    static $ignore_folders = null;
    static $size           = 0;

    public static function addError($error = true)
    {
        CC_Cache::addError($error);
    }

    public static function addMessage($message = '')
    {
        CC_Cache::addMessage($message);
    }

    public static function emptyFolder($path, $min_age_in_minutes = 0)
    {
        $params = Params::get();

        if ( ! JFolder::exists($path))
        {
            return;
        }

        $size = 0;

        if ($params->show_size)
        {
            $size = self::getFolderSize($path);
        }

        // remove folders
        $folders = JFolder::folders($path, '.', false, false, [], []);

        foreach ($folders as $folder)
        {
            $f = $path . '/' . $folder;

            if (in_array($f, self::getIgnoreFolders()) || ! @opendir($path . '/' . $folder))
            {
                continue;
            }

            if (self::isIgnoredParent($f))
            {
                self::emptyFolder($f);
                continue;
            }

            RL_File::deleteFolder($path . '/' . $folder, false, $min_age_in_minutes);

            // Zoo folder needs to be placed back, otherwise Zoo will break (stupid!)
            if ($folder == 'com_zoo')
            {
                JFolder::create($path . '/' . $folder);
            }
        }

        // remove files
        $files = JFolder::files($path, '.', false, false, [], []);

        foreach ($files as $file)
        {
            $file_path = $path . '/' . $file;

            if ( ! is_file($file_path))
            {
                continue;
            }

            if (
                $file == 'index.html'
                || in_array($path, self::getIgnoreFolders())
                || in_array($file_path, self::getIgnoreFolders())
                || $file_path == JPATH_ADMINISTRATOR . '/cache/autoload_psr4.php'
            )
            {
                continue;
            }

            $deleted = RL_File::delete($file_path, false, $min_age_in_minutes);

            if ( ! $deleted)
            {
                self::addError(JText::sprintf('JLIB_FILESYSTEM_DELETE_FAILED', $file_path));
            }
        }

        if ($params->show_size)
        {
            $size -= self::getFolderSize($path);

            self::$size += $size;
        }
    }

    public static function emptyFolderList($folders)
    {
        if (empty($folders))
        {
            return;
        }

        if ( ! is_array($folders))
        {
            $folders = explode("\n", str_replace('\n', "\n", $folders));
        }

        foreach ($folders as $folder)
        {
            if ( ! trim($folder))
            {
                continue;
            }

            $folder = rtrim(str_replace('\\', '/', trim($folder)), '/');
            $path   = str_replace('//', '/', JPATH_SITE . '/' . $folder);
            self::emptyFolder($path);
        }
    }

    public static function emptyFolders()
    {
        $params = Params::get();

        // Empty tmp folder
        if ($params->clean_tmp)
        {
            self::emptyFolder(JPATH_SITE . '/tmp');
        }

        // Empty custom folders
        if ($params->clean_folders)
        {
            self::emptyFolderList($params->clean_folders_selection);
        }
    }

    public static function emptyTable($table)
    {
        if (trim($table) == '')
        {
            return;
        }

        $db    = JFactory::getDbo();
        $table = trim(str_replace('#__', $db->getPrefix(), $table));

        $db->setQuery('SHOW TABLES LIKE ' . $db->quote($table));

        if ( ! $db->loadResult())
        {
            return;
        }

        $db->setQuery('TRUNCATE TABLE `' . $table . '`');
        $db->execute();
    }

    public static function getError()
    {
        return CC_Cache::getError();
    }

    public static function getFolderSize($path)
    {
        if (is_file($path))
        {
            return @filesize($path);
        }

        if ( ! JFolder::exists($path) || ! (@opendir($path)))
        {
            return 0;
        }

        $size = 0;

        foreach (JFolder::files($path) as $file)
        {
            $size += @filesize($path . '/' . $file);
        }

        foreach (JFolder::folders($path) as $folder)
        {
            if ( ! @opendir($path . '/' . $folder))
            {
                continue;
            }

            $size += self::getFolderSize($path . '/' . $folder);
        }

        return $size;
    }

    public static function getIgnoreFolders()
    {
        if ( ! is_null(self::$ignore_folders))
        {
            return self::$ignore_folders;
        }

        $params = Params::get();

        if (empty($params->ignore_folders))
        {
            self::$ignore_folders = [];

            return self::$ignore_folders;
        }

        $ignore_folders = explode("\n", str_replace('\n', "\n", $params->ignore_folders));

        foreach ($ignore_folders as &$folder)
        {
            if (trim($folder) == '')
            {
                continue;
            }

            $folder = rtrim(str_replace('\\', '/', trim($folder)), '/');
            $folder = str_replace('//', '/', JPATH_SITE . '/' . $folder);
        }

        self::$ignore_folders = $ignore_folders;

        return self::$ignore_folders;
    }

    public static function getMessage()
    {
        return CC_Cache::getMessage();
    }

    public static function getSize()
    {
        if ( ! self::$size)
        {
            return false;
        }

        if (self::$size < 1024)
        {
            // Return in Bs
            return self::$size . ' bytes';
        }

        if (self::$size < (1024 * 1024))
        {
            // Return in KBs
            return round(self::$size / 1024, 2) . ' KB';
        }

        // Return in MBs
        return round(self::$size / (1024 * 1024), 2) . ' MB';
    }

    /**
     * Check if folder is a parent path of something in the ignore list
     */
    public static function isIgnoredParent($path)
    {
        $check = $path . '/';

        foreach (self::getIgnoreFolders() as $ignore_folder)
        {
            if (str_starts_with($ignore_folder, $check))
            {
                return true;
            }
        }

        return false;
    }

    public static function purgeTables()
    {
    }

    public static function setError($error = true)
    {
        CC_Cache::setError($error);
    }

    public static function setMessage($message = '')
    {
        CC_Cache::setMessage($message);
    }

    public static function updateLog()
    {
        $params = Params::get();

        // Write current time to text file

        $file_path = str_replace('//', '/', JPATH_SITE . '/' . str_replace('\\', '/', $params->log_path . '/'));

        if ( ! JFolder::exists($file_path))
        {
            $file_path = JPATH_PLUGINS . '/system/cachecleaner/';
        }

        $time = time();
        JFile::write($file_path . 'cachecleaner_lastclean.log', $time);
    }
}
