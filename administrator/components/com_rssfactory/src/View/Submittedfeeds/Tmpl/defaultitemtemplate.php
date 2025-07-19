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
<tr class="row<?php echo $this->i % 2; ?>">
    <td class="center d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.id', $this->i, $this->item->id); ?>
    </td>

    <td class="nowrap has-context">
        <?php echo $this->escape($this->item->title); ?>

        <div class="small">
            <?php echo Text::_($this->item->comment); ?>
        </div>
    </td>

    <td class="d-none d-md-table-cell small">
        <a href="<?php echo $this->item->url; ?>" target="_blank">
            <?php echo HTMLHelper::_('string.truncate', $this->escape($this->item->url), 40); ?>
        </a>
    </td>

    <td class="d-none d-md-table-cell small">
        <?php echo HTMLHelper::_('date', $this->item->date, Text::_('DATE_FORMAT_LC2')); ?>
    </td>

    <td class="center d-none d-md-table-cell">
        <?php echo $this->item->id; ?>
    </td>
</tr>
