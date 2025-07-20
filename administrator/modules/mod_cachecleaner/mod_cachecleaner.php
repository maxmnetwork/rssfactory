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

use Joomla\CMS\Helper\ModuleHelper as JModuleHelper;
use Joomla\CMS\Plugin\PluginHelper as JPluginHelper;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Module\CacheCleaner\Administrator\Helper\CacheCleaner as CacheCleanerHelper;

/**
 * Module that cleans cache
 */

// return if Regular Labs Library plugin is not installed
if ( ! is_file(JPATH_PLUGINS . '/system/regularlabs/regularlabs.xml'))
{
    return;
}

if ( ! RL_Document::isAdmin(true))
{
    return;
}

if ( ! RL_Document::isJoomlaVersion(4))
{
    return;
}

// return if Regular Labs Library plugin is not enabled
if ( ! JPluginHelper::isEnabled('system', 'regularlabs'))
{
    return;
}

// return if Cache Cleaner system plugin is not enabled
if ( ! JPluginHelper::isEnabled('system', 'cachecleaner'))
{
    return;
}

if (true)
{
    $params = RL_Parameters::getPlugin('cachecleaner');

    if ( ! $params->display_statusbar_button && ! ! $params->display_toolbar_button)
    {
        return;
    }

    CacheCleanerHelper::addScriptsAndStyles();

    if ($params->display_toolbar_button)
    {
        CacheCleanerHelper::addToolbarButton();
    }

    if ($params->display_statusbar_button)
    {
        require JModuleHelper::getLayoutPath('mod_cachecleaner');
    }
}
