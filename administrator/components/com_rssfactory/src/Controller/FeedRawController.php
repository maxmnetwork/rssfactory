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
use Joomla\CMS\Response\JsonResponse;

class FeedRawController extends FormController
{
    protected $option = 'com_rssfactory';

    public function preview()
    {
        $input = Factory::getApplication()->input;
        $data = $input->get('jform', [], 'array');
        $model = $this->getModel();

        try {
            $result = $model->preview($data);
            $response = [
                'status' => 1,
                'result' => $result
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => 0,
                'message' => $e->getMessage()
            ];
        }

        return new JsonResponse($response);
    }
}
