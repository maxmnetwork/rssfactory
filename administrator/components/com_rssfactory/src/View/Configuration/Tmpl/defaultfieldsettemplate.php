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

defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>
<fieldset
    class="form-<?php echo isset($this->fieldsets[$this->fieldset]->display) ? $this->fieldsets[$this->fieldset]->display : 'horizontal'; ?>">
    <legend><?php echo FactoryTextRss::_('configuration_fieldset_' . $this->fieldset); ?></legend>

    <?php foreach ($this->form->getFieldset($this->fieldset) as $field): ?>
        <?php $display = $this->form->getFieldAttribute($field->fieldname, 'display', 'horizontal', $field->group); ?>
        <?php if ('vertical' == $display): ?>
            <div class="form-vertical">
        <?php endif; ?>

        <?php echo $field->renderField(); ?>

        <?php if ('vertical' == $display): ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</fieldset>
