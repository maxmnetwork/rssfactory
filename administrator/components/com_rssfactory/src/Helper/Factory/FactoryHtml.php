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

use Joomla\CMS\HTML\HTMLHelper;

class FactoryHtml
{
    /**
     * @var string
     */
    protected static string $option = 'com_rssfactory';

    /**
     * Include a JavaScript file.
     *
     * @param string $file
     * @param bool $framework
     * @param bool $relative
     * @param bool $path_only
     * @param bool $detect_browser
     * @param bool $detect_debug
     */
    public static function script(
        string $file,
        bool $framework = false,
        bool $relative = false,
        bool $path_only = false,
        bool $detect_browser = true,
        bool $detect_debug = true
    ): void {
        $file = self::parsePath($file);
        HTMLHelper::script($file, $framework, $relative, $path_only, $detect_browser, $detect_debug);
    }

    /**
     * Include a CSS file.
     *
     * @param string $file
     * @param array $attribs
     * @param bool $relative
     * @param bool $path_only
     * @param bool $detect_browser
     * @param bool $detect_debug
     */
    public static function stylesheet(
        string $file,
        array $attribs = [],
        bool $relative = false,
        bool $path_only = false,
        bool $detect_browser = true,
        bool $detect_debug = true
    ): void {
        $file = self::parsePath($file, 'css');
        HTMLHelper::stylesheet($file, $attribs, $relative, $path_only, $detect_browser, $detect_debug);
    }

    /**
     * Register HTML (no longer needed for Joomla 4 as it uses PSR-4 autoloading).
     *
     * @param string $html
     * @return bool
     */
    public static function registerHtml(string $html): bool
    {
        // Joomla 4: No need to register classes, use PSR-4 autoloading and namespaces.
        // This method is now a no-op for compatibility.
        return true;
    }

    /**
     * Parse the file path for assets (JS or CSS).
     *
     * @param string $file
     * @param string $type
     * @return string
     */
    protected static function parsePath(string $file, string $type = 'js'): string
    {
        $path = [];
        $parts = explode('/', $file);

        $path[] = 'media';
        $path[] = self::$option;
        $path[] = 'assets';

        // Determine frontend or backend
        if ('admin' == $parts[0]) {
            $path[] = 'backend';
            unset($parts[0]);
            $parts = array_values($parts);
        } else {
            $path[] = 'frontend';
        }

        $path[] = $type;

        // Append the file parts
        $count = count($parts);
        foreach ($parts as $i => $part) {
            if ($i + 1 == $count) {
                $path[] = $part . '.' . $type;
            } else {
                $path[] = $part;
            }
        }

        return implode('/', $path);
    }
}
