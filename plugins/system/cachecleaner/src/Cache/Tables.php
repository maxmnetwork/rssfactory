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

class Tables extends Cache
{
    public static function purge()
    {
        $params = Params::get();

        if (empty($params->clean_tables_selection))
        {
            return;
        }

        $tables = explode(',', str_replace("\n", ',', $params->clean_tables_selection));

        foreach ($tables as $table)
        {
            self::emptyTable($table);
        }
    }
}
