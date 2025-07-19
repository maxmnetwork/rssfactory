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

?>

<table class="table table-striped" id="adsList">
    <thead>
        <tr>
            <?php foreach ($this->columns as $column): ?>
                <th><?php echo $column['label']; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php // Table body rendered in default_body.php ?>
