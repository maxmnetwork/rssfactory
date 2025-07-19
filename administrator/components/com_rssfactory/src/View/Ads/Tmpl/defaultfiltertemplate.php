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

namespace Joomla\Component\Rssfactory\Administrator\View\Ads\Tmpl;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

?>

<form action="<?php echo Route::_('index.php?option=com_rssfactory&view=ads'); ?>" method="post" name="adminForm" id="adminForm" class="mb-3">
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <div id="filter-bar" class="btn-toolbar">

                <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="visually-hidden"><?php echo FactoryTextRss::_('filter_search_desc'); ?></label>
                    <input type="text" name="filter_search" placeholder="<?php echo FactoryTextRss::_('filter_search_desc'); ?>"
                           id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                           title="<?php echo FactoryTextRss::_('filter_search_desc'); ?>"/>
                </div>

                <div class="btn-group pull-left d-none d-md-inline">
                    <button class="btn btn-outline-secondary" type="submit" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
                    <button class="btn btn-outline-secondary" type="button"
                            onclick="document.getElementById('filter_search').value='';this.form.submit();"
                            title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>"><span class="icon-remove"></span></button>
                </div>

                <div class="btn-group pull-right d-none d-md-inline">
                    <label for="limit"
                           class="visually-hidden"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>

                <div class="btn-group pull-right d-none d-md-inline">
                    <label for="directionTable" class="visually-hidden"><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></label>
                    <select name="directionTable" id="directionTable" class="form-select" onchange="Joomla.orderTable()">
                        <option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
                        <option
                            value="asc" <?php if ($this->listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo Text::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                        <option
                            value="desc" <?php if ($this->listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo Text::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
                    </select>
                </div>

                <div class="btn-group pull-right">
                    <label for="sortTable" class="visually-hidden"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
                    <select name="sortTable" id="sortTable" class="form-select" onchange="Joomla.orderTable()">
                        <option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
                        <?php echo HTMLHelper::_('select.options', $this->sortFields, 'value', 'text', $this->listOrder); ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
        </div>
    </div>
</form>

<div class="clearfix"></div>
