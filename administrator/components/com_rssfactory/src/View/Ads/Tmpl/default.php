<?php
defined('_JEXEC') or die;
use Joomla\CMS\Router\Route;

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

?>
<script type="text/javascript">
    Joomla.orderTable = function () {
        var table = document.getElementById("sortTable");
        var direction = document.getElementById("directionTable");
        var order = table.options[table.selectedIndex].value;
        var dirn;
        if (order != '<?php echo $this->listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<form action="<?php echo Route::_('index.php?option=' . $this->option . '&view=' . $this->getName()); ?>" method="post"
      name="adminForm" id="adminForm">

    <div class="container-fluid">
        <div class="row">
            <?php if (!empty($this->sidebar)): ?>
                <div id="j-sidebar-container" class="col-2">
                    <?php echo $this->sidebar; ?>
                </div>
            <?php endif; ?>
            <div id="j-main-container" class="<?php echo !empty($this->sidebar) ? 'col-10' : 'col-12'; ?>">
                <?php
                // Render filter bar
                echo $this->loadTemplate('default_filter');
                // Render table head
                echo $this->loadTemplate('default_head');
                // Render table body
                echo $this->loadTemplate('default_body');
                // Render hidden fields
                echo $this->loadTemplate('default_hidden');
                // Render pagination
                echo $this->loadTemplate('default_pagination');
                ?>
            </div>
        </div>
    </div>

</form>
