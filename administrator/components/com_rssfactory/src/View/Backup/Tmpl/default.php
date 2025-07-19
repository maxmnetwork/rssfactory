<?php
defined('_JEXEC') or die;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>

<form class="form-horizontal"
      action="<?php echo Route::_('index.php?option=' . $this->option . '&view=' . $this->getName()); ?>" method="post"
      name="adminForm" id="adminForm" enctype="multipart/form-data">

    <div class="row">
        <?php echo $this->loadTemplate('sidebar'); ?>

        <div id="j-main-container" class="<?php echo !empty($this->sidebar) ? 'col-10' : 'col-12'; ?>">

            <fieldset>
                <?php echo HTMLHelper::_('bootstrap.startTabSet', 'backup', ['active' => 'backup']); ?>
                    <?php echo HTMLHelper::_('bootstrap.addTab', 'backup', 'backup', FactoryTextRss::_('backup_tab_label_backup')); ?>
                        <?php echo $this->loadTemplate('backup'); ?>
                    <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

                    <?php echo HTMLHelper::_('bootstrap.addTab', 'backup', 'restore', FactoryTextRss::_('backup_tab_label_restore')); ?>
                        <?php echo $this->loadTemplate('restore'); ?>
                    <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

                    <?php echo HTMLHelper::_('bootstrap.addTab', 'backup', 'import', FactoryTextRss::_('backup_tab_label_import')); ?>
                        <?php echo $this->loadTemplate('import'); ?>
                    <?php echo HTMLHelper::_('bootstrap.endTab'); ?>
                <?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
            </fieldset>

        </div>

        <input type="hidden" name="task" value=""/>
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
