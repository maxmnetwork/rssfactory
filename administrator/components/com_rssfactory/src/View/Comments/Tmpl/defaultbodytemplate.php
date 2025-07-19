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

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern
// Ensure Bootstrap 5 compatibility
?>
<tbody>
<?php foreach ($this->items as $i => $item): ?>
    <?php $this->i = $i; $this->item = $item; ?>
    <?php echo $this->loadTemplate('item'); ?>
<?php endforeach; ?>
</tbody>
