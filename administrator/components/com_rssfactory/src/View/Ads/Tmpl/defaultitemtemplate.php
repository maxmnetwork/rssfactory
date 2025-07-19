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

namespace Joomla\Component\Rssfactory\Administrator\View\Ads\Tmpl;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>

<tr class="row<?php echo $this->i % 2; ?>">
    <td class="center d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.id', $this->i, $this->item->id); ?>
    </td>

    <td class="center">
        <div class="btn-group">
            <?php echo HTMLHelper::_('jgrid.published', $this->item->published, $this->i, 'ads.', true); ?>
        </div>
    </td>

    <td class="nowrap has-context">
        <a href="<?php echo Route::_('index.php?option=' . $this->option . '&task=ad.edit&id=' . $this->item->id); ?>"
           title="<?php echo Text::_('JACTION_EDIT'); ?>">
            <?php echo $this->escape($this->item->title); ?>
        </a>
    </td>

    <td class="small d-none d-md-table-cell">
        <?php if ($this->item->categories): ?>
            <?php echo $this->escape($this->item->categories); ?>
        <?php else: ?>
            <?php echo FactoryTextRss::_('ads_list_all_categories'); ?>
        <?php endif; ?>
    </td>

    <td class="center d-none d-md-table-cell">
        <?php echo $this->item->id; ?>
    </td>
</tr>
