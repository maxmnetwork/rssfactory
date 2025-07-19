<?php
// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;

class VoteTable extends Table
{
    /**
     * Constructor for the VoteTable class.
     *
     * @param   \Joomla\Database\DatabaseDriver  $db  Database driver object.
     */
    public function __construct($db)
    {
        // It's a good practice to inject the database via the constructor for DI.
        parent::__construct('#__rssfactory_voting', 'id', $db);
    }
}
