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

namespace Joomla\Component\Rssfactory\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

class SubmittedFeedTable extends Table
{
    /**
     * Constructor.
     *
     * @param   \JDatabaseDriver $db  Database driver object
     */
    public function __construct(\JDatabaseDriver $db)
    {
        parent::__construct('#__rssfactory_submitted', 'id', $db);
    }

    /**
     * Method to check the data before saving.
     *
     * @return  bool  True on success, false on failure
     */
    public function check()
    {
        // Call the parent check method first
        if (!parent::check()) {
            return false;
        }

        // Set default values for missing fields
        if (is_null($this->date)) {
            $this->date = Factory::getDate()->toSql();
        }

        if (is_null($this->userid)) {
            $this->userid = Factory::getApplication()->getIdentity()->id;
        }

        return true;
    }
}
