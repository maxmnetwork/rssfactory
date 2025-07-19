<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

?>
<script type="text/javascript">
    Joomla.orderTable = function () {
        var table = document.getElementById("sortTable");
        var direction = document.getElementById("directionTable");
        var order = table.options[table.selectedIndex].value;
        var dirn;
        
        // Check the current order and decide the direction
        if (order !== '<?php echo $this->listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }

        // Trigger the table ordering method
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<form action="<?php echo Route::_('index.php?option=' . $this->option . '&view=' . $this->getName()); ?>" method="post" name="adminForm" id="adminForm">

    <div class="container-fluid">
        <div class="row">
            <?php echo $this->loadTemplate('sidebar'); ?>

            <div id="j-main-container" class="<?php echo !empty($this->sidebar) ? 'col-10' : 'col-12'; ?>">

                <?php echo $this->loadTemplate('filter'); ?>

                <table class="table table-striped" id="articleList">
                    <?php echo $this->loadTemplate('head'); ?>
                    <?php echo $this->loadTemplate('body'); ?>
                </table>

                <!-- Pagination Template -->
                <?php echo $this->loadTemplate('pagination'); ?>

                <!-- Batch Template -->
                <?php echo $this->loadTemplate('batch'); ?>

                <!-- Hidden Inputs -->
                <?php echo $this->loadTemplate('hidden'); ?>
            </div>
        </div>
    </div>

</form>
