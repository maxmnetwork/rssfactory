<?php
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

?>
<form action="" method="post" name="adminForm" id="adminForm">
    <div class="container-fluid">
        <div class="row">
            <?php if (!empty($this->sidebar)): ?>
                <div id="j-sidebar-container" class="col-2">
                    <?php echo $this->sidebar; ?>
                </div>
            <?php endif; ?>
            <div id="j-main-container" class="<?php echo !empty($this->sidebar) ? 'col-10' : 'col-12'; ?>">
                <?php echo $this->loadTemplate('filter'); ?>
                <table class="table table-striped" id="articleList">
                    <?php echo $this->loadTemplate('head'); ?>
                    <?php echo $this->loadTemplate('body'); ?>
                </table>
                <?php echo $this->loadTemplate('pagination'); ?>
                <?php echo $this->loadTemplate('hidden'); ?>
            </div>
        </div>
    </div>
</form>
