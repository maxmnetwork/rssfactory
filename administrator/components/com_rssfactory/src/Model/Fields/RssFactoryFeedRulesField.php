<?php

namespace Joomla\Component\Rssfactory\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;
use Joomla\Component\Rssfactory\Administrator\Helper\RssFactoryRules; // Assuming a helper for rules

/**
 * RssFactoryFeedRulesField Class
 *
 * Custom form field to handle RSS feed rules
 */
class RssFactoryFeedRulesField extends FormField
{
    /**
     * @var string The field type
     */
    protected $type = 'RssFactoryFeedRules';

    /**
     * Get the label for the field
     *
     * @return string The field label or empty string if disabled
     */
    protected function getLabel(): string
    {
        if ('false' == (string) $this->element['hasLabel']) {
            return '';
        }

        return parent::getLabel();
    }

    /**
     * Get the input for the field
     *
     * @return string The HTML input for the field
     */
    protected function getInput(): string
    {
        // J4: Use Composer autoload/namespace for rules
        $html = [];

        $html[] = '<div>';
        $html[] = HTMLHelper::_('select.genericlist', RssFactoryRules::options(), 'rules', ['class' => 'custom-select']);
        $html[] = '<a href="#" class="btn btn-small btn-success button-add-rule" style="vertical-align: top;">'
            . '<i class="icon-new"></i>&nbsp;' . FactoryTextRss::_('rule_add_new') . '</a>';
        $html[] = '</div>';

        $html[] = '<div class="rules">';

        $last = 0;
        if ($this->value) {
            foreach ($this->value as $value) {
                // Assuming RssFactoryRule::getInstance is a valid method that exists in your project
                $rule = \RssFactoryRule::getInstance($value['type']);
                $html[] = $rule->getTemplate($value['order'], $value, $this->getName($this->fieldname));

                $last = $value['order'];
            }
        }

        $html[] = '</div>';

        $html[] = '<div class="rules-templates" data-last="' . $last . '" '
            . RssFactoryRules::templates() . '></div>';

        return implode("\n", $html);
    }
}
