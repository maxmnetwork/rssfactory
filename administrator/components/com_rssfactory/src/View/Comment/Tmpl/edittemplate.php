<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Comment\Tmpl;

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

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern
// Ensure Bootstrap 5 compatibility

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

?>
<form action="<?php echo Route::_('index.php?option=' . $this->option . '&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate row g-3">
    <input type="hidden" name="jform[categories_assigned]" value=""/>

    <div class="col-md-10">
        <?php echo $this->loadFieldset('details'); ?>
    </div>

    <div class="col-md-2">
        <h4><?php echo Text::_('JDETAILS'); ?></h4>
        <hr/>

        <fieldset class="form-vertical">
            <div class="mb-3">
                <div><?php echo $this->form->getValue('title'); ?></div>
            </div>
            <?php foreach ($this->form->getFieldset('sidebar') as $field): ?>
                <div class="mb-3">
                    <label class="form-label"><?php echo $field->label; ?></label>
                    <div><?php echo $field->input; ?></div>
                </div>
            <?php endforeach; ?>
        </fieldset>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>
