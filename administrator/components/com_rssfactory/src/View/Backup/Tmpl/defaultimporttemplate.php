<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Backup\Tmpl;

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

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>

<div class="tab-pane" id="import">
    <fieldset class="uploadform">
        <legend><?php echo FactoryTextRss::_('backup_tab_import_title'); ?></legend>

        <div class="mb-3"><?php echo FactoryTextRss::_('backup_tab_import_info'); ?></div>

        <div class="mb-3">
            <label for="import_file"
                   class="form-label"><?php echo FactoryTextRss::_('backup_tab_import_select_file'); ?></label>

            <div>
                <input class="form-control" id="import_file" name="import_file" type="file" size="57"/>
            </div>
        </div>

        <div class="mb-3">
            <label id="import_separator"
                   for="import_separator" class="form-label"><?php echo FactoryTextRss::_('backup_tab_import_separator'); ?></label>
            <div>
                <?php echo HTMLHelper::_(
                    'select.genericlist',
                    array(
                        'TAB' => FactoryTextRss::_('backup_tab_import_separator_tab'),
                        ';'   => FactoryTextRss::_('backup_tab_import_separator_semicolon'),
                        ','   => FactoryTextRss::_('backup_tab_import_separator_comma'),
                    ),
                    'import_separator'
                ); ?>
            </div>
        </div>

        <div class="form-actions">
            <input class="btn btn-primary" type="button"
                   value="<?php echo FactoryTextRss::_('backup_tab_import_upload_restore'); ?>"
                   onclick="Joomla.submitbutton('backup.import');"/>
        </div>
    </fieldset>
</div>
