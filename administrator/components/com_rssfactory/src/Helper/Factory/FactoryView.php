<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Helper\Factory;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
use Joomla\Component\Rssfactory\Administrator\Helper\RssFactoryHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryText;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryViewHelp;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryHtml;

class FactoryView extends HtmlView
{
    protected string $option = 'com_rssfactory';
    protected array $get = [];
    protected array $buttons = [];
    protected string $title = 'title';
    protected string $id = 'id';
    protected array $css = [];
    protected array $js = [];
    protected array $html = [];
    protected array $registerHtml = [];
    protected array $filters = [];
    protected array $permissions = [];
    protected ?string $tpl = null;
    protected ?string $layout = null;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        // Permission check
        if ($this->permissions) {
            $user = Factory::getApplication()->getIdentity();
            foreach ($this->permissions as $layout => $permission) {
                if (is_string($layout) && $this->getLayout() != $layout) {
                    continue;
                }

                if (!$user->authorise($permission, $this->option)) {
                    throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
                }
            }
        }

        $this->document = $this->document ?? Factory::getDocument();
    }

    public function display(?string $tpl = null)
    {
        $isAdmin = Factory::getApplication()->isClient('administrator');

        if (is_null($tpl)) {
            $tpl = $this->tpl;
        }

        if (!is_null($this->layout)) {
            $this->setLayout($this->layout);
        }

        foreach ($this->get as $get) {
            $this->$get = $this->get($get);
        }

        // Error handling
        if ($errors = $this->get('Errors')) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->loadAssets();
        $this->setCharset();

        if ($isAdmin) {
            $this->addToolbar();
            $this->addFilters();

            if (isset($this->saveOrder) && $this->saveOrder) {
                $saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&tmpl=component';
                HTMLHelper::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($this->listDirn), $saveOrderingUrl);
            }

            RssFactoryHelper::addSubmenu($this->getName());
            if (Version::MAJOR_VERSION === 3) {
                $this->sidebar = HTMLHelper::_('sidebar.render');
            }

            $viewHelp = new FactoryViewHelp();
            $viewHelp->render($this->getName());
        } else {
            $this->prepareDocument();
        }

        return parent::display($tpl);
    }

    protected function addToolbar(): bool
    {
        $this->setTitle();

        foreach ($this->buttons as $type => $button) {
            if (is_int($type)) {
                $type = $button;
            }

            switch ($type) {
                case '':
                    ToolbarHelper::divider();
                    break;
                case 'add':
                    ToolbarHelper::addNew(rtrim($this->getName(), 's') . '.add');
                    break;
                case 'edit':
                    ToolbarHelper::editList(rtrim($this->getName(), 's') . '.edit');
                    break;
                case 'publish':
                    ToolbarHelper::publish($this->getName() . '.publish');
                    break;
                case 'unpublish':
                    ToolbarHelper::unpublish($this->getName() . '.unpublish');
                    break;
                case 'delete':
                    ToolbarHelper::delete($this->getName() . '.delete', FactoryText::_('list_delete'));
                    break;
                case 'apply':
                    ToolbarHelper::apply($this->getName() . '.apply');
                    break;
                case 'save':
                    ToolbarHelper::save($this->getName() . '.save');
                    break;
                case 'save2new':
                    ToolbarHelper::save2new($this->getName() . '.save2new');
                    break;
                case 'save2copy':
                    ToolbarHelper::save2copy($this->getName() . '.save2copy');
                    break;
                case 'close':
                    ToolbarHelper::cancel($this->getName() . '.cancel', (isset($this->item) && $this->item->{$this->id} ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL'));
                    break;
                case 'back':
                    ToolbarHelper::back();
                    break;
                case 'batch':
                    $title = FactoryText::_($button[0]);
                    $bar = Toolbar::getInstance('toolbar');
                    $dhtml = "<button data-bs-toggle=\"modal\" data-bs-target=\"#collapseModal\" class=\"btn btn-small btn-secondary\"><i class=\"icon-" . $button[1] . "\" title=\"$title\"></i>$title</button>";
                    $bar->appendButton('Custom', $dhtml, 'batch');
                    break;
                default:
                    ToolbarHelper::custom($this->getName() . '.' . $button[0], $button[2], $button[2], FactoryText::_($button[1]), $button[3]);
                    break;
            }
        }

        return true;
    }

    protected function setTitle(): bool
    {
        if (isset($this->item) && $this->item) {
            if ($this->item->{$this->id}) {
                $title = is_null($this->title) ? '' : $this->item->{$this->title};
                ToolbarHelper::title(FactoryText::sprintf('view_title_edit_' . $this->getName(), $title, $this->item->{$this->id}));
            } else {
                ToolbarHelper::title(FactoryText::_('view_title_new_' . $this->getName()));
            }
        } else {
            ToolbarHelper::title(FactoryText::_('view_title_' . $this->getName()));
        }

        return true;
    }

    protected function loadAssets(): void
    {
        $prefix = Factory::getApplication()->isClient('administrator') ? 'admin/' : '';

        foreach ($this->html as $html) {
            if (false !== strpos($html, '/')) {
                list($html, $arg) = explode('/', $html);
                HTMLHelper::_($html, $arg);
            } else {
                HTMLHelper::_($html);
            }
        }

        $version = (int)Version::MAJOR_VERSION;

        $this->css[] = $prefix . 'migration' . $version;
        $this->css[] = $prefix . 'views/' . strtolower($this->getName());
        foreach ($this->css as $css) {
            FactoryHtml::stylesheet($css);
        }

        $this->js[] = $prefix . 'views/' . strtolower($this->getName());
        foreach ($this->js as $js) {
            FactoryHtml::script($js);
        }

        foreach ($this->registerHtml as $html) {
            FactoryHtml::registerHtml($html);
        }
    }

    protected function addFilters(): bool
    {
        if (!$this->filters) {
            return true;
        }

        HTMLHelper::_('sidebar.setAction', 'index.php?option=' . $this->option . '&view=' . $this->getName());

        foreach ($this->filters as $filter) {
            HTMLHelper::_('sidebar.addFilter',
                FactoryText::_('list_filter_title_' . $filter),
                'filter_' . $filter,
                HTMLHelper::_('select.options', $this->get('Filter' . ucfirst($filter)), 'value', 'text', $this->state->get('filter.' . $filter), true)
            );
        }
    }

    protected function prepareDocument(): bool
    {
        $app = Factory::getApplication();
        $menu = $app->getMenu();
        $active = $menu->getActive();

        $title = $active ? $active->title : null;

        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        if (empty($title) && isset($this->item->title)) {
            $title = $this->item->title;
        }

        $this->document->setTitle($title);

        return true;
    }

    protected function setCharset(): void
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $charset = $configuration->get('force_charset', '');

        if ('' != $charset) {
            Factory::getDocument()->setCharset($charset);
        }
    }
}
