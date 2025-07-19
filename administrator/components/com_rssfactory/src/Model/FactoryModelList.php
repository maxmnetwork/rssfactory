<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class FactoryModelList extends ListModel
{
    protected $filters = [];
    protected $defaultOrdering = 'title';
    protected $defaultDirection = 'asc';

    public function __construct($config = [])
    {
        // Set filter fields if not provided
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array_keys($this->getSortFields());
        }

        parent::__construct($config);
    }

    /**
     * Get the current list ordering.
     *
     * @return mixed
     */
    public function getListOrder()
    {
        return $this->state->get('list.ordering');
    }

    /**
     * Get the current list direction.
     *
     * @return mixed
     */
    public function getListDirn()
    {
        return $this->state->get('list.direction');
    }

    /**
     * Check if the list ordering is 'ordering'.
     *
     * @return bool
     */
    public function getSaveOrder()
    {
        return $this->getListOrder() == $this->tableAlias . '.ordering';
    }

    /**
     * Get published filter options.
     *
     * @return mixed
     */
    public function getFilterPublished()
    {
        return HTMLHelper::_('jgrid.publishedOptions', [
            'trash'    => false,
            'archived' => false,
            'all'      => false,
        ]);
    }

    /**
     * Get the filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Add a filter for the 'published' field in the query.
     *
     * @param \JDatabaseQuery $query
     * @return void
     */
    protected function addFilterPublished(&$query)
    {
        $published = $this->getState('filter.published');

        if ('' != $published) {
            $query->where($this->tableAlias . '.published = ' . $query->quote($published));
        }
    }

    /**
     * Add order results to the query.
     *
     * @param \JDatabaseQuery $query
     * @return void
     */
    protected function addOrderResults(&$query)
    {
        $orderCol = $this->state->get('list.ordering', $this->tableAlias . '.' . $this->defaultOrdering);
        $orderDirn = $this->state->get('list.direction', $this->defaultDirection);

        $query->order($query->escape($orderCol . ' ' . $orderDirn));
    }

    /**
     * Populate the state with sorting, filtering, and pagination parameters.
     *
     * @param string|null $ordering
     * @param string|null $direction
     * @return void
     */
    protected function populateState($ordering = null, $direction = null)
    {
        if (is_null($ordering)) {
            $ordering = $this->tableAlias . '.' . $this->defaultOrdering;
        }

        if (is_null($direction)) {
            $direction = $this->defaultDirection;
        }

        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout')) {
            $this->context .= '.' . $layout;
        }

        // Get search filter state.
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // Set other filters.
        foreach ($this->filters as $filter) {
            $value = $this->getUserStateFromRequest($this->context . '.filter.' . $filter, 'filter_' . $filter, '');
            $this->setState('filter.' . $filter, $value);
        }

        // Populate list state.
        parent::populateState($ordering, $direction);
    }

    /**
     * Get the query to retrieve the list of items.
     *
     * @return \JDatabaseQuery
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('f.*')
            ->from($db->quoteName('#__rssfactory', 'f'));

        return $query;
    }
}
