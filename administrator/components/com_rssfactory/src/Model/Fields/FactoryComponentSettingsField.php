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
use Joomla\Component\Rssfactory\Administrator\Model\AboutModel;

class FactoryComponentSettingsField extends FormField
{
    protected $type = 'FactoryComponentSettings';

    /**
     * Method to get the input markup for this field.
     *
     * @return string The field input HTML.
     */
    protected function getInput(): string
    {
        // Get the output from the helper method based on the field option
        return $this->getOutput((string) $this->element['option']);
    }

    /**
     * Method to return the output based on the given option.
     *
     * @param string $option The option to determine what value to retrieve.
     * @return string The formatted output.
     */
    protected function getOutput(string $option): string
    {
        $output = [];

        // Instantiate the AboutModel to retrieve version information
        $model = new AboutModel();

        // Switch based on the option provided
        switch ($option) {
            case 'current_version':
                // Get and display the current version
                $output[] = $model->getCurrentVersion();
                break;

            case 'latest_version':
                // Get and display the latest version
                $output[] = $model->getLatestVersion();
                break;

            default:
                // If option is not matched, return an empty string or a default message
                $output[] = 'Unknown option';
                break;
        }

        return implode("\n", $output);
    }
}
