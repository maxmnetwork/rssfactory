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

use RegularLabs\Plugin\System\CacheCleaner\Params;

class Folders extends Cache
{
    /**
     * Empty custom folder
     */
    public static function purge_folders()
    {
        $params = Params::get();

        if (empty($params->clean_folders_selection))
        {
            return;
        }

        $min_age = $params->clean_folders_min_age;
        $folders = explode("\n", str_replace('\n', "\n", $params->clean_folders_selection));

        foreach ($folders as $folder)
        {
            if ( ! trim($folder))
            {
                continue;
            }

            $folder = rtrim(str_replace('\\', '/', trim($folder)), '/');
            $path   = str_replace('//', '/', JPATH_SITE . '/' . $folder);

            self::emptyFolder($path, $min_age);
        }
    }

    /**
     * Empty tmp folder
     */
    public static function purge_tmp()
    {
        $min_age = Params::get()->clean_tmp_min_age;
        self::emptyFolder(JPATH_SITE . '/tmp', $min_age);
    }
}
