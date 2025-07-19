<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;
use Joomla\CMS\Date\Date;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Application\CMSApplicationInterface;

class RssFactoryFeedsHelper
{
    /**
     * Returns the feeds (with stories and pagination for each feed) for the tiled output.
     *
     * @param array $filters
     * @param CMSApplicationInterface|null $app
     * @param DatabaseDriver|null $dbo
     * @return mixed
     */
    public static function getItemsForTiled(array $filters = [], ?CMSApplicationInterface $app = null, ?DatabaseDriver $dbo = null)
    {
        static $items = [];

        $filters = self::prepareFilters($filters, $app);
        $hash = md5(serialize($filters));

        if (!isset($items[$hash])) {
            $results = self::getFeeds($filters, $app, $dbo);

            foreach ($results as &$result) {
                $result->stories = self::getStoriesForFeed($filters, $result->id, $filters['limit'], $app, $dbo);
                $result->pagination = self::getPaginationForFeed($result->id, $result->stories_total, $filters['limit'], $app);
            }

            $items[$hash] = $results;
        }

        return $items[$hash];
    }

    public static function getItemsForList(array $filters = [], ?CMSApplicationInterface $app = null, ?DatabaseDriver $dbo = null)
    {
        static $items = [];

        $filters = self::prepareFilters($filters, $app);
        $hash = md5(serialize($filters));

        if (!isset($items[$hash])) {
            $items[$hash] = [
                'stories'    => self::getStoriesForList($filters, $app, $dbo),
                'pagination' => self::getPaginationForList($filters, $app, $dbo),
            ];
        }

        return $items[$hash];
    }

    public static function getStoriesForFeed(array $filters, int $feedId, int $limit, ?CMSApplicationInterface $app = null, ?DatabaseDriver $dbo = null)
    {
        $filters = self::prepareFilters($filters, $app);
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $app = $app ?: Factory::getApplication();

        $query = $dbo->getQuery(true)
            ->select('c.*')
            ->from($dbo->quoteName('#__rssfactory_cache', 'c'))
            ->where('c.rssid = :feedId')
            ->group('c.id')
            ->bind(':feedId', $feedId, \PDO::PARAM_INT);

        $query = self::addQueryOrderCondition($query, $filters, $dbo);
        $query = self::addQuerySelectVoteValue($query, $dbo, $app);
        $query = self::addQuerySearchCondition($query, $filters, $dbo);
        $query = self::addQueryIntervalCondition($query, $filters['interval'], $dbo);
        $query = self::addQueryWordFilter($query, $filters['wordfilter'], $dbo);

        $app->getDispatcher()->dispatch('onQueryStoriesForFeed', [
            'com_rssfactory',
            $query,
        ]);

        $results = $dbo->setQuery($query, self::getLimitstartForFeed($feedId, $app), $limit)
            ->loadObjectList('id');

        $app->getDispatcher()->dispatch('onResultsStoriesForFeed', [
            'com_rssfactory',
            $results,
        ]);

        $results = self::getCommentsAndVotesForStories($results, $dbo, $app);

        return $results;
    }

    public static function getPaginationForFeed(int $feedId, int $totalStories, int $limit, ?CMSApplicationInterface $app = null)
    {
        $app = $app ?: Factory::getApplication();
        $pagination = new Pagination($totalStories, self::getLimitstartForFeed($feedId, $app), $limit);
        $pagination->setAdditionalUrlParam('feed_id', $feedId);
        return $pagination;
    }

    public static function getTotalStories(array $filters, ?DatabaseDriver $dbo = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $filters = self::prepareFilters($filters);

        $query = $dbo->getQuery(true)
            ->select('COUNT(c.id)')
            ->from($dbo->quoteName('#__rssfactory_cache', 'c'));

        if (!empty($filters['feeds'])) {
            $query->where('c.rssid IN (' . implode(',', array_map('intval', $filters['feeds'])) . ')');
        }

        $query = self::addQuerySearchCondition($query, $filters, $dbo);

        return $dbo->setQuery($query)->loadResult();
    }

