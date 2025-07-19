<?php

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

namespace Joomla\Component\Rssfactory\Administrator\View\Submittedfeeds\Tmpl;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<thead>
<tr>
    <th width="1%" class="d-none d-md-table-cell">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
               onclick="Joomla.checkAll(this)"/>
    </th>

    <th>
        <?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'f.title', $this->listDirn, $this->listOrder); ?>
    </th>

    <th width="25%" class="nowrap d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.sort', 'COM_RSSFACTORY_SUBMITTEDFEEDS_LIST_URL', 'f.url', $this->listDirn, $this->listOrder); ?>
    </th>

    <th width="20%" class="nowrap d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.sort', 'COM_RSSFACTORY_SUBMITTEDFEEDS_LIST_DATE', 'f.date', $this->listDirn, $this->listOrder); ?>
    </th>

    <th width="1%" class="nowrap d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'f.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
</thead>
