<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Application\CMSApplication;
use Joomla\Database\DatabaseDriver;

class FactoryMenu
{
    /**
     * @var CMSApplication
     */
    protected $app;

    /**
     * @var DatabaseDriver
     */
    protected $db;

    /**
     * Constructor with Dependency Injection (DI)
     *
     * @param CMSApplication|null $app
     * @param DatabaseDriver|null $db
     */
    public function __construct(CMSApplication $app = null, DatabaseDriver $db = null)
    {
        $this->app = $app ?: Factory::getApplication();
        $this->db = $db ?: Factory::getDbo();
    }

    /**
     * Create a menu, its items, and a corresponding menu module
     *
     * @param array $menu
     * @param array $items
     * @param string $component
     * @param array $module
     * @return bool
     */
    public function createMenu(array $menu, array $items, string $component, array $module): bool
    {
        // Check if the menu type already exists
        if ($this->menuTypeExists($menu['menutype'])) {
            return true;
        }

        // Create the menu type, menu items, and menu module
        $this->createMenuType($menu);
        $this->createMenuItems($menu, $items, $component);
        $this->createMenuModule($menu, $module);

        return true;
    }

    /**
     * Check if a menu type already exists
     *
     * @param string $menuType
     * @return bool
     */
    protected function menuTypeExists(string $menuType): bool
    {
        $table = Table::getInstance('MenuType');
        $result = $table->load(['menutype' => $menuType]);

        return !empty($result);
    }

    /**
     * Create a menu type
     *
     * @param array $menu
     * @return bool
     */
    protected function createMenuType(array $menu): bool
    {
        $table = Table::getInstance('MenuType');
        return $table->save($menu);
    }

    /**
     * Create menu items
     *
     * @param array $menu
     * @param array $items
     * @param string $component
     */
    protected function createMenuItems(array $menu, array $items, string $component): void
    {
        $extension = Table::getInstance('Extension');
        $componentId = $extension->find(['type' => 'component', 'element' => $component]);

        foreach ($items as $item) {
            $this->createMenuItem($menu, $item, $componentId);
        }
    }

    /**
     * Create a single menu item
     *
     * @param array $menu
     * @param array $item
     * @param int $componentId
     * @return bool
     */
    protected function createMenuItem(array $menu, array $item, int $componentId): bool
    {
        $defaults = [
            'menutype'     => $menu['menutype'],
            'alias'        => OutputFilter::stringURLSafe($item['title']),
            'type'         => 'component',
            'published'    => 1,
            'parent_id'    => 1,
            'level'        => 1,
            'component_id' => $componentId,
            'access'       => 1,
            'client_id'    => 0,
            'language'     => '*',
        ];

        $data = array_merge($defaults, $item);
        $table = Table::getInstance('Menu');

        $table->setLocation($data['parent_id'], 'last-child');

        return $table->save($data);
    }

    /**
     * Create a menu module
     *
     * @param array $menu
     * @param array $module
     * @param string $position
     * @return bool
     */
    protected function createMenuModule(array $menu, array $module, string $position = 'position-7'): bool
    {
        $data = [
            'title'     => $module['title'],
            'ordering'  => 0,
            'position'  => $position,
            'published' => 1,
            'module'    => 'mod_menu',
            'access'    => 1,
            'showtitle' => 1,
            'language'  => '*',
            'client_id' => 0,
            'params'    => '{"menutype":"' . $menu['menutype'] . '"}',
        ];

        $table = Table::getInstance('Module');

        if (!$table->save($data)) {
            return false;
        }

        $this->db->setQuery('INSERT INTO `#__modules_menu` (moduleid, menuid) VALUES (' . $table->id . ', 0)');

        return $this->db->execute();
    }
}
