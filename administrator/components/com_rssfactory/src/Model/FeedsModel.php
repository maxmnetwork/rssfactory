<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\ParameterType;
use Joomla\Database\DatabaseQuery;

class FeedsModel extends ListModel
{
    protected string $option = 'com_rssfactory';
    protected array $filters = ['published', 'category'];
    protected string $defaultOrdering = 'title';
    protected string $defaultDirection = 'asc';

    /**
     * Returns an array of fields the user can sort by.
     *
     * @return array
     */
    public function getSortFields(): array
    {
        $textHelper = \Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss::class;

        return [
            'f.ordering'    => Text::_('JGRID_HEADING_ORDERING'),
            'f.published'   => Text::_('JSTATUS'),
            'f.title'       => Text::_('JGLOBAL_TITLE'),
            'c.title'       => Text::_('JCATEGORY'),
            'f.date'        => $textHelper::_('feeds_list_last_refresh'),
            'f.nrfeeds'     => $textHelper::_('feeds_list_title_nr_feeds'),
            'f.rsserror'    => $textHelper::_('feeds_list_had_error'),
            'f.url'         => $textHelper::_('feeds_list_url'),
            'f.i2c_enabled' => $textHelper::_('feeds_list_i2c_enabled'),
            'f.id'          => Text::_('JGRID_HEADING_ID'),
        ];
    }

    /**
     * Get category filter options.
     *
     * @return array
     */
    public function getFilterCategory(): array
    {
        return HTMLHelper::_('category.options', 'com_rssfactory');
    }

    /**
     * Return the pagination object.
     *
     * @return \Joomla\CMS\Pagination\Pagination
     */
    public function getPagination(): \Joomla\CMS\Pagination\Pagination
    {
        return $this->getPaginationObject();
    }

    /**
     * Build the query for the list.
     *
     * @return DatabaseQuery
     */
    protected function getListQuery(): DatabaseQuery
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('f.*')
              ->from($db->quoteName('#__rssfactory', 'f'));

        $query->select('c.title AS category_title')
              ->leftJoin(
                  $db->quoteName('#__categories') . ' AS c ON c.id = f.cat AND c.extension = ' . $db->quote('com_rssfactory')
              );

        $query->select('COUNT(cache.id) AS storiesCached')
              ->leftJoin(
                  $db->quoteName('#__rssfactory_cache') . ' AS cache ON cache.rssid = f.id'
              )
              ->group('f.id');

        $this->addFilterSearch($query);
        $this->addFilterPublished($query);
        $this->addFilterCategory($query);
        $this->addOrderResults($query);

        return $query;
    }

    /**
     * Apply search filter to the query.
     *
     * @param DatabaseQuery $query
     * @return void
     */
    protected function addFilterSearch(DatabaseQuery &$query): void
    {
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('f.id = :id')
                      ->bind(':id', (int) substr($search, 3), ParameterType::INTEGER);
            } else {
                $search = '%' . $query->escape($search, true) . '%';
                $query->where('f.title LIKE :search')
                      ->bind(':search', $search, ParameterType::STRING);
            }
        }
    }

    /**
     * Apply category filter to the query.
     *
     * @param DatabaseQuery $query
     * @return void
     */
    protected function addFilterCategory(DatabaseQuery &$query): void
    {
        $category = $this->getState('filter.category');

        if ($category !== '') {
            $query->where('f.cat = :cat')
                  ->bind(':cat', $category, ParameterType::INTEGER);
        }
    }

    /**
     * Populate the model state.
     *
     * @param string|null $ordering
     * @param string|null $direction
     * @return void
     */
    protected function populateState($ordering = null, $direction = null): void
    {
        parent::populateState($ordering, $direction);

        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        $this->setState('filter.search', $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
        $this->setState('filter.published', $app->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string'));
        $this->setState('filter.category', $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'string'));

        $this->setState('list.ordering', $ordering ?: $this->defaultOrdering);
        $this->setState('list.direction', $direction ?: $this->defaultDirection);
    }
}
