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

use Joomla\CMS\Form\Field\RadioField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldType('Radio');

class FactoryBooleanField extends RadioField
{
    protected $type = 'FactoryBoolean';

    /**
     * Setup the field properties
     *
     * @param   \SimpleXMLElement  $element  The XML element representing the field
     * @param   mixed              $value    The current field value
     * @param   string|null        $group    The group name (optional)
     *
     * @return  self
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $element['class'] = 'switcher btn-group';
        $element['filter'] = 'integer';

        return parent::setup($element, $value, $group);
    }

    /**
     * Get the options for the radio field.
     *
     * @return  array  The options to be displayed in the field
     */
    protected function getOptions(): array
    {
        $options = parent::getOptions();

        if ($options) {
            return $options;
        }

        // Default options for the boolean field
        $options = [
            (object)[
                'value' => 0,
                'text'  => Text::_('JNO')
            ],
            (object)[
                'value' => 1,
                'text'  => Text::_('JYES')
            ]
        ];

        // If the 'global' attribute is set to 'true', add the "Use Global" option
        if ('true' === (string) $this->element['global']) {
            array_unshift($options, (object)[
                'value' => -1,
                'text'  => Text::_('JGLOBAL_USE_GLOBAL')
            ]);
        }

        return $options;
    }
}
