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

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;

class SubmittedFeedModel extends AdminModel
{
    /**
     * Returns a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  Table  A database object
     */
    public function getTable($type = 'SubmittedFeed', $prefix = 'RssFactoryTable', $config = []): Table
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  void
     */
    public function getForm($data = [], $loadData = true)
    {
        // This method is not fully implemented, add any necessary form handling if required.
    }

    /**
     * Method for batch processing of selected items.
     *
     * @param   array   $commands  The batch commands.
     * @param   array   $pks       Primary keys of items.
     * @param   array   $contexts  The context data.
     *
     * @return  bool  True on success, false on failure.
     */
    public function batch($commands, $pks, $contexts): bool
    {
        $pks = array_unique($pks);
        ArrayHelper::toInteger($pks);

        // Remove invalid pk (0 value)
        if (array_search(0, $pks, true)) {
            unset($pks[array_search(0, $pks, true)]);
        }

        if (empty($pks)) {
            $this->setState('error', Text::_('JGLOBAL_NO_ITEM_SELECTED'));
            return false;
        }

        // Ensure a category ID is set for batch operations.
        $categoryId = $commands['category_id'];
        if (!$categoryId) {
            $this->setState('error', Text::_('COM_RSSFACTORY_SUBMITTEDFEEDS_BATCH_PUBLISH_ERROR_CATEGORY_NOT_SET'));
            return false;
        }

        foreach ($pks as $id) {
            $table = $this->getTable();

            // Load the submitted feed item.
            if (!$table->load($id)) {
                continue;
            }

            // Prepare the data for saving to the Feed table.
            $feed = $this->getTable('Feed');
            $data = [
                'userid'    => $table->userid,
                'url'       => $table->url,
                'published' => 1,
                'date'      => $table->date,
                'cat'       => $categoryId,
                'title'     => $table->title,
            ];

            // Save the feed and delete the submitted feed entry.
            if ($feed->save($data)) {
                $table->delete();
            }
        }

        return true;
    }
}
