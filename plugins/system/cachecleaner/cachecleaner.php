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

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Extension as RL_Extension;
use RegularLabs\Library\SystemPlugin as RL_SystemPlugin;
use RegularLabs\Plugin\System\CacheCleaner\Cache;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/regularlabs.xml')
    || ! class_exists('RegularLabs\Library\Parameters')
    || ! class_exists('RegularLabs\Library\DownloadKey')
    || ! class_exists('RegularLabs\Library\SystemPlugin')
)
{
    JFactory::getApplication()->getLanguage()->load('plg_system_cachecleaner', __DIR__);
    JFactory::getApplication()->enqueueMessage(
        JText::sprintf('AA_EXTENSION_CAN_NOT_FUNCTION', JText::_('CACHECLEANER'))
        . ' ' . JText::_('AA_REGULAR_LABS_LIBRARY_NOT_INSTALLED'),
        'error'
    );

    return;
}

if ( ! RL_Document::isJoomlaVersion(4, 'CACHECLEANER'))
{
    RL_Extension::disable('cachecleaner', 'plugin');

    RL_Document::adminError(
        JText::sprintf('RL_PLUGIN_HAS_BEEN_DISABLED', JText::_('CACHECLEANER'))
    );

    return;
}

if (true)
{
    class PlgSystemCacheCleaner extends RL_SystemPlugin
    {
        public $_lang_prefix     = 'CC';
        public $_page_types      = ['html', 'ajax', 'json', 'raw'];
        public $_enable_in_admin = true;
        public $_jversion        = 4;

        public function handleOnAfterRoute()
        {
            Cache::clean($this->_id);
        }

        protected function changeFinalHtmlOutput(&$html)
        {
            return true;
        }
    }
}
