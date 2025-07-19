<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Feed\Tmpl;

defined('_JEXEC') or die;

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
// No namespace or use statements needed for tmpl files

use Joomla\CMS\Router\Route;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<form action="<?php echo Route::_('index.php?option=' . $this->option . '&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate row g-3">

    <?php echo HTMLHelper::_('bootstrap.startTabSet', 'feed', ['active' => 'details']); ?>
        <?php echo HTMLHelper::_('bootstrap.addTab', 'feed', 'details', Text::_('JDETAILS')); ?>
            <div class="row">
                <div class="col-6">
                    <?php echo $this->loadFieldset('details'); ?>
                    <?php echo $this->loadFieldset('http'); ?>
                    <?php echo $this->loadFieldset('ftp'); ?>
                </div>
                <div class="col-6">
                    <?php echo $this->loadFieldset('filter'); ?>
                </div>
            </div>
        <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

        <?php $notice = '<span class="badge bg-danger">' . FactoryTextRss::_('pro_version_notice') . '</span>'; ?>
        <?php $label = FactoryTextRss::_('form_feed_fieldset_import2content') . (!$this->form->getFieldset('import2content_details') ? '&nbsp;' . $notice : null); ?>
        <?php echo HTMLHelper::_('bootstrap.addTab', 'feed', 'import2content', $label); ?>
            <?php if ($this->form->getFieldset('import2content_details')): ?>
                <div class="row">
                    <div class="col-6">
                        <?php echo $this->loadFieldset('import2content_details'); ?>
                        <?php echo $this->loadFieldset('import2content_filter'); ?>
                    </div>
                    <div class="col-6">
                        <?php echo $this->loadFieldset('import2content_relevant_stories'); ?>
                        <?php echo $this->loadFieldset('import2content_publishing'); ?>
                    </div>
                </div>
            <?php else: ?>
                <?php echo FactoryTextRss::_('feature_available_in_pro_version'); ?>
            <?php endif; ?>
        <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

        <?php $notice = '<span class="badge bg-danger">' . FactoryTextRss::_('pro_version_notice') . '</span>'; ?>
        <?php $label = FactoryTextRss::_('form_feed_fieldset_import2content_rules') . (!$this->form->getFieldset('import2content_details') ? '&nbsp;' . $notice : null); ?>
        <?php echo HTMLHelper::_('bootstrap.addTab', 'feed', 'import2content_rules', $label); ?>
            <?php if ($this->form->getFieldset('import2content_details')): ?>
                <div class="row">
                    <div class="col-6">
                        <?php echo $this->loadFieldset('import2content_rules_details'); ?>
                        <?php echo $this->loadFieldset('import2content_rules_preview'); ?>
                    </div>
                    <div class="col-6">
                        <?php echo $this->loadFieldset('import2content_rules'); ?>
                    </div>
                </div>
            <?php else: ?>
                <?php echo FactoryTextRss::_('feature_available_in_pro_version'); ?>
            <?php endif; ?>
        <?php echo HTMLHelper::_('bootstrap.endTab'); ?>
    <?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php echo $this->loadTemplate('preview'); ?>
