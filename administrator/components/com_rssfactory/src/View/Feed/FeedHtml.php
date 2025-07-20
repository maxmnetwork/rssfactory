<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Feed;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class FeedHtml extends HtmlView
{
    protected $item;
    protected $form;
    protected $state;

    public function display($tpl = null)
    {
        // Get data from the model
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');
        $this->state = $this->get('State');

        // Load jQuery UI and Bootstrap
        $this->loadAssets();

        // Add toolbar
        $this->addToolbar();

        parent::display($tpl);
    }

    protected function loadAssets()
    {
        // Joomla 4: Use WebAssetManager to load jQuery UI (for sortable, etc.)
        $doc = Factory::getDocument();
        $wa = $doc->getWebAssetManager();
        $wa->useScript('jquery');

        // Register and use jQuery UI if it's not already registered
        if (!$wa->assetExists('script', 'jquery.ui')) {
            $wa->registerScript(
                'jquery.ui',
                'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
                ['jquery'],
                ['defer' => true]
            );
            $wa->registerStyle(
                'jquery.ui',
                'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css'
            );
        }

        // Use the registered assets
        $wa->useScript('jquery.ui');
        $wa->useStyle('jquery.ui');

        // Load Bootstrap 5 (Joomla 4 default)
        HTMLHelper::_('bootstrap.framework');

        // Add tooltips, multiselect, dropdown, and form validation
        HTMLHelper::_('bootstrap.tooltip');
        HTMLHelper::_('formbehavior.multiselect');
        HTMLHelper::_('dropdown.init');
        HTMLHelper::_('formbehavior.formvalidation');

        // Optionally, load custom CSS
        HTMLHelper::_('stylesheet', 'com_rssfactory/admin.css', ['relative' => true, 'version' => 'auto']);
    }

    protected function addToolbar()
    {
        $isNew = empty($this->item->id);
        $title = $isNew ? \Joomla\CMS\Language\Text::_('COM_RSSFACTORY_FEED_NEW') : \Joomla\CMS\Language\Text::_('COM_RSSFACTORY_FEED_EDIT');

        ToolbarHelper::title($title, 'rssfactory');
        ToolbarHelper::apply('feed.apply');
        ToolbarHelper::save('feed.save');
        ToolbarHelper::save2new('feed.save2new');
        ToolbarHelper::cancel('feed.cancel', 'JTOOLBAR_CLOSE');

        // Add refresh button if editing
        if (!$isNew) {
            $bar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');
            $bar->appendButton('Standard', 'loop', 'feeds_list_refresh', 'feed.refresh', false);
        }
    }
}
