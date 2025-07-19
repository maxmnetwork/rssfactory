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

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\View\About\Tmpl;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<table>
    <tr>
        <td><?php echo Text::_('ABOUT_YOUR_VERSION'); ?>:</td>
        <td><?php echo $this->current_version; ?></td>
    </tr>
    <tr>
        <td><?php echo Text::_('ABOUT_LATEST_VERSION'); ?>:</td>
        <td><?php echo isset($this->information['latestversion']) ? $this->information['latestversion'] : 'n/a'; ?></td>
    </tr>
    <tr>
        <td colspan="2"
            style="color: #<?php echo !$this->new_version ? '000000' : 'ff0000'; ?>; font-weight: bold; padding-top: 10px;">
            <?php echo Text::_($this->new_version ? 'ABOUT_NEW_VERSION_AVAILABLE' : 'ABOUT_YOU_HAVE_LATEST_VERSION'); ?>
        </td>
    </tr>
</table>
