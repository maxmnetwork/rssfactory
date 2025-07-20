<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Comments;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Comments View for RSS Factory
 *
 * @since  4.0.0
 */
class CommentsHtml extends BaseHtmlView
{
    protected array $items = [];
    protected object $pagination;
    protected object $state;
    protected string $listOrder = '';
    protected string $listDirn = '';
    protected bool $saveOrder = false;
    protected array $sortFields = [];
    protected array $filters = [];

    public function display($tpl = null): void
    {
        // Load data from the model
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');
        $this->listOrder  = $this->state->get('list.ordering');
        $this->listDirn   = $this->state->get('list.direction');
        $this->saveOrder  = $this->listOrder === 'ordering';
        $this->sortFields = $this->get('SortFields');
        $this->filters    = $this->get('ActiveFilters');

        // Optional: check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors));
        }

        parent::display($tpl);
    }
}
