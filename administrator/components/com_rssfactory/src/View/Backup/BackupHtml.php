<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Backup;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\WebAsset\WebAssetManager;

class BackupHtml extends HtmlView
{
    /**
     * List of toolbar buttons.
     *
     * @var array
     */
    protected array $buttons = [
        'close',
    ];

    /**
     * HTML assets to be loaded for the view.
     *
     * @var array
     */
    protected array $html = [
        'behavior.framework', // For Joomla 4's framework (e.g., JS libraries)
    ];

    /**
     * Display the view
     *
     * @param   string|null  $tpl  The template file to display
     *
     * @return  void
     */
    public function display($tpl = null): void
    {
        $this->addAssets();
        
        // Call parent display method
        parent::display($tpl);
    }

    /**
     * Adds the required assets (JS, CSS) for the view.
     *
     * @return void
     */
    protected function addAssets(): void
    {
        $document = \Joomla\CMS\Factory::getDocument();
        $wa = $document->getWebAssetManager();

        // Add the necessary JS and CSS for this view
        $wa->useScript('core.js'); // Add core.js from Joomla
        $wa->useStyle('com_rssfactory/admin.css'); // Add custom admin styles

        // If needed, register other assets like chosen/select (J4 equivalent of "chosen" behavior)
        // $wa->registerScript('chosen', 'path_to_chosen.js', ['defer' => true]);
        // $wa->useScript('chosen');
    }
}
