<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @author      thePHPfactory
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Rssfactory\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Controller for managing Comments in RSS Factory
 *
 * @since  4.0.0
 */
class CommentsController extends AdminController
{
    protected string $option = 'com_rssfactory';

    /** @var string View list name used in redirects */
    protected string $view_list = 'comments';

    /** @var string Language prefix for messages */
    protected string $text_prefix = 'COM_RSSFACTORY_COMMENTS';

    /**
     * Returns the model for this controller.
     *
     * @param   string  $name     The model name. Default is 'Comment'.
     * @param   string  $prefix   The model prefix. Leave empty for Joomla 4+.
     * @param   array   $config   The model configuration.
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
     */
    public function getModel($name = 'Comment', $prefix = '', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
