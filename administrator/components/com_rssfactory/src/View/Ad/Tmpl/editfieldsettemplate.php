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

namespace Joomla\Component\Rssfactory\Administrator\View\Ad\Tmpl;

defined('_JEXEC') or die;

?>

<fieldset>
    <legend><?php echo $this->form->getLabel('details'); ?></legend>
    <div class="row g-3">
        <?php foreach ($this->form->getFieldset('details') as $field): ?>
            <div class="col-12">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </div>
        <?php endforeach; ?>
    </div>
</fieldset>
