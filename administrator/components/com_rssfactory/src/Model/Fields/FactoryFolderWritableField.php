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
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

class FactoryFolderWritableField extends FormField
{
    protected $type = 'FactoryFolderWritable';

    /**
     * Method to get the input markup for this field.
     *
     * @return string The field input HTML.
     */
    protected function getInput(): string
    {
        // Get the folder path from the field element
        $folder = JPATH_COMPONENT_SITE . '/' . (string) $this->element['folder'];
        
        // Check if the folder is writable
        $isWritable = (int) is_writable($folder);

        // Return the localized output for writable status
        return FactoryTextRss::plural('field_factory_folder_writable_status', $isWritable, $folder);
    }
}
