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

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Text;

class SubmittedFeedsModel extends ListModel
{
    protected string $defaultOrdering = 'date';
    protected string $defaultDirection = 'desc';

    /**
     * Returns an array of fields the user can sort by.
     */
    public function getSortFields(): array
    {
        return [
            'sf.title' => Text::_('JGLOBAL_TITLE'),
            'sf.url'   => \Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss::_('submittedfeeds_list_url'),
            'sf.date'  => \Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss::_('submittedfeeds_list_date'),
            'sf.id'    => Text::_('JGRID_HEADING_ID'),
        ];
    }

    /**
     * Build the query for the list.
     */
    protected function getListQuery(): \Joomla\Database\DatabaseQuery
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('sf.*')
            ->from($db->quoteName('build_rssfactory_submitted', 'sf'));

        $this->addFilterSearch($query);
        $this->addOrderResults($query);

        return $query;
    }

    /**
     * Apply search filter to the query.
     */
    protected function addFilterSearch(\Joomla\Database\DatabaseQuery &$query): void
    {
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('sf.id = :id')
                      ->bind(':id', (int)substr($search, 3));
            } else {
                $search = '%' . $query->escape($search, true) . '%';
                $query->where('sf.title LIKE :search')
                      ->bind(':search', $search);
            }
        }
    }
}
