<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

class FeedModel extends AdminModel
{
    protected $option = 'com_rssfactory';

    /**
     * Get the associated table instance.
     *
     * @param string $type
     * @param string $prefix
     * @param array  $config
     * @return \JTable
     */
    public function getTable($type = 'Feed', $prefix = 'RssFactoryTable', $config = [])
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Get the form object.
     *
     * @param array $data
     * @param bool  $loadData
     * @return Form|false
     */
    public function getForm($data = [], $loadData = true)
    {
        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/' . $this->option . '/models/forms');
        $form = $this->loadForm(
            $this->option . '.' . $this->getName(),
            $this->getName(),
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Load form data.
     *
     * @return mixed
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $context = $this->option . '.edit.' . $this->getName();
        $data = $app->getUserState($context . '.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Preprocess the form.
     *
     * @param Form  $form
     * @param mixed $data
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

    /**
     * Clear cache.
     *
     * @param mixed $pks
     * @return bool
     */
    public function clearCache($pks = null)
    {
        $pks = (array) $pks;
        $db = $this->getDbo();

        if (empty($pks)) {
            $this->setState('error', Text::_('COM_RSSFACTORY_NO_ITEM_SELECTED'));
            return false;
        }

        foreach ($pks as $pk) {
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__rssfactory_cache'))
                ->where($db->quoteName('rssid') . ' = ' . (int) $pk);
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (\Exception $e) {
                $this->setState('error', $e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Test FTP connection.
     *
     * @param array $data
     * @return bool
     */
    public function testFtp($data)
    {
        if (empty($data['host']) || empty($data['username'])) {
            $this->setState('error', Text::_('feed_task_testftp_error_invalid_data'));
            return false;
        }

        $ftp = \JClientFtp::getInstance($data['host'], 21);
        $contents = false;

        if (!$ftp->login($data['username'], $data['password'])) {
            $this->setState('error', Text::_('feed_task_testftp_error_invalid_credentials'));
            return false;
        }

        if (!$ftp->read($data['path'], $contents)) {
            $this->setState('error', Text::_('feed_task_testftp_error_invalid_path'));
            return false;
        }

        return true;
    }

    /**
     * Refresh feed icon.
     *
     * @param array $data
     * @return bool
     */
    public function refreshIcon($data)
    {
        if (empty($data['url']) || empty($data['id'])) {
            $this->setState('error', Text::_('feed_task_refreshicon_error_invalid_data'));
            return false;
        }

        \RssFactoryHelper::getSiteIcon($data['id'], $data['url']);
        $temp = HTMLHelper::_('feeds.icon', $data['id'], true);
        $this->setState('feed.icon', $temp);

        return true;
    }

    /**
     * Preview feed content.
     *
     * @param array $data
     * @return string
     */
    public function preview($data)
    {
        $url = urldecode($data['i2c_rules_preview_story']);
        $rules = $data['params']['i2c_rules'] ?? [];
        $debug = $data['preview_debug'] ?? 0;

        try {
            return \RssFactoryHelper::parseFullArticle($url, $rules, $debug);
        } catch (\Exception $e) {
            return '<div class="alert alert-danger"><h4>Error</h4>' . $e->getMessage() . '<br />Error code: ' . $e->getCode() . '</div>';
        }
    }

    /**
     * Move feed to a new category.
     *
     * @param array $pks
     * @param array $batch
     * @return bool
     */
    public function move($pks, $batch)
    {
        if (empty($pks)) {
            $this->setState('error', Text::_('feed_move_error_no_items_selected'));
            return false;
        }

        if (!is_array($batch) || empty($batch['category_id'])) {
            $this->setState('error', Text::_('feed_move_error_no_category_selected'));
            return false;
        }

        $dbo = $this->getDbo();
        $query = $dbo->getQuery(true)
            ->update('#__rssfactory')
            ->set('cat = ' . $dbo->quote($batch['category_id']))
            ->where('id IN (' . implode(',', $pks) . ')');
        $result = $dbo->setQuery($query)->execute();

        return $result;
    }
}

// Joomla 4 event handler for after feed delete
function onAfterFeedDelete($context, $table = null)
{
    if ($context instanceof \Joomla\Event\Event) {
        $arguments = $context->getArguments();
        $table = $arguments[1];
    }

    if ('com_rssfactory.feed' !== $context) {
        return;
    }

    $dbo = Factory::getDbo();
    $query = $dbo->getQuery(true)
        ->delete($dbo->quoteName('#__rssfactory_cache'))
        ->where($dbo->quoteName('rssid') . ' = ' . (int) $table->id);
    $dbo->setQuery($query)->execute();

    $query = $dbo->getQuery(true)
        ->delete('#__rssfactory_voting')
        ->where('rssid = ' . $dbo->quote($table->id));
    $dbo->setQuery($query)->execute();

    $query = $dbo->getQuery(true)
        ->delete('#__rssfactory_comments')
        ->where('rssid = ' . $dbo->quote($table->id));
    $dbo->setQuery($query)->execute();

    return true;
}
