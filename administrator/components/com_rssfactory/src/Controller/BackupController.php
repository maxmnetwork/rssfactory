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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Log\Log;
use Joomla\Utilities\ArrayHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

class BackupController extends BaseController
{
    protected $option = 'com_rssfactory';

    public function restore()
    {
        $model = $this->getModel('Backup');
        $app = Factory::getApplication();
        $data = $app->input->files->get('restore_archive', array());

        if ($model->restoreBackup($data)) {
            $app->enqueueMessage(FactoryTextRss::_('backup_task_restore_success'), 'message');
        } else {
            $app->enqueueMessage(FactoryTextRss::_('backup_task_restore_error'), 'error');
        }

        if ('' != $error = $model->getState('error')) {
            $app->enqueueMessage($error, 'error');
        }

        $restored = $model->getState('restored', array());

        if (isset($restored['feeds'])) {
            $app->enqueueMessage(FactoryTextRss::plural('backp_task_restore_notice_feeds', $restored['feeds']), 'notice');
        }

        if (isset($restored['categories'])) {
            $app->enqueueMessage(FactoryTextRss::plural('backp_task_restore_notice_categories', $restored['feeds']), 'notice');
        }

        if (isset($restored['ads'])) {
            $app->enqueueMessage(FactoryTextRss::plural('backp_task_restore_notice_ads', $restored['ads']), 'notice');
        }

        $this->setRedirect('index.php?option=' . $this->option . '&view=backup');
        return true;
    }

    public function generate()
    {
        $model = $this->getModel('Backup');
        $app = Factory::getApplication();

        if (!$model->generateBackup()) {
            $app->enqueueMessage(FactoryTextRss::_('backup_task_generate_error'), 'error');
            $app->enqueueMessage($model->getState('error'), 'error');
        }

        $this->setRedirect('index.php?option=' . $this->option . '&view=backup');
        return true;
    }

    public function import()
    {
        $model = $this->getModel('Backup');
        $app = Factory::getApplication();
        $separator = $app->input->post->getString('import_separator', 'TAB');
        $file = $app->input->files->get('import_file', array());

        if ($model->import($file, $separator)) {
            $app->enqueueMessage(FactoryTextRss::_('backup_task_import_success'), 'message');
        } else {
            $app->enqueueMessage(FactoryTextRss::_('backup_task_import_error'), 'error');
            $app->enqueueMessage($model->getState('error'), 'error');
        }

        $this->setRedirect('index.php?option=' . $this->option . '&view=backup');
        return true;
    }

    public function cancel()
    {
        $this->setRedirect(Route::_('index.php?option=' . $this->option, false));
    }
}
