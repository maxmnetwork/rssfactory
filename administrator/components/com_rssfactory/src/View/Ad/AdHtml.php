<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Ad;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * View class for displaying a single ad in RSS Factory.
 *
 * @since 4.0.0
 */
class AdHtml extends HtmlView
{
    /**
     * @var object
     */
    protected $item;

    /**
     * @var \Joomla\CMS\Form\Form|null
     */
    protected $form;

    /**
     * Display the view
     *
     * @param string|null $tpl Template file
     *
     * @return void
     */
    public function display($tpl = null): void
    {
        // Retrieve the model data
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        // Set the page title
        $document = Factory::getDocument();
        $document->setTitle(Text::_('COM_RSSFACTORY_AD_EDIT_TITLE'));

        // Render the layout (template)
        parent::display($tpl);
    }
}