    public static function getStoriesForList(array $filters, ?CMSApplicationInterface $app = null, ?DatabaseDriver $dbo = null)
    {
        $cache = RssFactoryCache::getInstance();
        $hash = md5('stories_for_list_' . serialize($filters));
        $results = $cache->get($hash);

        if (false === $results) {
            $dbo = $dbo ?: Factory::getContainer()->get('db');
            $app = $app ?: Factory::getApplication();
            $filters = self::prepareFilters($filters, $app);
            $limitstart = $app->getInput()->getInt('limitstart');

            $query = self::getBaseQueryForList($filters, $dbo)
                ->select('c.*');
            $query = self::addQueryOrderCondition($query, $filters, $dbo);
            $query = self::addQueryWordFilter($query, $filters['wordfilter'], $dbo);

            $app->getDispatcher()->dispatch('onQueryStoriesForFeed', [
                'com_rssfactory',
                $query,
            ]);

            $results = $dbo->setQuery($query, $limitstart, $filters['limit'])
                ->loadObjectList('id');

            $app->getDispatcher()->dispatch('onResultsStoriesForFeed', [
                'com_rssfactory',
                $results,
            ]);
        } else {
            $results = unserialize($results);
        }

        $results = self::getCommentsAndVotesForStories($results, $dbo, $app);
        $results = self::getVotesForStories($results, $dbo, $app);

        $cache->store(serialize($results), $hash);

        return $results;
    }

    public static function getPaginationForList(array $filters, ?CMSApplicationInterface $app = null, ?DatabaseDriver $dbo = null)
    {
        static $totals = [];

        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $app = $app ?: Factory::getApplication();
        $filters = self::prepareFilters($filters, $app);
        $limitstart = $app->getInput()->getInt('limitstart');

        $query = self::getBaseQueryForList($filters, $dbo)
            ->select('COUNT(c.id)');

        $hash = md5($query->dump());

        if (!isset($totals[$hash])) {
            $totals[$hash] = $dbo->setQuery($query)->loadResult();
        }

        return new Pagination($totals[$hash], $limitstart, $filters['limit']);
    }

    public static function getAds(?CMSApplicationInterface $app = null, ?DatabaseDriver $dbo = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $app = $app ?: Factory::getApplication();
        $categoryId = $app->getInput()->getInt('category_id', 0);

        if (!$configuration->get('enable_ads', 1) || !$categoryId) {
            return [];
        }

        $query = $dbo->getQuery(true)
            ->select('a.*')
            ->from($dbo->quoteName('#__rssfactory_ads', 'a'))
            ->leftJoin($dbo->quoteName('#__rssfactory_ad_category_map', 'm') . ' ON m.adId = a.id')
            ->where('a.categories_assigned = ' . $dbo->quote(''), 'OR')
            ->where('m.categoryId = ' . $dbo->quote($categoryId));
        return $dbo->setQuery($query)->loadObjectList();
    }

    public static function getRelevantCategories(array $joomlaCategories, ?DatabaseDriver $dbo = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $array = [];

        $query = $dbo->getQuery(true)
            ->select('c.id, c.params')
            ->from($dbo->quoteName('#__categories', 'c'))
            ->where('c.extension = ' . $dbo->quote('com_rssfactory'));
        $results = $dbo->setQuery($query)->loadObjectList();

        foreach ($results as $result) {
            $params = new Registry($result->params);
            $categories = $params->get('relevant_categories', []);
            if (array_intersect($joomlaCategories, $categories)) {
                $array[] = $result->id;
            }
        }

        return $array;
    }

    protected static function prepareFilters(array $filters, ?CMSApplicationInterface $app = null)
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $array = [
            'categories' => 'array',
            'feeds'      => 'array',
            'relevant'   => 'array',
            'search'     => 'array',
        ];

        foreach ($array as $item => $type) {
            if (!isset($filters[$item])) {
                $filters[$item] = [];
            }

            if ('array' === $type && !is_array($filters[$item])) {
                $filters[$item] = [$filters[$item]];
            }
        }

