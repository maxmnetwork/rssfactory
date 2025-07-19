<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @author      thePHPfactory
 * @copyright   Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// @copilot convert to Joomla\CMS\MVC\View\HtmlView

namespace Joomla\Component\Rssfactory\Administrator\View\Story;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;

class StoryView extends HtmlView
{
    protected $items;
    protected $pagination;

    // Constructor for Dependency Injection (if necessary)
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function display($tpl = null)
    {
        // Get the items and pagination from the model
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check if there is any data, if not return a message
        if (empty($this->items)) {
            $this->items = [new \stdClass()];  // Empty object to prevent error in view rendering
        }

        parent::display($tpl);
    }
}
