<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Feeds;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Object\CMSObject; // Optional, only if needed for state typing
use Joomla\CMS\Factory;

/**
 * Admin Feeds View for com_rssfactory.
 *
 * @since  4.0
 */
class FeedsHtml extends HtmlView
{
    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @var Pagination
     */
    protected Pagination $pagination;

    /**
     * @var object
     */
    protected object $state;

    /**
     * Execute and display a template script.
     *
     * @param string|null $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return void
     *
     * @throws \Exception If an error occurs.
     */
    public function display($tpl = null): void
    {
        // Debugging line: Remove or comment for production
        echo '<div style="background: lime; padding: 1rem;">🟩 Feeds view loaded</div>';

        // Fetching the items, pagination, and state
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        // Check if there are any errors and throw exception if needed
        if ($errors = $this->get('Errors')) {
            // You can also log errors here if necessary
            throw new \RuntimeException(implode("\n", $errors));
        }

        // Optionally set the layout if not passed as part of template parameter
        $this->setLayout($tpl ?? 'default');

        // Render the template
        parent::display($tpl);
    }
}
