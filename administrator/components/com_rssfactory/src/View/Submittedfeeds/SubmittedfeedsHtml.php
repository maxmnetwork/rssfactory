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

namespace Joomla\Component\Rssfactory\Administrator\View\Submittedfeeds;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;

class SubmittedfeedsHtml extends HtmlView
{
    protected $items;
    protected $pagination;
    protected $sidebar;
    protected $state;
    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $sortFields;
    protected $filters;
    protected $buttons;
    protected $html;

    // Inject dependencies (DI)
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->buttons = [
            'add',
            'edit',
            'publish',
            'unpublish',
            'delete',
        ];

        $this->html = [
            'bootstrap.tooltip',
            'behavior.multiselect',
            'dropdown.init',
        ];
    }

    public function display($tpl = null)
    {
        // Get the necessary data
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->listOrder = $this->get('ListOrder');
        $this->listDirn = $this->get('ListDirn');
        $this->saveOrder = $this->get('SaveOrder');
        $this->sortFields = $this->get('SortFields');
        $this->filters = $this->get('Filters');
        $this->sidebar = $this->get('Sidebar');

        // Pass the necessary data to the template
        parent::display($tpl);
    }
}
