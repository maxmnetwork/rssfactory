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

namespace Joomla\Component\Rssfactory\Administrator\View\Feeds\Tmpl;

defined('_JEXEC') or die;
echo $this->pagination->getListFooter();
if ($this->pagination) :
    echo $this->pagination->getListFooter();
    ?>
    <div class="pagination">
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>
