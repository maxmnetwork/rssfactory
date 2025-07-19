<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

/**
 * RSS Factory - Loader Helper
 *
 * @since  4.3.6
 * @package Joomla.Component.Rssfactory
 */
class Loader
{
    // Define constants only once.
    public static function defineConstants(): void
    {
        if (!defined('RSS_FACTORY_COMPONENT_NAME')) {
            define('RSS_FACTORY_COMPONENT_NAME', 'rssfactory');
            define('RSS_FACTORY_COMPONENT_PATH', JPATH_ROOT . '/components/com_' . RSS_FACTORY_COMPONENT_NAME);
            define('RSS_FACTORY_COMPONENT_ADMIN_PATH', JPATH_ROOT . '/administrator/components/com_' . RSS_FACTORY_COMPONENT_NAME);
            define('RSS_FACTORY_COMPONENT_URI', Uri::root() . 'components/com_' . RSS_FACTORY_COMPONENT_NAME . '/');
            define('RSS_FACTORY_COMPONENT_ADMIN_URI', Uri::root() . 'administrator/components/com_' . RSS_FACTORY_COMPONENT_NAME . '/');
            define('RSS_FACTORY_XAJAX_PATH', RSS_FACTORY_COMPONENT_PATH . '/xajax');
            define('RSS_FACTORY_CLASSNAME', 'RFPROController');
            define('RSS_FACTORY_ADMIN_CLASSNAME', 'RFPROAdminController');
            define('RSS_FACTORY_TMP_PATH', JPATH_ROOT . '/administrator/components/com_' . RSS_FACTORY_COMPONENT_NAME . '/tmp');
            define('RSS_FACTORY_LAYOUTS_PATH', RSS_FACTORY_COMPONENT_PATH . '/layouts');
            define('RSS_FACTORY_SITE_SAFE_MODE_ON', false);
        }

        // Redundant definitions removed, retained only once.
        define('RSS_FACTORY_XAJAX_PATH', RSS_FACTORY_COMPONENT_PATH . '/xajax');
        define('RSS_FACTORY_CLASSNAME', 'RFPROController');
        define('RSS_FACTORY_ADMIN_CLASSNAME', 'RFPROAdminController');
        define('RSS_FACTORY_TMP_PATH', JPATH_ROOT . '/administrator/components/com_' . RSS_FACTORY_COMPONENT_NAME . '/tmp');
        define('RSS_FACTORY_LAYOUTS_PATH', RSS_FACTORY_COMPONENT_PATH . '/layouts');
        define('RSS_FACTORY_SITE_SAFE_MODE_ON', false);
    }
}
