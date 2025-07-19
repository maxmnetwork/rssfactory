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

// @copilot convert to Joomla\CMS\MVC\Controller\FormController

namespace Joomla\Component\Rssfactory\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Submit Feed Controller class
 *
 * This class extends the Joomla FormController to manage submitting feed data.
 *
 * @since  4.0.0
 */
class SubmitFeedController extends FormController
{
    /**
     * @var string The component option for the controller.
     */
    protected $option = 'com_rssfactory';

    /**
     * Constructor method.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   4.0.0
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }
}
