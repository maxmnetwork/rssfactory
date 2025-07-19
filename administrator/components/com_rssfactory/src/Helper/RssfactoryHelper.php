<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class RssfactoryHelper
{
    /**
     * Configure the Admin Submenu.
     *
     * @param string $vName Active view name
     */
    public static function addSubmenu(string $vName = ''): void
    {
        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_FEEDS'),
            'index.php?option=com_rssfactory&view=feeds',
            $vName === 'feeds'
        );

        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_COMMENTS'),
            'index.php?option=com_rssfactory&view=comments',
            $vName === 'comments'
        );

        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_ADS'),
            'index.php?option=com_rssfactory&view=ads',
            $vName === 'ads'
        );

        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_SUBMITTED'),
            'index.php?option=com_rssfactory&view=submitted',
            $vName === 'submitted'
        );

        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&extension=com_rssfactory',
            $vName === 'categories'
        );

        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_SETTINGS'),
            'index.php?option=com_config&view=component&component=com_rssfactory',
            $vName === 'settings'
        );

        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_BACKUP'),
            'index.php?option=com_rssfactory&view=backup',
            $vName === 'backup'
        );

        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_ABOUT'),
            'index.php?option=com_rssfactory&view=about',
            $vName === 'about'
        );
    }
}
