<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;

class CategoryModel extends ListModel
{
    protected $option = 'com_rssfactory';

    /**
     * Get the table instance for the Category model
     *
     * @param string $type The table type (Category by default)
     * @param string $prefix The table prefix (RssFactoryTable by default)
     * @param array $config Configuration options
     * 
     * @return \Joomla\CMS\Table\Table
     */
    public function getTable($type = 'Category', $prefix = 'RssFactoryTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Get the form instance for the Category model
     *
     * @param array $data Data to populate the form
     * @param bool $loadData Whether to load the data into the form
     * 
     * @return \Joomla\CMS\Form\Form|null
     */
    public function getForm($data = array(), $loadData = true)
    {
        // For now, this method is not implemented.
        return null;
    }
}
