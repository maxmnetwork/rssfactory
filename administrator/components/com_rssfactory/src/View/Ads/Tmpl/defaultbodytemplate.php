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

// Render table body for ads list using Joomla 4 best practices.
?>
<tbody>
<?php foreach ($this->items as $i => $item): ?>
    <tr>
        <?php foreach ($this->columns as $column): ?>
            <td>
                <?php echo isset($item->{$column['name']}) ? $item->{$column['name']} : ''; ?>
            </td>
        <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
</tbody>
