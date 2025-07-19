<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Factory;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Helper class for static language text retrieval used in RSS Factory admin UI
 * 
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @since       4.0.0
 */
final class FactoryTextRss
{
    public static function configurationTabGeneral(): string
    {
        return Text::_('COM_RSSFACTORY_CONFIGURATION_TAB_GENERAL');
    }

    public static function configurationTabDisplay(): string
    {
        return Text::_('COM_RSSFACTORY_CONFIGURATION_TAB_DISPLAY');
    }

    public static function configurationTabRefresh(): string
    {
        return Text::_('COM_RSSFACTORY_CONFIGURATION_TAB_REFRESH');
    }

    public static function configurationTabCron(): string
    {
        return Text::_('COM_RSSFACTORY_CONFIGURATION_TAB_CRON');
    }

    public static function configurationTabImport2Content(): string
    {
        return Text::_('COM_RSSFACTORY_CONFIGURATION_TAB_IMPORT2CONTENT');
    }

    public static function configurationTabPermissions(): string
    {
        return Text::_('COM_RSSFACTORY_CONFIGURATION_TAB_PERMISSIONS');
    }

    public static function configurationTabSystemInfo(): string
    {
        return Text::_('COM_RSSFACTORY_CONFIGURATION_TAB_SYSTEMINFO');
    }

    public static function featureAvailableInProVersion(): string
    {
        return Text::_('COM_RSSFACTORY_FEATURE_AVAILABLE_IN_PRO_VERSION');
    }

    public static function proVersionNotice(): string
    {
        return Text::_('COM_RSSFACTORY_PRO_VERSION_NOTICE');
    }
}
