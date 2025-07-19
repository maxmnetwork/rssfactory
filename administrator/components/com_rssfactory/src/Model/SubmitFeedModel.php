<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 */

namespace Joomla\Component\Rssfactory\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Form\Form;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseTableInterface;

class SubmitFeedModel extends AdminModel
{
    /**
     * The component option name.
     *
     * @var    string
     */
    protected string $option = 'com_rssfactory';

    /**
     * Returns a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  DatabaseTableInterface
     */
    public function getTable($type = 'Feed', $prefix = 'Administrator', $config = []): DatabaseTableInterface
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  Form|false  A Form object on success, false on failure
     */
    public function getForm($data = [], $loadData = true): Form|false
    {
        $form = $this->loadForm(
            $this->option . '.submitfeed',
            'submitfeed',
            [
                'control'   => 'jform',
                'load_data' => $loadData
            ]
        );

        return $form ?: false;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  array  The default data is an empty array.
     */
    protected function loadFormData(): array
    {
        $app = Factory::getApplication();
        $data = $app->getUserState(
            $this->option . '.edit.submitfeed.data',
            []
        );

        if (empty($data)) {
            $item = $this->getItem();
            $data = $item ? (array) $item : [];
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer|null  $pk  The id of the primary key.
     *
     * @return  object|false  Object on success, false on failure.
     */
    public function getItem($pk = null): object|false
    {
        return parent::getItem($pk);
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param   DatabaseTableInterface  $table
     *
     * @return  void
     */
    protected function prepareTable(DatabaseTableInterface $table): void
    {
        // Add custom table preparation logic here if needed
    }
}
