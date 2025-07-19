<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Text;

class CommentsModel extends ListModel
{
    protected $option = 'com_rssfactory';
    protected $tableAlias = 'c';
    protected $filters = array('published');
    protected $defaultOrdering = 'created_at';
    protected $defaultDirection = 'desc';

    /**
     * Get the list of sort fields for comments
     *
     * @return array
     */
    public function getSortFields()
    {
        return array(
            $this->tableAlias . '.text'       => \Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss::_('comments_list_text'),
            'cache.item_title'                => \Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss::_('comments_list_story'),
            'u.username'                      => \Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss::_('comments_list_username'),
            $this->tableAlias . '.created_at' => \Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss::_('comments_list_created_at'),
            $this->tableAlias . '.published'  => Text::_('JSTATUS'),
            $this->tableAlias . '.id'         => Text::_('JGRID_HEADING_ID'),
        );
    }

    /**
     * Get the list of comment items
     *
     * @return array
     */
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * Get the pagination object for the list
     *
     * @return mixed
     */
    public function getPagination()
    {
        return $this->getPaginationObject();
    }

    /**
     * Generate the query for retrieving the list of comments
     *
     * @return \Joomla\Database\DatabaseQuery
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('c.*')
            ->from($db->quoteName('#__rssfactory_comments', 'c'));

        // Select the username.
        $query->select('u.username')
            ->leftJoin('#__users u ON u.id = c.user_id');

        // Select the story.
        $query->select('cache.item_title')
            ->leftJoin('#__rssfactory_cache cache ON cache.id = c.item_id');

        $this->addFilterSearch($query);
        $this->addFilterPublished($query);
        $this->addOrderResults($query);

        return $query;
    }

    /**
     * Add search filter to the query
     *
     * @param \Joomla\Database\DatabaseQuery $query The query object
     * @return void
     */
    protected function addFilterSearch(&$query)
    {
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where($this->tableAlias . '.id = ' . (int)substr($search, 3));
            } else {
                $search = $query->quote('%' . $query->escape($search, true) . '%');
                $query->where($this->tableAlias . '.text LIKE ' . $search);
            }
        }
    }
}
