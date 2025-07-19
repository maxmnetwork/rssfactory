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

namespace Joomla\Component\Rssfactory\Administrator\View\Feed\Tmpl;

defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>
<!-- Bootstrap 5 modal for preview -->
<div class="modal fade" id="collapseModal4" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 90%; left: 5%; margin: 0; top: 5%; height: 90%; max-width: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel"><?php echo FactoryTextRss::_('feed_preview_modal_title'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 20px;"></div>
        </div>
    </div>
</div>
                <h5 class="modal-title"><?php echo FactoryTextRss::_('feed_preview_modal_title'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;"></div>
        </div>
    </div>
</div>
