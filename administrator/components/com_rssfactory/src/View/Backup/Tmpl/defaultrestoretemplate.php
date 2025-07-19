<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Backup\Tmpl;

defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>

<div class="tab-pane" id="restore">
    <fieldset class="uploadform">
        <legend><?php echo FactoryTextRss::_('backup_tab_restore_title'); ?></legend>

        <div class="mb-3"><?php echo FactoryTextRss::_('backup_tab_restore_info'); ?></div>

        <div class="mb-3">
            <label for="restore_archive"
                   class="form-label"><?php echo FactoryTextRss::_('backup_tab_restore_select_file'); ?></label>

            <div>
                <input class="form-control" id="restore_archive" name="restore_archive" type="file" size="57"/>
            </div>
        </div>

        <div class="form-actions">
            <input class="btn btn-primary" type="button"
                   value="<?php echo FactoryTextRss::_('backup_tab_restore_upload_restore'); ?>"
                   onclick="Joomla.submitbutton('backup.restore');"/>
        </div>
    </fieldset>
</div>
