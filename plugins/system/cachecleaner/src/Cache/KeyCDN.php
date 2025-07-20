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
use Joomla\CMS\Language\Text as JText;
use KeyCDN as ApiKeyCDN;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Plugin\System\CacheCleaner\Params;

class KeyCDN extends Cache
{
    public static function getAPI($key)
    {
        require_once __DIR__ . '/../Api/KeyCDN.php';

        return new ApiKeyCDN(trim($key));
    }

    public static function purge()
    {
        $params = Params::get();

        $key   = RL_Input::get('k', $params->keycdn_authentication_key);
        $zones = RL_Input::get('z', $params->keycdn_zones);

        if (empty($key))
        {
            self::addError(JText::sprintf('CC_ERROR_CDN_NO_AUTHENTICATION_KEY', JText::_('CC_KEYCDN')));

            return -1;
        }

        if (empty($zones))
        {
            self::addError(JText::sprintf('CC_ERROR_CDN_NO_ZONES', JText::_('CC_KEYCDN')));

            return -1;
        }

        $api = self::getAPI($key);

        if ( ! $api || is_string($api))
        {
            self::addError(JText::sprintf('CC_ERROR_CDN_COULD_NOT_INITIATE_API', JText::_('CC_KEYCDN')));

            if (is_string($api))
            {
                self::addError($api);
            }

            return false;
        }

        $zones = explode(',', $zones);

        foreach ($zones as $zone)
        {
            $api_call = json_decode($api->get('zones/purge/' . $zone . '.json'));

            if ( ! is_null($api_call) && isset($api_call->status) && $api_call->status == 'success')
            {
                continue;
            }

            self::addError(JText::sprintf('CC_ERROR_CDN_COULD_NOT_PURGE_ZONE', JText::_('CC_KEYCDN'), $zone));

            return false;
        }

        return true;
    }
}
