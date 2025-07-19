<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Access\Rules;
use Joomla\Registry\Registry;

class ConfigurationModel extends AdminModel
{
    protected $option = 'com_rssfactory';
    protected $fieldsets = null;

    /**
     * Get the form for the configuration.
     *
     * @param array $data
     * @param bool  $loadData
     * @return mixed
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Add form path
        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/' . $this->option);

        $form = $this->loadForm(
            $this->option . '.' . $this->getName(),
            $this->getName(),
            array(
                'control'   => 'jform',
                'load_data' => $loadData
            ),
            false,
            '/config'
        );

        if (empty($form)) {
            return false;
        }

        $form->loadFile($this->getName() . '.import2content', false, '/config');

        $data = $loadData ? $this->loadFormData() : array();
        $this->preprocessForm($form, $data);

        if ($loadData) {
            $form->bind($data);
        }

        $this->fieldsets = $form->getFieldsets();

        return $form;
    }

    /**
     * Save the configuration data.
     *
     * @param array $data
     * @return bool
     */
    public function save($data)
    {
        // Save the rules.
        if (isset($data['rules'])) {
            $rules = new Rules($data['rules']);
            $asset = Table::getInstance('Asset');

            if (!$asset->loadByName($this->option)) {
                $root = Table::getInstance('Asset');
                $root->loadByName('root.1');
                $asset->name = $this->option;
                $asset->title = $this->option;
                $asset->setLocation($root->id, 'last-child');
            }

            $asset->rules = (string)$rules;

            if (!$asset->check() || !$asset->store()) {
                return false;
            }

            unset($data['rules']);
        }

        // Save component settings.
        $extension = Table::getInstance('Extension');
        $id = $extension->find(['element' => $this->option, 'type' => 'component']);
        $settings = ComponentHelper::getParams($this->option);

        $extension->load($id);
        $extension->bind(['params' => array_merge($settings->toArray(), $data)]);

        if (!$extension->store()) {
            return false;
        }

        // Clean the component cache.
        $this->cleanCache('_system', 1);

        return true;
    }

    /**
     * Get the fieldsets from the form.
     *
     * @return mixed
     */
    public function getFieldsets()
    {
        return $this->fieldsets;
    }

    /**
     * Load form data.
     *
     * @return mixed
     */
    protected function loadFormData()
    {
        $result = ComponentHelper::getComponent($this->option);

        return $result->params;
    }

    /**
     * Preprocess the form before it is displayed.
     *
     * @param Form   $form
     * @param mixed  $data
     * @param string $group
     * @return void
     */
    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        parent::preprocessForm($form, $data, $group);

        $formName = str_replace('.', '_', $form->getName());

        foreach ($form->getFieldsets() as $fieldset) {
            foreach ($form->getFieldset($fieldset->name) as $field) {
                $fieldName = ($field->group ? $field->group . '_' : '') . $field->fieldname;

                $label = $form->getFieldAttribute($field->fieldname, 'label', '', $field->group);

                if ('' == $label) {
                    $label = Text::_(strtoupper($formName . '_form_field_' . $fieldName . '_label'));
                    $form->setFieldAttribute($field->fieldname, 'label', $label, $field->group);
                }

                $desc = $form->getFieldAttribute($field->fieldname, 'description', '', $field->group);

                if ('' == $desc) {
                    $desc = Text::_(strtoupper($formName . '_form_field_' . $fieldName . '_desc'));
                    $form->setFieldAttribute($field->fieldname, 'description', $desc, $field->group);
                }
            }
        }
    }
}
