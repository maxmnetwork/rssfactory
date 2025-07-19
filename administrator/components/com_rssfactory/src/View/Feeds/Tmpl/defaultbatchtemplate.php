<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @author      thePHPfactory
 * @copyright   Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Rssfactory\Administrator\View\Feeds\Tmpl;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$user = Factory::getApplication()->getIdentity();
if ($user->authorise('core.edit', 'com_rssfactory')) :
?>
<div class="modal fade" id="collapseModal" tabindex="-1" aria-labelledby="batchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= Route::_('index.php?option=com_rssfactory&task=feeds.batch'); ?>" method="post" id="batch-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="batchModalLabel"><?= Text::_('JTOOLBAR_BATCH'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= Text::_('JCLOSE'); ?>"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="batch-category-id" class="form-label"><?= Text::_('JCATEGORY'); ?></label>
                        <select name="batch[category_id]" id="batch-category-id" class="form-select">
                            <?= HTMLHelper::_('select.options', $this->categories, 'value', 'text'); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= Text::_('JCANCEL'); ?></button>
                    <button type="submit" class="btn btn-primary"><?= Text::_('JAPPLY'); ?></button>
                </div>
                <?= HTMLHelper::_('form.token'); ?>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
