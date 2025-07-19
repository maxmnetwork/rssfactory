<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class CategoriesModel extends ListModel
{
    protected $option = 'com_rssfactory';

    /**
     * Get the query to load the list of categories
     *
     * @return \JDatabaseQuery
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('c.*')
            ->from($db->quoteName('#__categories', 'c'))
            ->where('c.extension = ' . $db->quote('com_rssfactory'))
            ->order('c.title ASC');

        // Add filtering if needed
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $query->where('c.title LIKE ' . $db->quote('%' . $search . '%'));
        }

        return $query;
    }
}
