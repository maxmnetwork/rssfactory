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

namespace Joomla\Component\Rssfactory\Administrator\View\Feeds\Tmpl;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

?>
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo HTMLHelper::_('form.token'); ?>
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>"/>
<?php echo HTMLHelper::_('form.token'); ?>
