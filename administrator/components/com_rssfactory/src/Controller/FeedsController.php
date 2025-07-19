<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @author      thePHPfactory
 * @copyright   Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Rssfactory\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Log\Log;
use Joomla\Utilities\ArrayHelper;

/**
 * Controller class for the Feeds view.
 */
class FeedsController extends AdminController
{
    protected string $option = 'com_rssfactory';

    /** @var string View list name used in redirects */
    protected string $view_list = 'feeds';

    /** @var string Language prefix for messages */
    protected string $text_prefix = 'COM_RSSFACTORY_FEEDS';

    /**
     * Gets a model object.
     *
     * @param  string  $name    The model name.
     * @param  string  $prefix  The model class prefix.
     * @param  array   $config  Configuration array.
     *
     * @return \Joomla\CMS\MVC\Model\BaseDatabaseModel
     */
    public function getModel($name = 'Feeds', $prefix = '', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Display the view.
     */
    public function display($cachable = false, $urlparams = []): void
    {
        if (!$this->input->get('view')) {
            $this->setRedirect(Route::_('index.php?option=com_rssfactory&view=feeds', false))->redirect();
            return;
        }

        parent::display($cachable, $urlparams);
    }

    /**
     * Save ordering via AJAX.
     */
    public function saveOrderAjax(): void
    {
        $pks   = $this->input->post->get('cid', [], 'array');
        $order = $this->input->post->get('order', [], 'array');

        ArrayHelper::toInteger($pks);
        ArrayHelper::toInteger($order);

        $model  = $this->getModel();
        $return = $model->saveOrder($pks, $order);

        echo $return ? '1' : '0';
        $this->app->close();
    }

    /**
     * Refresh selected feeds.
     */
    public function refresh(): void
    {
        Session::checkToken('post') or exit(Text::_('JINVALID_TOKEN'));

        $cid = $this->input->get('cid', [], 'array');

        if (empty($cid)) {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        } else {
            $model = $this->getModel();
            ArrayHelper::toInteger($cid);

            if (!$model->refresh($cid)) {
                Log::add($model->getState('error'), Log::WARNING, 'jerror');
            } else {
                $ntext = $this->text_prefix . '_N_ITEMS_REFRESHED';
                $this->setMessage(Text::plural($ntext, count($cid)));
            }
        }

        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }

    /**
     * Clear cache for selected feeds.
     */
    public function clearCache(): void
    {
        Session::checkToken('post') or exit(Text::_('JINVALID_TOKEN'));

        $cid = $this->input->get('cid', [], 'array');

        if (empty($cid)) {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        } else {
            $model = $this->getModel();
            ArrayHelper::toInteger($cid);

            if (!$model->clearCache($cid)) {
                Log::add($model->getState('error'), Log::WARNING, 'jerror');
            } else {
                $ntext = $this->text_prefix . '_CACHE_CLEARED';
                $this->setMessage(Text::plural($ntext, count($cid)));
            }
        }

        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }
}
