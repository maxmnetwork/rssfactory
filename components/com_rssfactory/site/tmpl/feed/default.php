<?php
\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('formbehavior.chosen', 'select');

?>
<h1><?php echo Text::_('COM_RSSFACTORY_SUBMIT_FEED'); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_rssfactory&task=feed.submit'); ?>" method="post" name="adminForm" id="adminForm">
    <div>
        <label for="jform_title"><?php echo Text::_('JGLOBAL_TITLE'); ?></label>
        <input type="text" name="jform[title]" id="jform_title" required />
    </div>
    <div>
        <label for="jform_url"><?php echo Text::_('COM_RSSFACTORY_FEED_URL'); ?></label>
        <input type="url" name="jform[url]" id="jform_url" required />
    </div>
    <input type="hidden" name="<?php echo \Joomla\CMS\Session\Session::getFormToken(); ?>" value="1" />
    <button type="submit"><?php echo Text::_('JSUBMIT'); ?></button>
</form>
