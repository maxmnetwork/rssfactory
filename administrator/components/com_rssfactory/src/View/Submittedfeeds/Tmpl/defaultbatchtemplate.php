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

namespace Joomla\Component\Rssfactory\Administrator\View\Submittedfeeds\Tmpl;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>
<div class="modal fade" id="collapseModal" tabindex="-1" role="dialog" aria-labelledby="batchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchModalLabel"><?php echo FactoryTextRss::_('submittedfeeds_batch_options'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo Text::_('JCLOSE'); ?>"></button>
            </div>
            <div class="modal-body p-3">
                <p><?php echo FactoryTextRss::_('submittedfeeds_batch_tip'); ?></p>
                <div class="form-group">
                    <label id="batch-category-lbl" for="batch-category-id"><?php echo FactoryTextRss::_('submittedfeeds_batch_category'); ?></label>
                    <select name="batch[category_id]" class="form-control custom-select" id="batch-category-id">
                        <option value=""><?php echo Text::_('JSELECT'); ?></option>
                        <?php echo HTMLHelper::_('select.options', HTMLHelper::_('category.options', 'com_rssfactory'), 'value', 'text'); ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('JCANCEL'); ?></button>
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('submittedfeed.batch')"><?php echo FactoryTextRss::_('submittedfeeds_batch_process'); ?></button>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
