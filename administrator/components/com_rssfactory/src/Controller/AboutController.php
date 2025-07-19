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

namespace Joomla\Component\Rssfactory\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class AboutController extends BaseController
{
    /**
     * @var string Option name for the component
     */
    protected $option = 'com_rssfactory';

    /**
     * Method to cancel the current operation and redirect.
     *
     * @return void
     */
    public function cancel()
    {
        // Redirect back to the component's main page
        $this->setRedirect(Route::_('index.php?option=' . $this->option, false));
    }
}
