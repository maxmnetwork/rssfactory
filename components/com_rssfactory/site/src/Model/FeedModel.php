<?php
namespace Joomla\Component\Rssfactory\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;

class FeedModel extends FormModel
{
    public function submitFeed(array $data)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $columns = ['title', 'url', 'user_id', 'submitted_on'];
        $values = [
            $db->quote($data['title']),
            $db->quote($data['url']),
            (int)Factory::getUser()->id,
            $db->quote(date('Y-m-d H:i:s'))
        ];

        $query
            ->insert('#__rssfactory_submitted')
            ->columns($columns)
            ->values(implode(',', $values));

        $db->setQuery($query);

        try {
            $db->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
