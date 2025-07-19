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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\Utilities\ArrayHelper;

class CategoriesController extends AdminController
{
    protected $option = 'com_rssfactory';

    /**
     * Get the model.
     *
     * @param string $name   The model name.
     * @param string $prefix The model prefix.
     * @param array  $config Configuration array.
     *
     * @return \Joomla\CMS\MVC\Model\BaseDatabaseModel The model.
     */
    public function getModel($name = 'Category', $prefix = 'Joomla\Component\Rssfactory\Administrator\Model', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Save the order of categories (AJAX).
     */
    public function saveOrderAjax()
    {
        $input = Factory::getApplication()->input;
        $pks = $input->post->get('cid', [], 'array');
        $order = $input->post->get('order', [], 'array');

        ArrayHelper::toInteger($pks);
        ArrayHelper::toInteger($order);

        $model = $this->getModel();
        $return = $model->saveorder($pks, $order);

        if ($return) {
            echo "1";
        }

        Factory::getApplication()->close();
    }
}
