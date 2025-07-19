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
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryHtmlRss;

class FactoryAjaxLinkField extends FormField
{
    protected $type = 'FactoryAjaxLink';

    /**
     * Get the label for the field.
     *
     * @return  string  The label
     */
    protected function getLabel(): string
    {
        return ''; // No label is rendered for this field
    }

    /**
     * Get the input field for the form.
     *
     * @return  string  The HTML markup for the input field
     */
    protected function getInput(): string
    {
        // Ensure the necessary JavaScript is loaded
        FactoryHtmlRss::script('admin/fields/factoryajaxlink');

        $output = [];

        // Get the option and task for the AJAX request
        $option = Factory::getApplication()->input->getCmd('option', '');
        $url = 'index.php?option=' . $option . '&task=' . $this->element['task'] . '&format=json';
        $update = $this->element['update'];

        // Create the button with data attributes for the AJAX request
        $output[] = '<input type="button" id="' . $this->id . '" data-update="' . $update . '" data-url="' . $url . '" value="' . $this->element['label'] . '" class="btn btn-small btn-primary factory-ajax-link">';

        // Return the HTML output
        return implode("\n", $output);
    }
}
