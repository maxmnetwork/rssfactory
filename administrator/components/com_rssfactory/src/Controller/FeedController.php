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
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Log\Log;
use Joomla\Utilities\ArrayHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

class FeedController extends FormController
{
    protected $option = 'com_rssfactory';

    public function refresh()
    {
        // Check CSRF token
        Session::checkToken() or exit(Text::_('JINVALID_TOKEN'));

        // Retrieve input
        $input = Factory::getApplication()->input;
        $id = $input->getInt('id', 0);
        $cid = [$id];

        // Ensure item(s) are selected
        if (empty($cid)) {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        } else {
            $model = $this->getModel();
            ArrayHelper::toInteger($cid);

            // Refresh the feed
            if (!$model->refresh($cid)) {
                Log::add($model->getState('error'), Log::WARNING, 'jerror');
            } else {
                $ntext = $this->text_prefix . '_N_ITEMS_REFRESHED';
                $this->setMessage(Text::plural($ntext, count($cid)));
            }
        }

        // Redirect after operation
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=feed&layout=edit&id=' . $id, false));
    }

    public function clearCache()
    {
        // Check CSRF token
        Session::checkToken() or exit(Text::_('JINVALID_TOKEN'));

        // Retrieve input
        $input = Factory::getApplication()->input;
        $id = $input->getInt('id', 0);
        $cid = [$id];

        // Ensure item(s) are selected
        if (empty($cid)) {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        } else {
            $model = $this->getModel();
            ArrayHelper::toInteger($cid);

            // Clear cache
            if (!$model->clearCache($cid)) {
                Log::add($model->getState('error'), Log::WARNING, 'jerror');
            } else {
                $ntext = $this->text_prefix . '_N_ITEMS_CLEARED_CACHE';
                $this->setMessage(Text::plural($ntext, count($cid)));
            }
        }

        // Redirect after operation
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=feed&layout=edit&id=' . $id, false));
    }

    public function move()
    {
        // Retrieve input and model
        $app = Factory::getApplication();
        $input = $app->input;
        $batch = $input->get('batch', [], 'array');
        $cid = $input->get('cid', [], 'array');
        $model = $this->getModel();

        // Move the feeds
        if ($model->move($cid, $batch)) {
            $msg = FactoryTextRss::_('feed_task_move_success');
        } else {
            $msg = FactoryTextRss::_('feed_task_move_error');
            $app->enqueueMessage($model->getState('error'), 'warning');
        }

        // Redirect after operation
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=feeds', false), $msg);
    }
}
