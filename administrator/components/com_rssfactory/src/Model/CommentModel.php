<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class CommentModel extends AdminModel
{
    protected $option = 'com_rssfactory';

    /**
     * Get the table instance for the Comment model
     *
     * @param string $type The table type (Comment by default)
     * @param string $prefix The table prefix (RssFactoryTable by default)
     * @param array $config Configuration options
     * 
     * @return \Joomla\CMS\Table\Table
     */
    public function getTable($type = 'Comment', $prefix = 'RssFactoryTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Get the form instance for the Comment model
     *
     * @param array $data Data to populate the form
     * @param bool $loadData Whether to load the data into the form
     * 
     * @return \Joomla\CMS\Form\Form|false
     */
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            $this->option . '.' . $this->getName(),
            $this->getName(),
            array(
                'control'   => 'jform',
                'load_data' => $loadData,
            ),
            false,
            '/form'
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Load form data from the session or the model item
     *
     * @return mixed
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $context = $this->option . '.edit.' . $this->getName();
        $data = $app->getUserState($context . '.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Preprocess the form before rendering, setting labels and descriptions
     *
     * @param Form $form The form object
     * @param mixed $data The data to populate the form
     * @param string $group The form group
     * 
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
