<?php

/**
 * @package         Regular Labs Library
 * @version         24.6.11852
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */
namespace RegularLabs\Library;

defined('_JEXEC') or die;
use JLoader;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin as JFieldsPlugin;
class FieldsPlugin extends JFieldsPlugin
{
    public function __construct(&$subject, $config = [])
    {
        parent::__construct($subject, $config);
        $path = JPATH_PLUGINS . '/fields/' . $this->_name . '/src/Form/Field';
        if (!file_exists($path)) {
            return;
        }
        $name = str_replace('PlgFields', '', $this::class);
        JLoader::registerAlias('JFormField' . $name, '\RegularLabs\Plugin\Fields\\' . $name . '\Form\Field\\' . $name . 'Field');
    }
}
