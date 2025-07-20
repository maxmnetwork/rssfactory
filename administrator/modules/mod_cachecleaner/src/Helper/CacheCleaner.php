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

namespace RegularLabs\Module\CacheCleaner\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\FileLayout as JFileLayout;
use Joomla\CMS\Toolbar\Toolbar as JToolbar;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;

class CacheCleaner
{
    static public function addScriptsAndStyles()
    {
        RL_Language::load('plg_system_cachecleaner');

        RL_Document::scriptOptions(
            [
                'message_clean'    => JText::_('CC_CLEANING_CACHE'),
                'message_inactive' => JText::sprintf(
                    'CC_SYSTEM_PLUGIN_NOT_ENABLED',
                    '<a href=&quot;index.php?option=com_plugins&filter[folder]=system&filter[search]=cache cleaner&quot;>',
                    '</a>'
                ),
                'message_failure'  => JText::_('CC_CACHE_COULD_NOT_BE_CLEANED'),
            ],
            'Cache Cleaner'
        );

        RL_Document::script('regularlabs.regular');
        RL_Document::script('cachecleaner.script');
        RL_Document::style('cachecleaner.style');
    }

    static public function addToolbarButton()
    {
        $params = RL_Parameters::getPlugin('cachecleaner');

        // Instantiate a new LayoutFile instance and render the layout
        $layout = new JFileLayout('joomla.toolbar.standard');
        $class  = 'btn'
            . ($params->add_button_text ? '' : ' rl-button-no-text')
            . ($params->button_classname ? ' ' . $params->button_classname : '');

        $button = [
            'text'           => self::getText(),
            'onclick'        => 'RegularLabs.CacheCleaner.purge();',
            'class'          => 'icon-trash',
            'btnClass'       => $class,
            'htmlAttributes' => 'type="button"',
        ];

        $toolbar = JToolBar::getInstance('toolbar');
        $toolbar->appendButton('Custom', $layout->render($button));
    }

    static public function getText()
    {
        $params = RL_Parameters::getPlugin('cachecleaner');

        if ( ! $params->add_button_text)
        {
            return '';
        }

        $text_ini = strtoupper(str_replace(' ', '_', $params->button_text));
        $text     = JText::_($text_ini);

        if ($text == $text_ini)
        {
            $text = JText::_($params->button_text);
        }

        return $text;
    }
}
