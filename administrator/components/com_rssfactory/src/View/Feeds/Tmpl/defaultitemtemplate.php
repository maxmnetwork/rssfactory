<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Rssfactory\Administrator\Helper\Html\Feeds as FeedsHelper;

$item = $this->item;
$i = $this->i;
$user = $this->user ?? Joomla\CMS\Factory::getApplication()->getIdentity();
$canEdit    = $user->authorise('core.edit', 'com_rssfactory.feed.' . $item->id);
$canChange  = $user->authorise('core.edit.state', 'com_rssfactory.feed.' . $item->id);
?>
<tr>
    <td class="text-center">
        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
    </td>
    <td class="text-center">
        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'feeds.', $canChange, 'cb'); ?>
    </td>
    <td class="text-center">
        <?php echo FeedsHelper::icon($item->id, $item->url, ['width' => 16, 'height' => 16]); ?>
    </td>
    <td>
        <div class="dropdown">
            <?php if ($canEdit) : ?>
                <a href="<?php echo Route::_('index.php?option=com_rssfactory&task=feed.edit&id=' . $item->id); ?>">
                    <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php else : ?>
                <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
            <button class="btn btn-link btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="icon-menu"></span>
            </button>
            <ul class="dropdown-menu">
                <?php if ($canEdit) : ?>
                    <li>
                        <a class="dropdown-item" href="<?php echo Route::_('index.php?option=com_rssfactory&task=feed.edit&id=' . $item->id); ?>">
                            <span class="icon-edit"></span> <?php echo Text::_('JACTION_EDIT'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($canChange) : ?>
                    <li>
                        <a class="dropdown-item" href="<?php echo Route::_('index.php?option=com_rssfactory&task=feeds.refresh&cid[]=' . $item->id . '&' . HTMLHelper::_('form.token')); ?>">
                            <span class="icon-refresh"></span> <?php echo Text::_('COM_RSSFACTORY_FEEDS_TOOLBAR_REFRESH'); ?>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo Route::_('index.php?option=com_rssfactory&task=feeds.clearCache&cid[]=' . $item->id . '&' . HTMLHelper::_('form.token')); ?>">
                            <span class="icon-trash"></span> <?php echo Text::_('COM_RSSFACTORY_FEEDS_TOOLBAR_CLEAR_CACHE'); ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </td>
    <td class="text-center">
        <?php echo (int) $item->stories_total; ?>
    </td>
    <td class="text-center">
        <?php echo $item->last_refresh ? HTMLHelper::_('date', $item->last_refresh, Text::_('DATE_FORMAT_LC2')) : '-'; ?>
    </td>
    <td class="text-center">
        <?php echo $item->i2c_enabled ? Text::_('JYES') : Text::_('JNO'); ?>
    </td>
    <td class="text-center">
        <?php echo $item->error ? '<span class="text-danger" title="' . htmlspecialchars($item->error, ENT_QUOTES, 'UTF-8') . '">&#9888;</span>' : ''; ?>
    </td>
    <td class="text-center">
        <?php echo (int) $item->id; ?>
    </td>
</tr>
            <?php echo RssFactoryHtml::itemDropDown([
                'edit'       => ['id' => $this->item->id, 'prefix' => 'feed'],
                'publish'    => ['i' => $this->i, 'published' => $this->item->published, 'prefix' => 'feeds'],
                'refresh'    => ['i' => $this->i, 'prefix' => 'feeds'],
                'divider'    => [],
                'clearcache' => ['i' => $this->i, 'prefix' => 'feeds'],
            ]); ?>
        </div>
    </td>

    <td class="d-none d-md-table-cell center">
        <b><?php echo $this->item->storiesCached; ?></b> / <?php echo $this->item->nrfeeds; ?>
    </td>

    <td class="d-none d-md-table-cell small" style="line-height: normal;">
        <?php if (!is_null($this->item->date)): ?>
            <?php echo HTMLHelper::_('date', $this->item->date, Text::_('DATE_FORMAT_LC2')); ?>

            <div class="text-muted">
                <?php echo Text::plural('COM_RSSFACTORY_FEEDS_LAST_REFRESH_NO_NEW_STORIES', $this->item->last_refresh_stories); ?>
            </div>
        <?php endif; ?>
    </td>

    <td class="d-none d-md-table-cell center small">
        <?php if ($this->item->i2c_enabled): ?>
            <i class="icon-publish"></i>
        <?php endif; ?>
    </td>

    <td class="d-none d-md-table-cell center small">
        <?php if ($this->item->rsserror): ?>
            <i style="cursor: pointer;" class="icon-publish hasTooltip"
               title="<?php echo $this->item->last_error; ?>"></i>
        <?php endif; ?>
    </td>

    <td class="center d-none d-md-table-cell">
        <?php echo $this->item->id; ?>
    </td>
</tr>
