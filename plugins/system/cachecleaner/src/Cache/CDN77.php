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

use CDN77 as ApiCDN77;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Plugin\System\CacheCleaner\Params;

class CDN77 extends Cache
{
    public static function getAPI($login, $pass)
    {
        require_once __DIR__ . '/../Api/CDN77.php';

        return new ApiCDN77(trim($login), trim($pass));
    }

    public static function purge()
    {
        $params = Params::get();

        $login = RL_Input::get('l', $params->cdn77_login);
        $pass  = RL_Input::get('p', $params->cdn77_passwd);
        $ids   = RL_Input::get('i', $params->cdn77_ids);

        if (empty($login))
        {
            self::addError(JText::sprintf('CC_ERROR_CDN_NO_USERNAME', JText::_('CC_CDN77')));

            return -1;
        }

        if (empty($pass))
        {
            self::addError(JText::sprintf('CC_ERROR_CDN_NO_PASSWORD', JText::_('CC_CDN77')));

            return -1;
        }

        if (empty($params->cdn77_ids))
        {
            self::addError(JText::sprintf('CC_ERROR_CDN_NO_IDS', JText::_('CC_CDN77')));

            return -1;
        }

        $api = self::getAPI($login, $pass);

        if ( ! $api || is_string($api))
        {
            self::addError(JText::sprintf('CC_ERROR_CDN_COULD_NOT_INITIATE_API', JText::_('CC_CDN77')));

            if (is_string($api))
            {
                self::addError($api);
            }

            return false;
        }

        $ids = explode(',', $ids);

        foreach ($ids as $id)
        {
            $api_call = json_decode($api->purge($id));

            if ( ! is_null($api_call) && isset($api_call->status) && $api_call->status == 'ok')
            {
                continue;
            }

            self::addError(JText::sprintf('CC_ERROR_CDN_COULD_NOT_PURGE_ID', JText::_('CC_CDN77'), $id));

            if ( ! empty($api_call->description))
            {
                self::addError(JText::_('CC_CDN77') . ' Error: ' . $api_call->description);
            }

            return false;
        }

        if ( ! empty($api_call->description))
        {
            self::setMessage($api_call->description);
        }

        return true;
    }
}
