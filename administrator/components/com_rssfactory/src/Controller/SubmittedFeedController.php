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

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class SubmittedFeedController extends FormController
{
    protected $option = 'com_rssfactory';

    /**
     * Overrides the batch method to apply custom batch actions.
     *
     * @param   object  $model  The model to be used.
     *
     * @return  mixed  The response from the parent batch method.
     */
    public function batch($model = null)
    {
        // Ensure token validity
        Session::checkToken() or exit(Text::_('JINVALID_TOKEN'));

        // Get the model to handle the batch task
        $model = $this->getModel('SubmittedFeed', '', array());

        // Redirect after batch operation
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=submittedfeeds', false));

        // Call parent batch function to continue the operation
        return parent::batch($model);
    }
}
