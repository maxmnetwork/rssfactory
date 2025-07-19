<?php
namespace Joomla\Component\Rssfactory\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;

class FeedController extends FormController
{
    protected $option = 'com_rssfactory';

    public function submit()
    {
        Session::checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));

        $data = $this->input->get('jform', [], 'array');

        $model = $this->getModel('Feed');
        if ($model->submitFeed($data)) {
            $this->setMessage(Text::_('COM_RSSFACTORY_FEED_SUBMITTED_SUCCESS'));
        } else {
            $this->setMessage(Text::_('COM_RSSFACTORY_FEED_SUBMITTED_ERROR'), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_rssfactory&view=feed', false));
    }
}
