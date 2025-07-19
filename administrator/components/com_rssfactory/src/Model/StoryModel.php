<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 */

namespace Joomla\Component\Rssfactory\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Form\Form;

class StoryModel extends AdminModel
{
    protected $option = 'com_rssfactory';

    /**
     * Returns a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  \Joomla\CMS\Table\Table  A database object
     */
    public function getTable($type = 'Story', $prefix = 'Table', $config = []): Table
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|boolean  A Form object on success, false on failure
     */
    public function getForm($data = [], $loadData = true): Form|bool
    {
        $form = $this->loadForm(
            $this->option . '.story',
            'story',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        return $form ?: false;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData(): mixed
    {
        $app = Factory::getApplication();
        $data = $app->getUserState($this->option . '.edit.story.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null): mixed
    {
        $item = parent::getItem($pk);

        // Example: sanitize output
        if ($item && isset($item->title)) {
            $item->title = OutputFilter::stringURLSafe($item->title);
        }

        return $item;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param   \Joomla\CMS\Table\Table  $table  The Table object
     *
     * @return  void
     */
    protected function prepareTable($table): void
    {
        // Set ordering if new
        if (empty($table->id)) {
            if (!isset($table->ordering)) {
                $db = $this->getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__rssfactory_stories');
                $max = (int) $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, false otherwise.
     */
    public function save($data): bool
    {
        // Custom pre-save logic
        if (isset($data['title'])) {
            $data['title'] = trim($data['title']);
        }

        return parent::save($data);
    }

    /**
     * Method to delete one or more records.
     *
     * @param   array  &$pks  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     */
    public function delete(&$pks): bool
    {
        return parent::delete($pks);
    }
}
