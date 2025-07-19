<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 *
 * @author      thePHPfactory
 * @copyright   Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Rssfactory\Administrator\View\Feeds\Tmpl;

// DEBUG: Feeds/Tmpl/defaultbodytemplate.php loaded
echo '<!-- DEBUG: Feeds/Tmpl/defaultbodytemplate.php loaded -->';

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;


?>
<tbody>
<?php if (!empty($this->items)) : ?>
    <?php foreach ($this->items as $i => $item) : ?>
        <tr>
            <td><?php echo $item->id; ?></td>
            <td><?php echo $item->title; ?></td>
            <!-- Add other columns as needed -->
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr>
        <td colspan="9" class="text-center">
            <?php echo Text::_('COM_RSSFACTORY_FEEDS_NO_ITEMS'); ?>
        </td>
    </tr>
<?php endif; ?>
</tbody>
