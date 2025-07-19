<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

class CacheModel extends BaseDatabaseModel
{
    public function clear(): bool
    {
        $dbo = $this->getDbo();

        // Get list of cache IDs
        $query = $dbo->getQuery(true)
            ->select('c.id')
            ->from('#__rssfactory_cache AS c');
        $results = $dbo->setQuery($query)->loadAssocList('id');
        $results = array_keys($results);

        // Clear the cache table
        $result = $dbo->setQuery('TRUNCATE TABLE #__rssfactory_cache')->execute();

        if (!$result) {
            return false;
        }

        // Import plugin for finder
        PluginHelper::importPlugin('finder');

        // Trigger event after deleting cache entries
        foreach ($results as $id) {
            Factory::getApplication()->triggerEvent('onFinderAfterDelete', [
                'com_rssfactory.story',
                $id
            ]);
        }

        // Remove all votes
        $dbo->setQuery('TRUNCATE TABLE #__rssfactory_voting')->execute();

        // Remove all comments
        $dbo->setQuery('TRUNCATE TABLE #__rssfactory_comments')->execute();

        return true;
    }

    public function optimize(): bool
    {
        $dbo = $this->getDbo();
        return (bool)$dbo->setQuery('OPTIMIZE TABLE #__rssfactory_cache')->execute();
    }
}
