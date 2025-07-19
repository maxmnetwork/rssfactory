<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Comment;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class CommentView extends BaseHtmlView
{
    protected array $get = [
        'item', 'form', 'state',
    ];

    protected array $buttons = [
        'apply', 'save', 'close',
    ];

    protected array $html = [
        'bootstrap.tooltip',
        'behavior.multiselect',
        'dropdown.init',
        'formvalidator',
    ];

    protected ?string $title = null;

    /**
     * Method to load a specific fieldset template.
     *
     * @param string $fieldset The name of the fieldset template.
     *
     * @return string The output of the fieldset template.
     */
    protected function loadFieldset(string $fieldset): string
    {
        $this->fieldset = $fieldset;
        return $this->loadTemplate('fieldset');
    }

    /**
     * Display the view template.
     *
     * @param string|null $tpl The name of the template file to parse.
     *
     * @return void
     */
    public function display($tpl = null): void
    {
        // Get the data from the model
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $this->state = $this->get('State');

        // Optional: check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new \RuntimeException(implode("\n", $errors));
        }

        // Use default layout (default.php)
        $this->setLayout('default');

        parent::display($tpl);
    }
}
