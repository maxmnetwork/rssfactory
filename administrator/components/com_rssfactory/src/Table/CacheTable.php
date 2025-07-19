<?php
// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseDriver;
use Joomla\Component\Rssfactory\Administrator\Helper\RssFactoryFilterHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;

class CacheTable extends Table
{
    public $item_description;
    protected $feed;
    protected $debug = false;

    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__rssfactory_cache', 'id', $db);
    }

    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    public function getFeed()
    {
        return $this->feed;
    }

    public function getItemDescription()
    {
        $description = $this->item_description;
        $description = $this->stripTags($description);
        $description = $this->addSourceLink($description);

        return $description;
    }

    public function check()
    {
        if (!parent::check()) {
            return false;
        }

        if (is_null($this->item_hash)) {
            $this->item_hash = $this->getItemHash();
        }

        if (!$this->debug && $this->storyExists()) {
            return false;
        }

        if (is_null($this->item_date)) {
            $this->item_date = Factory::getDate()->toSql();
        }

        if (is_null($this->date)) {
            $this->date = Factory::getDate()->toSql();
        }

        $this->encodeUrl();
        $this->parseLinksInDescription();

        if (!$this->checkWordFilters()) {
            return false;
        }

        return true;
    }

    public function getItemHash()
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $hashSource = '';

        switch ($configuration->get('detectduplicates', 'title_description')) {
            case 'title':
                $hashSource = $this->item_title;
                break;
            case 'description':
                $hashSource = $this->item_description;
                break;
            case 'pubdate':
                $hashSource = $this->item_date;
                break;
            default:
            case 'title_description':
                $hashSource = $this->item_title . $this->item_description;
                break;
        }

        return sha1(preg_replace('#\s+#', '', $hashSource));
    }

    protected function storyExists()
    {
        $table = self::getInstance('Cache', 'RssFactoryTable');

        $result = $table->load(array(
            'rssid'     => $this->rssid,
            'item_hash' => $this->getItemHash(),
        ));

        if ($result) {
            $table->archived = 0;
            $table->item_date = $this->item_date;
            $table->store();
            return true;
        }

        return false;
    }

    protected function encodeUrl()
    {
        $uri = Uri::getInstance($this->item_link);
        $query = $uri->getQuery(true);
        $uri->setQuery(null);
        $uri->setQuery($query);

        $this->item_link = $uri->toString();

        return true;
    }

    protected function parseLinksInDescription()
    {
        $this->item_description = preg_replace('/<a /', '<a target="_blank" rel="nofollow" ', $this->item_description);
        $this->item_description = preg_replace('/&gt;a /', '&gt;a target="_blank" rel="nofollow" ', $this->item_description);

        return true;
    }

    protected function checkWordFilters()
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $helper = new RssFactoryFilterHelper($configuration);
        $filter = $helper->getWordFilter($this->getFeed());

        $text = $this->item_title . ' ' . $this->item_description;

        if (false === $filter) {
            return true;
        }

        if (!$this->passesAllowedWordsFilter($text, $filter['allowed'])) {
            return false;
        }

        if (!$this->passesBannedWordsFilter($text, $filter['banned'])) {
            return false;
        }

        if (!$this->passesExactWordsFilter($text, $filter['exact'])) {
            return false;
        }

        return true;
    }

    protected function passesAllowedWordsFilter($text, $filter)
    {
        if (!$filter) {
            return true;
        }

        foreach ($filter as $word) {
            if (preg_match('/\b' . $word . '\b/iu', $text)) {
                return true;
            }
        }

        return false;
    }

    protected function passesBannedWordsFilter($text, $filter)
    {
        foreach ($filter as $word) {
            if (preg_match('/\b' . $word . '\b/iu', $text)) {
                return false;
            }
        }

        return true;
    }

    protected function passesExactWordsFilter($text, $filter)
    {
        foreach ($filter as $word) {
            if (!preg_match('/\b' . $word . '\b/iu', $text)) {
                return false;
            }
        }

        return true;
    }

    protected function wordReplacements()
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $helper = new RssFactoryFilterHelper($configuration);
        $filter = $helper->getI2CWordFilter($this->getFeed());

        if (false === $filter || !$filter['replacements']) {
            return true;
        }

        $patterns = array();
        $replacements = array();

        foreach ($filter['replacements'] as $expression) {
            if (!mb_strpos($expression, '|')) {
                continue;
            }

            list ($search, $replace) = explode('|', $expression);

            $regExSpecialCharacters = array('.', '^', '$', '*', '+', '?', '{', '}', '\\', '[', ']', '|', '(', ')', ' ', '#');
            $replaceRegExSpecialCharacters = array('\.', '\^', '\$', '\*', '\+', '\?', '\{', '\}', '\\\\', '\[', '\]', '\|', '\(', '\)', '\s*', '\#');
            $wordDelimiterRegExpClass = '[\s\.\;\:\-\/]';

            $patterns[] = '#(' . $wordDelimiterRegExpClass . '+)'
                . str_replace($regExSpecialCharacters, $replaceRegExSpecialCharacters, trim($search))
                . '(' . $wordDelimiterRegExpClass . '*?)#is';
            $replacements[] = '\1' . $replace . '\2';
        }

        $this->title = preg_replace($patterns, $replacements, ' ' . $this->title . ' ');
        $this->introtext = preg_replace($patterns, $replacements, ' ' . $this->introtext . ' ');
        $this->fulltext = preg_replace($patterns, $replacements, ' ' . $this->fulltext . ' ');
    }

    protected function addReadMore()
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');

        if ($configuration->get('i2c_add_read_more', 0)) {
            $limit = $configuration->get('i2c_readmore_options', 50);
            $words = explode(' ', $this->introtext);

            if (count($words) > $limit) {
                $this->introtext = implode(' ', array_slice($words, 0, $limit));
                array_splice($words, 0, $limit);
                $this->fulltext = implode(' ', $words);
            }
        }
    }

    protected function addRelevantStories()
    {
        $params = $this->getFeed()->params;
        $configuration = ComponentHelper::getParams('com_rssfactory');

        if (0 == $params->get('enable_relevant_stories', -1) ||
            (-1 == $params->get('enable_relevant_stories', -1) && 0 == $configuration->get('enable_relevant_stories'))
        ) {
            return false;
        }

        $limit = '' != $params->get('relevant_stories_limit', '') ? $params->get('relevant_stories_limit', '') : $configuration->get('relevant_stories_limit', 10);
        $position = -1 != $params->get('relevant_stories_position', -1) ? $params->get('relevant_stories_position', -1) : $configuration->get('relevant_stories_position');

        $html = ' <p>{com_rssfactory relevantStories nrStories=[' . $limit . ']}</p> ';

        if (1 == $position) {
            $this->introtext = $html . $this->introtext;
        } else {
            $this->fulltext .= $html;
        }

        return true;
    }
}
