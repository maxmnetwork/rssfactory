<?php
/**
 * @package     Com_Rssfactory
 * @subpackage  View
 * @copyright   Copyright (C) 2024
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Joomla\Component\Rssfactory\Administrator\View\Category;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Object\CMSObject;

/**
 * View class for a single Category in RSS Factory.
 *
 * @since  4.0.0
 */
class CategoryView extends HtmlView
{
    /**
     * The item object.
     *
     * @var  object|null
     */
    protected ?object $item = null;

    /**
     * The form object.
     *
     * @var  Form|null
     */
    protected ?Form $form = null;

    /**
     * The state object.
     *
     * @var  CMSObject|null
     */
    protected ?CMSObject $state = null;

    /**
     * The items for the view.
     *
     * @var  array|null
     */
    protected ?array $items = null;

    /**
     * The pagination object.
     *
     * @var  Pagination|null
     */
    protected ?Pagination $pagination = null;

    /**
     * The sidebar content.
     *
     * @var  string|null
     */
    protected ?string $sidebar = null;

    /**
     * Display the view
     *
     * @param   string|null  $tpl  The template file to include
     *
     * @return  void
     *
     * @throws \RuntimeException
     */
    public function display($tpl = null): void
    {
        // Load data from the model
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->sidebar = $this->get('Sidebar');

        // Check for errors
        $errors = $this->get('Errors');
        if (!empty($errors)) {
            throw new \RuntimeException(implode("\n", $errors), 500);
        }

        // Add toolbar
        $this->addToolbar();

        // Render the template
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolbar(): void
    {
        $isNew = empty($this->item->id);

        // Get the toolbar instance
        $toolbar = Toolbar::getInstance('toolbar');

        // Set the page title
        $title = $isNew ? Text::_('COM_RSSFACTORY_CATEGORY_NEW') : Text::_('COM_RSSFACTORY_CATEGORY_EDIT');
        $toolbar->title($title, 'folder');

        // Add toolbar buttons
        $toolbar->apply('category.apply');
        $toolbar->save('category.save');
        $toolbar->save2new('category.save2new');

        if (!$isNew) {
            $toolbar->save2copy('category.save2copy');
        }

        // Add cancel or close button
        $toolbar->cancel('category.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
