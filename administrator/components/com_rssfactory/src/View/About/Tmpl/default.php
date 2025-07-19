<?php
defined('_JEXEC') or die;
use Joomla\CMS\Layout\FileLayout;

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

?>

<div class="row">
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="col-2">
            <?php echo $this->sidebar; ?>
        </div>
    <?php endif; ?>

    <div id="j-main-container" class="<?php echo !empty($this->sidebar) ? 'col-10' : 'col-12'; ?>">
        <?php
        // Render the about layout using Joomla 4 LayoutHelper/FileLayout
        $layout = new FileLayout('about_layout', JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
        echo $layout->render(['data' => $this->information]);
        ?>
    </div>
</div>
