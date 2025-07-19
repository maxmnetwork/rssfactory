<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class AdModel extends AdminModel
{
    public $option = 'com_rssfactory';

    public function __construct($config = array())
    {
        $config['event_after_save'] = 'onRssFactoryProAdAfterSave';
        $config['event_after_delete'] = 'onRssFactoryProAdAfterDelete';

        parent::__construct($config);

        Factory::getApplication()->registerEvent($this->event_after_save, $this->event_after_save);
        Factory::getApplication()->registerEvent($this->event_after_delete, $this->event_after_delete);
    }

    public function getTable($type = 'Ad', $prefix = 'RssFactoryTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

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

        return $form ? $form : false;
    }

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        $properties = $item->getProperties(1);
        $item = ArrayHelper::toObject($properties, 'JObject');

        if (property_exists($item, 'categories_assigned')) {
            $registry = new Registry;
            $registry->loadString($item->categories_assigned);
            $item->categories_assigned = $registry->toArray();
        }

        return $item;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $context = $this->option . '.edit.' . $this->getName();
        $data = $app->getUserState($context . '.data', array());

        return empty($data) ? $this->getItem() : $data;
    }

    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        parent::preprocessForm($form, $data, $group);
        $formName = str_replace('.', '_', $form->getName());

        foreach ($form->getFieldsets() as $fieldset) {
            foreach ($form->getFieldset($fieldset->name) as $field) {
                $fieldName = ($field->group ? $field->group . '_' : '') . $field->fieldname;
                $label = $form->getFieldAttribute($field->fieldname, 'label', '', $field->group) ?: Text::_(strtoupper($formName . '_form_field_' . $fieldName . '_label'));
                $form->setFieldAttribute($field->fieldname, 'label', $label, $field->group);

                $desc = $form->getFieldAttribute($field->fieldname, 'description', '', $field->group) ?: Text::_(strtoupper($formName . '_form_field_' . $fieldName . '_desc'));
                $form->setFieldAttribute($field->fieldname, 'description', $desc, $field->group);
            }
        }
    }
}

function onRssFactoryProAdAfterDelete($event, $table = null)
{
    if ($event instanceof \Joomla\Event\Event) {
        $arguments = $event->getArguments();
        $table = $arguments[1];
    }

    $dbo = \Joomla\CMS\Factory::getDbo();
    $query = $dbo->getQuery(true)
        ->delete()
        ->from('#__rssfactory_ad_category_map')
        ->where('adId = ' . $dbo->quote($table->id));
    $dbo->setQuery($query)
        ->execute();

    return true;
}

function onRssFactoryProAdAfterSave($event, $table = null)
{
    if ($event instanceof \Joomla\Event\Event) {
        $arguments = $event->getArguments();
        $table = $arguments[1];
    }

    $categories = new Registry($table->categories_assigned);
    $categories = $categories->toArray();
    $dbo = \Joomla\CMS\Factory::getDbo();

    ArrayHelper::toInteger($categories);

    $query = $dbo->getQuery(true)
        ->delete()
        ->from('#__rssfactory_ad_category_map')
        ->where('adId = ' . $dbo->quote($table->id));
    $dbo->setQuery($query)
        ->execute();

    if (!$categories) {
        return true;
    }

    foreach ($categories as $category) {
        if (!$category) continue;

        $map = Table::getInstance('AdCategoryMap', 'RssFactoryTable');
        $map->save(['adId' => $table->id, 'categoryId' => $category]);
    }

    return true;
}
