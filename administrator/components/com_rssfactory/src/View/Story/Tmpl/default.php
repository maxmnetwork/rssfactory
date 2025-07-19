<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @author      thePHPfactory
 * @copyright   Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Get items and pagination from the view/model
$items = $this->get('Items');
$pagination = $this->get('Pagination');
?>

<form action="<?php echo Route::_('index.php?option=com_rssfactory&view=story'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="container-fluid">
        <div class="row">
            <?php if (!empty($this->sidebar)) : ?>
                <div id="j-sidebar-container" class="col-2">
                    <?php echo $this->sidebar; ?>
                </div>
            <?php endif; ?>

            <div id="j-main-container" class="<?php echo !empty($this->sidebar) ? 'col-10' : 'col-12'; ?>">
                <table class="table table-striped" id="storyList">
                    <thead>
                        <tr>
                            <th width="1%" class="text-center"><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
                            <th><?php echo Text::_('COM_RSSFACTORY_STORY_TITLE'); ?></th>
                            <th class="text-center"><?php echo Text::_('COM_RSSFACTORY_STORY_DATE'); ?></th>
                            <th class="text-center"><?php echo Text::_('JSTATUS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)) : ?>
                            <?php foreach ($items as $i => $item) : ?>
                                <tr>
                                    <td class="text-center"><?php echo (int) $item->id; ?></td>
                                    <td><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-center"><?php echo HTMLHelper::_('date', $item->item_date, Text::_('DATE_FORMAT_LC2')); ?></td>
                                    <td class="text-center"><?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'stories.', true, 'cb'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <?php echo Text::_('COM_RSSFACTORY_NO_STORIES_FOUND'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($pagination) : ?>
                    <div class="pagination">
                        <?php echo $pagination->getListFooter(); ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="task" value="" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
