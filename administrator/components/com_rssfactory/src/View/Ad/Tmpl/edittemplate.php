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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Bootstrap 5 form layout for Joomla 4
?>
<form action="<?php echo $this->escape($this->form->getAction()); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate form-horizontal">

    <?php echo $this->loadTemplate('edit_fieldset'); ?>

    <div class="clearfix"></div>

    <div class="btn-toolbar" role="toolbar" aria-label="<?php echo Text::_('JTOOLBAR'); ?>">
        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <span class="icon-save"></span> <?php echo Text::_('JSAVE'); ?>
            </button>
            <button type="button" class="btn btn-secondary" onclick="Joomla.submitbutton('ad.cancel')">
                <span class="icon-cancel"></span> <?php echo Text::_('JCANCEL'); ?>
            </button>
        </div>
    </div>

    <?php echo HTMLHelper::_('form.token'); ?>
</form>
