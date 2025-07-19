<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Backup\Tmpl;

defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>

<div class="tab-pane active" id="backup">
    <fieldset class="uploadform">
        <legend><?php echo FactoryTextRss::_('backup_tab_backup_title'); ?></legend>

        <div class="mb-3"><?php echo FactoryTextRss::_('backup_tab_backup_info'); ?></div>
        <div class="form-actions">
            <input class="btn btn-primary" type="button"
                   value="<?php echo FactoryTextRss::_('backup_tab_restore_generate_backup'); ?>"
                   onclick="Joomla.submitbutton('backup.generate');"/>
        </div>
    </fieldset>
</div>
