<?php
namespace Joomla\Component\Rssfactory\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = []): mixed
    {
        // Force the view to "feeds" if none provided
        $input = Factory::getApplication()->input;
        if (empty($input->getCmd('view'))) {
            $input->set('view', 'feeds');
        }

        return parent::display($cachable, $urlparams);
    }
}
