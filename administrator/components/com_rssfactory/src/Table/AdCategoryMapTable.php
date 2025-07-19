<?php
// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class AdCategoryMapTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__rssfactory_ad_category_map', 'id', $db);
    }
}
