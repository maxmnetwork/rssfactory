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

namespace Joomla\Component\Rssfactory\Administrator\View\Comment\Tmpl;

defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

// Render all fields in the given fieldset using Joomla 4 markup.
?>
<fieldset id="fieldset-<?php echo $this->fieldset; ?>"
          class="form-<?php echo isset($this->fieldsets[$this->fieldset]->display) ? $this->fieldsets[$this->fieldset]->display : 'horizontal'; ?>">
    <legend><?php echo FactoryTextRss::_('configuration_fieldset_' . $this->fieldset); ?></legend>

    <?php foreach ($this->form->getFieldset($this->fieldset) as $field): ?>
        <?php $display = $this->form->getFieldAttribute($field->fieldname, 'display', 'horizontal', $field->group); ?>
        <?php if ('vertical' == $display): ?>
            <div class="form-vertical">
        <?php endif; ?>

        <div class="mb-3 row">
            <?php if ('' != $field->label): ?>
                <label class="col-form-label col-sm-3"><?php echo $field->label; ?></label>
            <?php endif; ?>
            <div class="col-sm-9"><?php echo $field->input; ?></div>
        </div>

        <?php if ('vertical' == $display): ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</fieldset>
