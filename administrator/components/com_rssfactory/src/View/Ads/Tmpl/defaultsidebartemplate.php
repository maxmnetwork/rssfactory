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

// Render the sidebar if present, using Bootstrap 5 classes.
?>
<?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="col-2">
        <?php echo $this->sidebar; ?>
    </div>
<?php endif; ?>
