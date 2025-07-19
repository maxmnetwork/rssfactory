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

namespace Joomla\Component\Rssfactory\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

class FactoryJoomlaSettingsField extends FormField
{
    protected $type = 'FactoryJoomlaSettings';

    /**
     * Get the input HTML for this field.
     *
     * @return string The field's input HTML.
     */
    protected function getInput(): string
    {
        return $this->getOutput((string) $this->element['option']);
    }

    /**
     * Get the output based on the provided option.
     *
     * @param string $option The setting to retrieve.
     *
     * @return string The HTML output for the setting.
     */
    protected function getOutput(string $option): string
    {
        $output = [];
        $app = Factory::getApplication();

        switch ($option) {
            case 'error_reporting':
                $output[] = (string) $app->get('error_reporting');
                break;

            case 'locale_time':
                $output[] = (string) $app->get('offset');
                break;
        }

        return implode("\n", $output);
    }
}
