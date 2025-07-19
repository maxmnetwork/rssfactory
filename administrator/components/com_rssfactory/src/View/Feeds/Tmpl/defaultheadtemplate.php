<?php
// DEBUG: Feeds/Tmpl/defaultheadtemplate.php loaded

/**
-------------------------------------------------------------------------
rssfactory - Rss Factory 4.3.6
-------------------------------------------------------------------------
 * @author thePHPfactory
 * @copyright Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.thePHPfactory.com
 * Technical Support: Forum - http://www.thePHPfactory.com/forum/
-------------------------------------------------------------------------
*/

namespace Joomla\Component\Rssfactory\Administrator\View\Feeds\Tmpl;

echo '<!-- DEBUG: Feeds/Tmpl/defaultheadtemplate.php loaded -->';

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
?>
<tr>
    <th width="1%"><?= Text::_('JGRID_HEADING_ID'); ?></th>
    <th><?= Text::_('COM_RSSFACTORY_FEEDS_TITLE'); ?></th>
    <!-- Add other columns as needed, and use HTMLHelper if required -->
</tr>
<tr>
    <th width="1%" class="text-center">
        <?php echo HTMLHelper::_('grid.checkall'); ?>
    </th>
    <th class="text-center">
        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'f.published', $listDirn, $listOrder); ?>
    </th>
    <th class="text-center">
        <?php echo Text::_('COM_RSSFACTORY_FEEDS_ICON'); ?>
    </th>
    <th>
        <?php echo HTMLHelper::_('searchtools.sort', 'COM_RSSFACTORY_FEEDS_TITLE', 'f.title', $listDirn, $listOrder); ?>
    </th>
    <th class="text-center">
        <?php echo Text::_('COM_RSSFACTORY_FEEDS_STORIES'); ?>
    </th>
    <th class="text-center">
        <?php echo Text::_('COM_RSSFACTORY_FEEDS_LAST_REFRESH'); ?>
    </th>
    <th class="text-center">
        <?php echo Text::_('COM_RSSFACTORY_FEEDS_I2C'); ?>
    </th>
    <th class="text-center">
        <?php echo Text::_('COM_RSSFACTORY_FEEDS_ERROR'); ?>
    </th>
    <th width="1%" class="text-center">
        <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'f.id', $listDirn, $listOrder); ?>
    </th>
</tr>
</thead>
