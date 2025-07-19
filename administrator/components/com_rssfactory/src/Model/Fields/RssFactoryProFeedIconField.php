<?php

namespace Joomla\Component\Rssfactory\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\Component\Rssfactory\Administrator\Helper\Html\Feeds;

/**
 * Class RssFactoryProFeedIconField
 * Custom form field for displaying the feed icon in the Joomla 4/5 backend
 */
class RssFactoryProFeedIconField extends FormField
{
    /**
     * @var string The field type
     */
    protected $type = 'RssFactoryProIcon';

    /**
     * Method to generate the input for the field
     *
     * @return string HTML string
     */
    protected function getInput(): string
    {
        $output = [];

        // Wrap the icon in a div element with the field name as the id
        $output[] = '<div id="' . htmlspecialchars((string) $this->element['name'], ENT_QUOTES, 'UTF-8') . '">';

        // Use the Feeds helper to display the feed icon for the current feed ID
        $output[] = Feeds::icon($this->form->getValue('id'));

        $output[] = '</div>';

        return implode("\n", $output);
    }
}
