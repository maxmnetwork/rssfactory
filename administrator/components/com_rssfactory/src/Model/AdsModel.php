<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class AdsModel extends ListModel
{
    protected $filters = ['published', 'category'];
    protected $defaultOrdering = 'title';
    protected $defaultDirection = 'asc';

    public function getSortFields()
    {
        return [
            'a.title'     => Text::_('JGLOBAL_TITLE'),
            'a.published' => Text::_('JSTATUS'),
            'a.id'        => Text::_('JGRID_HEADING_ID'),
        ];
    }

    public function getFilterCategory()
    {
        return HTMLHelper::_('category.options', 'com_rssfactory');
    }

    public function getItems()
    {
        return parent::getItems();
    }

    public function getPagination()
    {
        return $this->getPaginationObject();
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__rssfactory_ads', 'a'));

        // Select the assigned categories.
        $query->select('GROUP_CONCAT(CAST(c.title AS CHAR) SEPARATOR ", ") AS categories')
            ->leftJoin('#__rssfactory_ad_category_map map ON map.adId = a.id')
            ->leftJoin('#__categories c ON c.id = map.categoryId AND c.extension = ' . $query->quote('com_rssfactory'))
            ->group('a.id');

        $this->addFilterSearch($query);
        $this->addFilterPublished($query);
        $this->addFilterCategory($query);
        $this->addOrderResults($query);

        return $query;
    }

    protected function addFilterSearch(&$query)
    {
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $search = $query->quote('%' . $query->escape($search, true) . '%');
                $query->where('a.title LIKE ' . $search);
            }
        }
    }

    protected function addFilterCategory(&$query)
    {
        $category = $this->getState('filter.category');

        if ('' != $category) {
            $query->leftJoin('#__rssfactory_ad_category_map map_filter ON map_filter.adId = a.id')
                ->where('map_filter.categoryId = ' . $query->quote($category));
        }
    }
}
