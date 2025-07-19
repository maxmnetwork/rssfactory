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

use Joomla\CMS\Form\Field\RulesField;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldType('Rules');

/**
 * Custom Factory Rules Form Field for Joomla 4
 */
class FactoryRulesField extends RulesField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    public $type = 'FactoryRules';

    /**
     * Get the input markup for this field.
     *
     * @return  string  The field input markup.
     */
    protected function getInput(): string
    {
        $input = parent::getInput();

        // Remove any legacy JS event handlers if present
        $input = str_replace('onchange="sendPermissions.call(this, event)"', '', $input);

        return $input;
    }
}
