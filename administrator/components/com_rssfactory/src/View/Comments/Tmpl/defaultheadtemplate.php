<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Comments\Tmpl;

defined('_JEXEC') or die;

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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<thead>
<tr>
    <th width="1%" class="d-none d-md-table-cell">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
               onclick="Joomla.checkAll(this)"/>
    </th>

    <th width="1%" style="min-width:55px" class="nowrap text-center">
        <?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'c.published', $this->listDirn, $this->listOrder); ?>
    </th>

    <th>
        <?php echo HTMLHelper::_('grid.sort', 'COM_RSSFACTORY_COMMENTS_LIST_TEXT', 'c.text', $this->listDirn, $this->listOrder); ?>
    </th>

    <th width="15%" class="nowrap d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.sort', 'COM_RSSFACTORY_COMMENTS_LIST_STORY', 'cache.item_title', $this->listDirn, $this->listOrder); ?>
    </th>

    <th width="15%" class="nowrap d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.sort', 'COM_RSSFACTORY_COMMENTS_LIST_USERNAME', 'u.username', $this->listDirn, $this->listOrder); ?>
    </th>

    <th width="20%" class="nowrap d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.sort', 'COM_RSSFACTORY_COMMENTS_LIST_CREATED_AT', 'c.created_at', $this->listDirn, $this->listOrder); ?>
    </th>

    <th width="1%" class="nowrap d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'c.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
</thead>
