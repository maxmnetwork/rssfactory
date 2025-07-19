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

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_rssfactory.admin');

/** @var \Joomla\CMS\MVC\View\HtmlView $this */
$items = $this->get('Items');
$pagination = $this->get('Pagination');
?>

<div class="com-rssfactory-comment-view">
    <h1><?php echo Text::_('COM_RSSFACTORY_COMMENTS'); ?></h1>

    <?php if (!empty($items)) : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
                    <th><?php echo Text::_('COM_RSSFACTORY_COMMENT_AUTHOR'); ?></th>
                    <th><?php echo Text::_('COM_RSSFACTORY_COMMENT_TEXT'); ?></th>
                    <th><?php echo Text::_('JDATE'); ?></th>
                    <th><?php echo Text::_('JGRID_HEADING_ACTIONS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item) : ?>
                    <tr>
                        <td><?php echo (int) $item->id; ?></td>
                        <td><?php echo htmlspecialchars($item->author, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($item->comment, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC2')); ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="<?php echo Route::_('index.php?option=com_rssfactory&task=comment.edit&id=' . (int) $item->id); ?>">
                                <?php echo Text::_('JACTION_EDIT'); ?>
                            </a>
                            <a class="btn btn-sm btn-danger" href="<?php echo Route::_('index.php?option=com_rssfactory&task=comment.delete&id=' . (int) $item->id . '&' . Factory::getApplication()->getFormToken() . '=1'); ?>" onclick="return confirm('<?php echo Text::_('JGLOBAL_CONFIRM_DELETE'); ?>');">
                                <?php echo Text::_('JACTION_DELETE'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php echo $pagination->getListFooter(); ?>

    <?php else : ?>
        <div class="alert alert-info">
            <?php echo Text::_('COM_RSSFACTORY_NO_COMMENTS_FOUND'); ?>
        </div>
    <?php endif; ?>
</div>