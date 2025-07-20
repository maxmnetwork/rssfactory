<?php

namespace Joomla\Component\Rssfactory\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Log\Log;
use Joomla\Utilities\ArrayHelper;

class FeedsController extends BaseController
{
    protected string $option = 'com_rssfactory';
    protected string $view_list = 'feeds';
    protected string $text_prefix = 'COM_RSSFACTORY_FEEDS';

    public function getModel($name = 'Feeds', $prefix = '', $config = ['ignore_request' => true])
    {
        echo "FeedsController: getModel() is being called.<br>";  // Debugging output
        return parent::getModel($name, $prefix, $config);
    }

    protected $default_view = 'feeds';
    /**
     * Display the view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached.
     * @param   array    $urlparams  An array of safe URL parameters and their variable types.
     *
     * @return  void
     */
    public function display($cachable = false, $urlparams = []): void
    {

        parent::display($cachable, $urlparams);
    }

    public function saveOrderAjax(): void
    {
        $pks   = $this->input->post->get('cid', [], 'array');
        $order = $this->input->post->get('order', [], 'array');

        ArrayHelper::toInteger($pks);
        ArrayHelper::toInteger($order);

        $model  = $this->getModel();
        
        // Add logging to check if the model is loaded correctly
        Log::add('View loaded: ' . get_class($view), Log::INFO, 'debug');
        
        $return = $model->saveOrder($pks, $order);

        echo $return ? '1' : '0';
        $this->app->close();
    }

    public function refresh(): void
    {
        Session::checkToken('post') or exit(Text::_('JINVALID_TOKEN'));

        $cid = $this->input->get('cid', [], 'array');

        if (empty($cid)) {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        } else {
            $model = $this->getModel();
            
            // Add logging to check if the model is loaded correctly
            Log::add('Model class: ' . get_class($model), Log::INFO, 'debug');
            
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

    public function clearCache(): void
    {
        Session::checkToken('post') or exit(Text::_('JINVALID_TOKEN'));

        $cid = $this->input->get('cid', [], 'array');

        if (empty($cid)) {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        } else {
            $model = $this->getModel();
            
            // Add logging to check if the model is loaded correctly
            Log::add('Model class: ' . get_class($model), Log::INFO, 'debug');
            
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
