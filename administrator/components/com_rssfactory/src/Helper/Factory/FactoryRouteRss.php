<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Helper\Factory;

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

class FactoryRouteRss
{
    protected static string $option = 'com_rssfactory';

    /**
     * Build the URL for the RSS factory component.
     *
     * @param string $url The URL string to append to the base URL.
     * @param bool $xhtml If true, forces XHTML output.
     * @param bool|null $ssl SSL flag, null to detect automatically.
     * @return string The constructed URL.
     */
    public static function _($url = '', bool $xhtml = false, $ssl = null): string
    {
        $url = 'index.php?option=' . self::$option . ($url !== '' ? '&' . $url : '');
        return Route::_($url, $xhtml, $ssl);
    }

    /**
     * Build the URL for a specific view in the RSS factory component.
     *
     * @param string $view The view name.
     * @param bool $xhtml If true, forces XHTML output.
     * @param bool|null $ssl SSL flag, null to detect automatically.
     * @return string The constructed URL for the view.
     */
    public static function view(string $view, bool $xhtml = false, $ssl = null): string
    {
        $url = 'view=' . $view;
        return self::_($url, $xhtml, $ssl);
    }

    /**
     * Build the URL for a specific task in the RSS factory component.
     *
     * @param string $task The task name.
     * @param bool $xhtml If true, forces XHTML output.
     * @param bool|null $ssl SSL flag, null to detect automatically.
     * @return string The constructed URL for the task.
     */
    public static function task(string $task, bool $xhtml = false, $ssl = null): string
    {
        $url = 'task=' . $task;
        return self::_($url, $xhtml, $ssl);
    }
}
