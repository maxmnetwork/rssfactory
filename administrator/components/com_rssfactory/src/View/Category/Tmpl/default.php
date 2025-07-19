<?php
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * @package     Com_Rssfactory
 * @subpackage  View
 * @copyright   Copyright (C) 2024
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


/** @var \Joomla\CMS\Mvc\View\HtmlView $this */
/** @var array $this->items */
/** @var \Joomla\CMS\Pagination\Pagination $this->pagination */

$app    = Factory::getApplication();
$user   = Factory::getUser();
$canEdit   = $user->authorise('core.edit', 'com_rssfactory');
$canCreate = $user->authorise('core.create', 'com_rssfactory');
$canDelete = $user->authorise('core.delete', 'com_rssfactory');

?>

<div class="com-rssfactory-category-list">
    <h1><?php echo Text::_('COM_RSSFACTORY_CATEGORIES'); ?></h1>

    <?php if ($canCreate) : ?>
        <a class="btn btn-success" href="<?php echo Route::_('index.php?option=com_rssfactory&task=category.add'); ?>">
            <?php echo Text::_('COM_RSSFACTORY_ADD_CATEGORY'); ?>
        </a>
    <?php endif; ?>

    <?php if (!empty($this->items)) : ?>
        <form action="<?php echo Route::_('index.php?option=com_rssfactory&view=category'); ?>" method="post" name="adminForm" id="adminForm">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="1%"><?php echo Text::_('JGLOBAL_NUM'); ?></th>
                        <th><?php echo Text::_('COM_RSSFACTORY_CATEGORY_TITLE'); ?></th>
                        <th width="10%"><?php echo Text::_('JSTATUS'); ?></th>
                        <?php if ($canEdit || $canDelete) : ?>
                            <th width="10%"><?php echo Text::_('JACTION'); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $i => $item) : ?>
                        <tr>
                            <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                            <td>
                                <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td>
                                <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'categories.', true, 'cb'); ?>
                            </td>
                            <?php if ($canEdit || $canDelete) : ?>
                                <td>
                                    <?php if ($canEdit) : ?>
                                        <a href="<?php echo Route::_('index.php?option=com_rssfactory&task=category.edit&id=' . (int) $item->id); ?>" class="btn btn-sm btn-primary">
                                            <?php echo Text::_('JACTION_EDIT'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($canDelete) : ?>
                                        <a href="<?php echo Route::_('index.php?option=com_rssfactory&task=category.delete&id=' . (int) $item->id); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo Text::_('JGLOBAL_CONFIRM_DELETE'); ?>');">
                                            <?php echo Text::_('JACTION_DELETE'); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php echo $this->pagination->getListFooter(); ?>
        </form>
    <?php else : ?>
        <div class="alert alert-info">
            <?php echo Text::_('COM_RSSFACTORY_NO_CATEGORIES_FOUND'); ?>
        </div>
    <?php endif; ?>
</div>