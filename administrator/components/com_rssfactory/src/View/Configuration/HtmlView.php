<?php

namespace Joomla\Component\Rssfactory\Administrator\View\Configuration;

use Joomla\CMS\Version;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryViewRss;

defined('_JEXEC') or die;

class ConfigurationView extends FactoryViewRss
{
    protected $buttons = ['apply', 'save', 'close'];
    protected $get = ['form', 'fieldsets'];
    protected $html = [];
    protected $permissions = ['backend.settings'];

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        // Add Joomla 4 compatible behaviors
        $this->html[] = 'bootstrap.tooltip';
        $this->html[] = 'behavior.multiselect';
        $this->html[] = 'dropdown.init';
        $this->html[] = 'formbehavior.chosen/.chosen';
    }

    protected function loadFieldset($fieldset)
    {
        $this->fieldset = $fieldset;
        return $this->loadTemplate('fieldset');
    }
}
