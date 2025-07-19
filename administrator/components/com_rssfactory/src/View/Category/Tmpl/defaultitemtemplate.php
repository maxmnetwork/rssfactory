<?php
/**
 * @package     com_rssfactory
 * @subpackage  administrator
 * @copyright   Copyright (C) 2024 Your Company
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Rssfactory\Administrator\View\Category\Tmpl;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;


/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

/** @var array $item */
$item = $this->item ?? null;

if (!$item) :
     echo '<div class="alert alert-warning">' . Text::_('COM_RSSFACTORY_CATEGORY_ITEM_NOT_FOUND') . '</div>';
     return;
endif;
?>

<div class="com-rssfactory-category-item">
     <h2><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></h2>

     <?php if (!empty($item->description)) : ?>
          <div class="category-description">
                <?php echo $item->description; ?>
          </div>
     <?php endif; ?>

     <dl class="category-details">
          <dt><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></dt>
          <dd><?php echo (int) $item->id; ?></dd>

          <dt><?php echo Text::_('JSTATUS'); ?></dt>
          <dd><?php echo HTMLHelper::_('jgrid.published', $item->published, $item->id, 'categories.', true, 'cb'); ?></dd>

          <dt><?php echo Text::_('JDATE_CREATED'); ?></dt>
          <dd><?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC2')); ?></dd>
     </dl>

     <div class="category-actions">
          <a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_rssfactory&task=category.edit&id=' . (int) $item->id); ?>">
                <?php echo Text::_('JACTION_EDIT'); ?>
          </a>
          <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_rssfactory&view=categories'); ?>">
                <?php echo Text::_('JTOOLBAR_BACK'); ?>
          </a>
     </div>
</div>