<?php

/**
-------------------------------------------------------------------------
rssfactory - Rss Factory 4.3.6
-------------------------------------------------------------------------
 * @author thePHPfactory
 * @copyright Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.thePHPfactory.com
 * Technical Support: Forum - http://www.thePHPfactory.com/forum/
-------------------------------------------------------------------------
*/

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Helper\Html;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

class RssFactoryHtml
{
    /**
     * Displays the dropdown menu for items based on Joomla version.
     *
     * @param array $options Options for the dropdown
     * @return string HTML of the dropdown menu
     */
    public static function itemDropDown(array $options = []): string
    {
        if (3 === (int)\Joomla\CMS\Version::MAJOR_VERSION) {
            return self::itemDropDown3($options);
        }

        return self::itemDropDown4($options);
    }

    /**
     * Generates the dropdown for Joomla 4.
     *
     * @param array $options Options for the dropdown
     * @return string Placeholder for Joomla 4 dropdown
     */
    private static function itemDropDown4(array $options = []): string
    {
        // Joomla 4: Bootstrap 5 dropdowns or custom implementation as needed.
        // Currently, returning empty string as a placeholder.
        return '';
    }

    /**
     * Generates the dropdown for Joomla 3 (using legacy dropdown methods).
     *
     * @param array $options Options for the dropdown
     * @return string HTML for the dropdown menu
     */
    private static function itemDropDown3(array $options = []): string
    {
        foreach ($options as $option => $params) {
            switch ($option) {
                case 'edit':
                    HTMLHelper::_('dropdown.edit', $params['id'], $params['prefix'] . '.');
                    break;

                case 'publish':
                    $method = $params['published'] ? 'unpublish' : 'publish';
                    HTMLHelper::_('dropdown.' . $method, 'cb' . $params['i'], $params['prefix'] . '.');
                    break;

                case 'divider':
                    HTMLHelper::_('dropdown.divider');
                    break;

                case 'refresh':
                    $task = $params['prefix'] . '.refresh';
                    HTMLHelper::_('dropdown.addCustomItem', FactoryTextRss::_('feeds_list_feed_refresh'), 'javascript:void(0)', 'onclick="contextAction(\'cb' . $params['i'] . '\', \'' . $task . '\')"');
                    break;

                case 'clearcache':
                    $task = $params['prefix'] . '.clearcache';
                    HTMLHelper::_('dropdown.addCustomItem', FactoryTextRss::_('feeds_list_feed_clear_cache'), 'javascript:void(0)', 'onclick="contextAction(\'cb' . $params['i'] . '\', \'' . $task . '\')"');
                    break;
            }
        }

        // Joomla 4: Use HTMLHelper for dropdown rendering instead of JHtmlDropdown
        return HTMLHelper::_('dropdown.render');
    }
}
