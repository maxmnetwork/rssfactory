<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Comments\Tmpl;

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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$feedSources = $this->feedSources;
$pageNav = $this->pageNav;

global $option;
$nrRows = count($feedSources);

HTMLHelper::_('bootstrap.tooltip');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

    <input type="hidden" name="option" value="<?php echo $option; ?>"/>
    <input type="hidden" name="task" value="managerss"/>
    <input type="hidden" name="boxchecked" value="0"/>

    <input type="hidden" name="resetFilters" value="0"/>
    <input type="hidden" name="changedPublishedStatus" value=""/>

    <div id="div_progress"
         style="margin-left:45%; display:none;color:white;width:100px;background-color:red; text-align:center;">
        Processing request...
    </div>
    <table class="adminheading">
        <tr>
            <td style="width:30%">
                <?php echo HTMLHelper::_('manageSources.refreshAllSourcesIcon'); ?>
            </td>
            <td style="width:50%"></td>
            <td align="right" valign="top">
                <?php echo RFPRO_WORD4FILTER ?>
            </td>
            <td align="right" valign="top">
                <?php echo HTMLHelper::_('manageSources.filterSearch'); ?>
            </td>
            <td align="right" valign="top">
                <?php echo HTMLHelper::_('manageSources.filterCategory', $this->aCategories); ?>
            </td>
            <td align="right" valign="top">
                <?php echo HTMLHelper::_('manageSources.filterPublished'); ?>
            </td>
            <td align="right" valign="top">
                <?php echo HTMLHelper::_('manageSources.filterReset'); ?>
            </td>
        </tr>
    </table>
    <table class="adminlist">
        <tr>
            <th width="20" align="center">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
            </th>
            <th width="16px" nowrap="nowrap" align="left">
                Id:&nbsp;<?php echo HTMLHelper::_('tooltip', 'Use this id for the replace plugin!'); ?>
            </th>
            <th width="5%" nowrap="nowrap" align="center"><?php echo Text::_('JPUBLISHED'); ?></th>
            <th width="5%" colspan="2" align="center"><?php echo RFPRO_WORD4ORDER ?></th>
            <th width="16px" nowrap="nowrap" align="left"><?php echo RFPRO_WORD4ICO ?></th>
            <th width="15%" nowrap="nowrap" align="left"><?php echo RFPRO_WORD4TITLE ?></th>
            <th width="10%" nowrap="nowrap" align="left"><?php echo RFPRO_WORD4CATEGORY ?></th>
            <th width="6%" nowrap="nowrap"><?php echo RFPRO_WORD4NRFEEDS ?></th>
            <th colspan="*%"><?php echo RFPRO_WORD4URL ?></th>
            <th width="10%" nowrap="nowrap"><?php echo RFPRO_WORD4LASTREFRESH ?></th>
            <th width="4%" nowrap="nowrap"><?php echo RFPRO_WORD4HADERROR ?></th>
        </tr>
        <?php
        $k = 0;
        $i = 0;
        foreach ($feedSources as $row) {
            $pub = $row->published ? 0 : 1;
            ?>
            <tr class="row<?php echo $k; ?>">
                <td align="center">
                    <?php echo HTMLHelper::_('grid.id', $i, $row->id, false); ?>
                </td>
                <td align="center">
                    <?php echo $row->id; ?>
                </td>
                <td align="center">
                    <?php echo HTMLHelper::_('grid.published', $row->published, $i); ?>
                </td>
                <td width="10" align="center">
                    <?php echo $pageNav->orderUpIcon($i, $i < $nrRows, 'orderupsource'); ?>
                </td>
                <td width="10" align="center">
                    <?php echo $pageNav->orderDownIcon($i, $nrRows, $i > -1, 'orderdownsource'); ?>
                </td>
                <td>
                    <?php echo HTMLHelper::_('manageSources.createSiteIco', $row); ?>
                </td>
                <td>
                    <a href="index.php?option=<?php echo $option . '&task=editSourceFeed&id=' . $row->id; ?>"><?php echo $row->title; ?></a>
                </td>
                <td>
                    <?php echo HTMLHelper::_('manageSources.stringCategoryName', $row); ?>
                </td>
                <td style="text-align: right;">
                    <?php echo HTMLHelper::_('manageSources.refreshSourceIcon', $row); ?>
                </td>
                <td>
                    <?php echo HTMLHelper::_('manageSources.sourceLocation', $row) ?>
                </td>
                <td>
                    <span id="refreshDate_<?php echo $row->id ?>"><?php echo $row->date; ?></span>
                </td>
                <td style="text-align: center;">
                    <?php echo HTMLHelper::_('manageSources.refreshError', $row); ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
            $i++;
        }
        ?>
    </table>
    <br/>
    <?php echo $pageNav->getListFooter(); ?>
</form>
