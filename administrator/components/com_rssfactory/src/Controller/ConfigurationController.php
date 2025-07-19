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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

class ConfigurationController extends BaseController
{
    protected $option = 'com_rssfactory';

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->registerTask('apply', 'save');
    }

    public function save()
    {
        $user = Factory::getApplication()->getIdentity();

        // Authorization check
        if (!$user->authorise('backend.settings', $this->option)) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $model = $this->getModel('Configuration');
        $form = $model->getForm();
        $data = Factory::getApplication()->input->get('jform', [], 'array');
        $app = Factory::getApplication();

        // Validate the form
        $return = $model->validate($form, $data);

        if ($return === false) {
            $errors = $model->getErrors();
            foreach ($errors as $i => $error) {
                if ($i >= 3) break; // Limit to 3 errors
                $app->enqueueMessage($error instanceof \Exception ? $error->getMessage() : $error, 'warning');
            }
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=configuration', false));
            return false;
        }

        // Save the configuration data
        if ($model->save($return)) {
            $msg = FactoryTextRss::_('configuration_task_save_success');
        } else {
            $app->enqueueMessage($model->getState('error'), 'error');
            $msg = FactoryTextRss::_('configuration_task_save_error');
        }

        $link = 'index.php?option=' . $this->option;
        if ('apply' === $this->getTask()) {
            $link .= '&view=configuration';
        }

        $this->setRedirect($link, $msg);

        return true;
    }

    public function cancel()
    {
        $this->setRedirect(Route::_('index.php?option=' . $this->option, false));
    }
}
