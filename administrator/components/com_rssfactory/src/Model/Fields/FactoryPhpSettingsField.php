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
use Joomla\CMS\Language\Text;

class FactoryPhpSettingsField extends FormField
{
    protected $type = 'FactoryPhpSettings';

    /**
     * Get the field input.
     *
     * @return string The HTML output for the field input.
     */
    protected function getInput(): string
    {
        $output = $this->getOutput($this->element['option']);
        return $output;
    }

    /**
     * Get the label for the field.
     *
     * @return string The label HTML or an empty string.
     */
    protected function getLabel(): string
    {
        if ((string) $this->element['hasLabel'] === 'false') {
            return '';
        }

        return parent::getLabel();
    }

    /**
     * Get the output based on the selected option.
     *
     * @param string $option The option for the PHP setting to be displayed.
     * @return string The HTML output for the selected PHP setting.
     */
    protected function getOutput(string $option): string
    {
        $output = [];

        switch ($option) {
            case 'version':
                $output[] = '<img src="' . $_SERVER['PHP_SELF'] . '?=' . (function_exists('php_logo_guid') ? php_logo_guid() : '') . '" alt="PHP Logo !" />';
                $output[] = '<br />';
                $output[] = php_uname();
                break;

            case 'display_errors':
                $output[] = Text::_(ini_get('display_errors') ? 'JYES' : 'JNO');
                break;

            case 'file_uploads':
                $max_upload = (int) ini_get('upload_max_filesize');
                $max_post = (int) ini_get('post_max_size');
                $memory_limit = (int) ini_get('memory_limit');

                $output[] = min($max_upload, $max_post, $memory_limit) . 'MB';
                break;

            case 'curl_support':
                $output[] = Text::_(function_exists('curl_init') ? 'JYES' : 'JNO');
                break;

            case 'gmt_time':
                $output[] = gmdate('Y-m-d H:i:s');
                break;
        }

        return implode("\n", $output);
    }
}
