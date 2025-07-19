<?php
/**
 * rssfactory - Rss Factory 4.3.6
 *
 * @author thePHPfactory
 * @copyright Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.thePHPfactory.com
 * Technical Support: Forum - http://www.thePHPfactory.com/forum/
 */

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

class CacheController extends BaseController
{
    protected $option = 'com_rssfactory';

    /**
     * Clear the cache.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function clear()
    {
        $model = $this->getModel('Cache');
        $response = [];

        if ($model->clear()) {
            $response['status'] = 1;
            $response['message'] = FactoryTextRss::plural('form_field_rssfactoryproinfo_cache_content', 0);
        } else {
            $response['status'] = 0;
        }

        // Use JsonResponse for consistent and proper JSON handling in Joomla 4.
        $this->sendJsonResponse($response);
    }

    /**
     * Optimize the cache.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function optimize()
    {
        $model = $this->getModel('Cache');
        $response = [];

        if ($model->clear()) {
            $response['status'] = 1;
            $response['message'] = FactoryTextRss::sprintf('form_field_rssfactoryproinfo_cache_table_status', 0);
        } else {
            $response['status'] = 0;
        }

        // Use JsonResponse for consistent and proper JSON handling in Joomla 4.
        $this->sendJsonResponse($response);
    }

    /**
     * Sends a JSON response.
     *
     * @param   array  $response  The response data
     *
     * @return  void
     *
     * @since   4.0.0
     */
    private function sendJsonResponse(array $response)
    {
        // Send the JSON response using the Joomla 4 response class.
        $responseObj = new JsonResponse($response);
        $responseObj->send();

        // Ensure the application is closed after sending the response.
        Factory::getApplication()->close();
    }
}
