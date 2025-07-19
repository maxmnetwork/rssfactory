<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Ads;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;

class HtmlView extends BaseHtmlView
{
    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @var \Joomla\CMS\Pagination\Pagination
     */
    protected $pagination;

    /**
     * @var string|null
     */
    protected ?string $sidebar = null;

    /**
     * @var array
     */
    protected array $buttons = [
        'add',
        'edit',
        'publish',
        'unpublish',
        'delete',
    ];

    /**
     * @var array
     */
    protected array $html = [
        'bootstrap.tooltip',
        'behavior.multiselect',
        'dropdown.init',
    ];

    /**
     * Constructor to initialize the class.
     *
     * @param array $config Configuration array.
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        // Add necessary assets for Joomla 4
        $this->addAssets();
    }

    /**
     * Display the view
     *
     * @param string|null $tpl Template file.
     *
     * @return void
     */
    public function display($tpl = null): void
    {
        $this->items     = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->sidebar   = $this->get('Sidebar');

        parent::display($tpl);
    }

    /**
     * Adds required assets (JS, CSS) for this view.
     *
     * @return void
     */
    protected function addAssets(): void
    {
        $document = \Joomla\CMS\Factory::getDocument();
        $wa = $document->getWebAssetManager();

        // Add Bootstrap framework (J4 default)
        \Joomla\CMS\HTML\HTMLHelper::_('bootstrap.framework');

        // Include necessary JS and CSS assets for the view
        $wa->useScript('core.js');  // Add core.js from Joomla
        $wa->useStyle('com_rssfactory/admin.css');  // Add custom admin styles

        // Add other scripts if needed (no longer need chosen/select, if necessary, you can load a custom JS here)
        // $wa->useScript('chosen');  // Example if you need chosen JS (not default in Joomla 4)
    }
}
