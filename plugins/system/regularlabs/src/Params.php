<?php
/**
 * @package         Regular Labs Library
 * @version         24.6.11852
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Plugin\System\RegularLabs;

defined('_JEXEC') or die;

use RegularLabs\Library\Parameters as RL_Parameters;

class Params
{
    protected static $params = null;

    public static function get()
    {
        if ( ! is_null(self::$params))
        {
            return self::$params;
        }

        self::$params = RL_Parameters::getPlugin('regularlabs');

        return self::$params;
    }
}