        $filters['limit'] = $filters['limit'] ?? $configuration->get('feedsperpage', 7);
        $filters['show_empty_feeds'] = $filters['show_empty_feeds'] ?? $configuration->get('showemptysources', 0);
        $filters['stories_sort_order'] = $filters['stories_sort_order'] ?? 'item_date';
        $filters['stories_sort_dir'] = $filters['stories_sort_dir'] ?? 'DESC';
        $filters['feeds_limit'] = $filters['feeds_limit'] ?? 0;
        $filters['interval'] = $filters['interval'] ?? false;
        $filters['bookmarked'] = $filters['bookmarked'] ?? false;
        $filters['feeds_sort_column'] = $filters['feeds_sort_column'] ?? 'ordering';
        $filters['feeds_sort_dir'] = $filters['feeds_sort_dir'] ?? 'asc';

        $filters['wordfilter']['any'] = $filters['wordfilter']['any'] ?? '';
        $filters['wordfilter']['exact'] = $filters['wordfilter']['exact'] ?? '';
        $filters['wordfilter']['none'] = $filters['wordfilter']['none'] ?? '';

        $filters['limitstart'] = $filters['limitstart'] ?? 0;

        return $filters;
    }

    protected static function getTotalCommentsForStories(array $stories, ?DatabaseDriver $dbo = null)
    {
        if (!$stories) {
            return [];
        }

        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $query = $dbo->getQuery(true)
            ->select('COUNT(c.id) AS comments_total, c.item_id')
            ->from($dbo->quoteName('#__rssfactory_comments', 'c'))
            ->where('c.type_id = ' . $dbo->quote(1))
            ->where('c.item_id IN (' . implode(',', array_map('intval', $stories)) . ')')
            ->group('c.item_id');

        $configuration = ComponentHelper::getParams('com_rssfactory');
        if ($configuration->get('approveComments', 0)) {
            $query->where('c.published = ' . $dbo->quote(1));
        }

        $results = $dbo->setQuery($query)
            ->loadObjectList('item_id');

        return $results;
    }

    protected static function getTotalVotesForStories(array $stories, ?DatabaseDriver $dbo = null)
    {
        if (!$stories) {
            return [];
        }

        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $query = $dbo->getQuery(true)
            ->select('SUM(v.voteValue) AS votes_total, v.cacheId')
            ->from($dbo->quoteName('#__rssfactory_voting', 'v'))
            ->where('v.cacheId IN (' . implode(',', array_map('intval', $stories)) . ')')
            ->group('v.cacheId');

        $results = $dbo->setQuery($query)
            ->loadObjectList('cacheId');

        return $results;
    }

    protected static function getLimitstartForFeed(int $feedId, ?CMSApplicationInterface $app = null)
    {
        $app = $app ?: Factory::getApplication();
        $input = $app->getInput();
        $limitstart = $input->getInt('limitstart', 0);
        $feed_id = $input->getInt('feed_id', 0);

        if ($feed_id != $feedId) {
            return 0;
        }

        return $limitstart;
    }

    protected static function getCommentsAndVotesForStories(array $stories, ?DatabaseDriver $dbo = null, ?CMSApplicationInterface $app = null)
    {
        $votes = [];
        $comments = [];

        if ($stories && RssFactoryHelper::isUserAuthorised('frontend.voting')) {
            $votes = self::getTotalVotesForStories(array_keys($stories), $dbo);
        }

        if ($stories && RssFactoryHelper::isUserAuthorised('frontend.comment.view')) {
            $comments = self::getTotalCommentsForStories(array_keys($stories), $dbo);
        }

        foreach ($stories as $id => &$story) {
            $story->votes_total = isset($votes[$id]) ? $votes[$id]->votes_total : 0;
            $story->comments_total = isset($comments[$id]) ? $comments[$id]->comments_total : 0;
        }

        return $stories;
    }

    protected static function getVotesForStories(array $stories, ?DatabaseDriver $dbo = null, ?CMSApplicationInterface $app = null)
    {
        if ($stories && RssFactoryHelper::isUserAuthorised('frontend.voting')) {
            $dbo = $dbo ?: Factory::getContainer()->get('db');
            $user = Factory::getUser();
            $app = $app ?: Factory::getApplication();
            $hash = sha1($user->id . $app->getInput()->server->getString('REMOTE_ADDR', ''));

            $query = $dbo->getQuery(true)
                ->select('v.voteValue AS vote_value, v.cacheId')
                ->from($dbo->quoteName('#__rssfactory_voting', 'v'))
                ->where('v.cacheId IN (' . implode(',', array_map('intval', array_keys($stories))) . ')')
                ->where('v.voteHash = ' . $dbo->q($hash));
            $results = $dbo->setQuery($query)
                ->loadAssocList('cacheId');

            foreach ($stories as $id => $story) {
                $story->vote_value = isset($results[$id]) ? $results[$id]['vote_value'] : null;
            }
        }

        return $stories;
    }

    protected static function getBaseQueryForList(array $filters, ?DatabaseDriver $dbo = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');

        $query = $dbo->getQuery(true)
            ->from($dbo->quoteName('#__rssfactory_cache', 'c'));

        if (!empty($filters['categories'])) {
            $query->leftJoin($dbo->quoteName('#__rssfactory', 'f') . ' ON f.id = c.rssid')
                ->where('f.cat IN (' . implode(',', array_map('intval', $filters['categories'])) . ')');
        }

        if (!empty($filters['feeds'])) {
            $query->where('c.rssid IN (' . implode(',', array_map('intval', $filters['feeds'])) . ')');
        }

        if (!empty($filters['relevant']) && $categories = self::getRelevantCategories($filters['relevant'], $dbo)) {
            $query->leftJoin($dbo->quoteName('#__rssfactory', 'f') . ' ON f.id = c.rssid')
                ->where('f.cat IN (' . implode(',', array_map('intval', $categories)) . ')');
        }

        $query = self::addQuerySearchCondition($query, $filters, $dbo);

        return $query;
    }

    protected static function getFeeds(array $filters, ?CMSApplicationInterface $app = null, ?DatabaseDriver $dbo = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $user = Factory::getUser();
        $filters = self::prepareFilters($filters, $app);

        $query = $dbo->getQuery(true)
            ->select('f.*')
            ->from($dbo->quoteName('#__rssfactory', 'f'));

        $query->order('f.' . $filters['feeds_sort_column'] . ' ' . $filters['feeds_sort_dir']);

        $query->select('COUNT(c.id) AS stories_total')
            ->leftJoin($dbo->quoteName('#__rssfactory_cache', 'c') . ' ON c.rssid = f.id')
            ->group('f.id');

        $query->where('f.published = ' . $dbo->quote(1));

        $query->leftJoin($dbo->quoteName('#__categories', 'cat') . ' ON cat.id = f.cat')
            ->where('cat.published = ' . $dbo->quote(1));

        if (!empty($filters['categories'])) {
            $query->where('f.cat IN (' . implode(',', array_map('intval', $filters['categories'])) . ')');
        }

        if (!empty($filters['feeds'])) {
            $query->where('f.id IN (' . implode(',', array_map('intval', $filters['feeds'])) . ')');
        }

        if (!empty($filters['relevant']) && $categories = self::getRelevantCategories($filters['relevant'], $dbo)) {
            $query->where('f.cat IN (' . implode(',', array_map('intval', $categories)) . ')');
        }

        if (empty($filters['show_empty_feeds'])) {
            $query->having('stories_total > 0');
        }

        if (RssFactoryHelper::isUserAuthorised('frontend.favorites')) {
            $query->select('fav.id AS is_favorite')
                ->leftJoin($dbo->quoteName('#__rssfactory_favorites', 'fav') . ' ON fav.feed_id = f.id AND fav.user_id = ' . $dbo->quote($user->id));

            if (!empty($filters['bookmarked'])) {
                $query->where('fav.id IS NOT NULL');
            }
        }

        $query = self::addQuerySearchCondition($query, $filters, $dbo);

        $results = $dbo->setQuery($query, 0, $filters['feeds_limit'])
            ->loadObjectList();

        return $results;
    }

    protected static function addQuerySearchCondition($query, $filters, ?DatabaseDriver $dbo = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        if (!empty($filters['search'])) {
            $array = [];
            foreach ($filters['search'] as $item) {
                $array[] = '((c.item_title LIKE ' . $dbo->quote('%' . $item . '%') . ') OR (c.item_description LIKE ' . $dbo->quote('%' . $item . '%') . '))';
            }
            $query->where('(' . implode(' OR ', $array) . ')');
        }
        return $query;
    }

    protected static function addQuerySelectVoteValue($query, ?DatabaseDriver $dbo = null, ?CMSApplicationInterface $app = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $user = Factory::getUser();
        $app = $app ?: Factory::getApplication();

        if (RssFactoryHelper::isUserAuthorised('frontend.voting')) {
            $hash = sha1($user->id . $app->getInput()->server->getString('REMOTE_ADDR', ''));
            $query->select('v.voteValue AS vote_value')
                ->leftJoin('#__rssfactory_voting v ON v.cacheId = c.id AND v.voteHash = ' . $dbo->quote($hash));
        }

        return $query;
    }

    protected static function addQueryOrderCondition($query, $filters, ?DatabaseDriver $dbo = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        $direction = $filters['stories_sort_dir'];

        switch ($filters['stories_sort_order']) {
            case '':
            case 'none':
            default:
                $query->order('c.item_date ' . $direction);
                break;

            case 'random':
                $query->order('RAND()');
                break;

            case 'votes':
                $query->leftJoin('#__rssfactory_voting votes ON votes.cacheId = c.id')
                    ->order('SUM(votes.voteValue) ' . $direction);
                break;
            case 'comments':
                $approval = ComponentHelper::getParams('com_rssfactory')->get('approveComments', 0);
                $approval = $approval ? ' AND comments.published = ' . $dbo->quote(1) : '';
                $query->leftJoin('#__rssfactory_comments comments ON comments.type_id = ' . $dbo->quote(1) . ' AND comments.item_id = c.id ' . $approval)
                    ->order('COUNT(comments.id) ' . $direction);
                break;

            case 'hits':
                $query->order('c.hits ' . $direction);
                break;
        }

        return $query;
    }

    protected static function addQueryIntervalCondition($query, $interval, ?DatabaseDriver $dbo = null)
    {
        $dbo = $dbo ?: Factory::getContainer()->get('db');
        if (!$interval) {
            return $query;
        }

        switch ($interval) {
            case 'today':
                $date = (new Date('today'))->toSql();
                $query->where('c.item_date >= ' . $dbo->quote($date));
                break;

            case 'week':
                $monday = strtotime('last monday');
                if (date('N') == 1) {
                    $monday = strtotime('-7 days', $monday);
                }
                $date = (new Date($monday))->toSql();
                $query->where('c.item_date >= ' . $dbo->quote($date));
                break;

            case 'last7days':
                $date = (new Date('-7 days'))->toSql();
                $query->where('c.item_date >= ' . $dbo->quote($date));
                break;

            case 'month':
                $firstDayOfMonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $date = (new Date($firstDayOfMonth))->toSql();
                $query->where('c.item_date >= ' . $dbo->quote($date));
                break;

            case 'year':
                $firstDayOfYear = mktime(0, 0, 0, 1, 1, date('Y'));
                $date = (new Date($firstDayOfYear))->toSql();
                $query->where('c.item_date >= ' . $dbo->quote($date));
                break;
        }

        return $query;
    }

    protected static function addQueryWordFilter($query, $wordfilter, ?DatabaseDriver $dbo = null)
    {
        // Implement as needed for your application
        return $query;
    }
}
