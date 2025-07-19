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

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Component\Rssfactory\Administrator\Helper\RssFactoryFilterHelper;

class ContentTable extends Table
{
    public $refreshallowedwords;
    public $refreshbannedwords;
    public $refreshexactmatchwords;
    public $i2c_enable_word_filter;
    public $i2c_words_white_list;
    public $i2c_words_black_list;
    public $i2c_words_exact_list;
    public $i2c_words_replacements;
    public $params;
    protected $filter = null;
    protected $filterI2C = null;

    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database driver object
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__rssfactory', 'id', $db);
    }

    /**
     * Binds an array to the table properties
     *
     * @param   array   $array  Data to bind
     * @param   string  $ignore  Fields to ignore
     * @return  bool  True on success
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new Registry($array['params']);
            $array['params'] = $registry->toString();
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Method to check and prepare data before saving
     *
     * @return  bool  True if successful
     */
    public function check()
    {
        $this->prepareTable();

        if (!parent::check()) {
            return false;
        }

        // Check if item passes I2C word filters check.
        if (!$this->checkI2CWordFilters()) {
            return false;
        }

        $this->wordReplacements();
        $this->addReadMore();
        $this->addRelevantStories();
        $this->checkOverwriteArticle();

        return true;
    }

    /**
     * Store the data in the database
     *
     * @param   bool  $updateNulls  Whether to update null values
     * @return  bool  True if successful
     */
    public function store($updateNulls = false)
    {
        if (!parent::store($updateNulls)) {
            return false;
        }

        // Set article as featured
        $this->setFeatured();

        return true;
    }

    /**
     * Prepare the table before saving
     *
     * @return  bool  True if successful
     */
    protected function prepareTable()
    {
        $db = $this->getDbo();
        if ($this->state == 1 && (int)$this->publish_up == 0) {
            $this->publish_up = Factory::getDate()->toSql();
        }

        if ($this->state == 1 && intval($this->publish_down) == 0) {
            $this->publish_down = $db->getNullDate();
        }

        $this->version++;

        if (empty($this->id)) {
            $this->reorder('catid = ' . (int)$this->catid . ' AND state >= 0');
        }

        return true;
    }

    /**
     * Set the article as featured
     *
     * @return  bool  True if successful
     */
    protected function setFeatured()
    {
        if (!$this->featured) {
            return true;
        }

        $dbo = $this->getDbo();
        $query = ' INSERT INTO #__content_frontpage (' . $dbo->quoteName('content_id') . ', ' . $dbo->quoteName('ordering') . ')'
            . ' VALUES (' . $dbo->quote($this->id) . ', 0)';

        return $dbo->setQuery($query)->execute();
    }

    /**
     * Remove featured status from the article
     *
     * @param   int  $pk  Article ID
     * @return  bool  True if successful
     */
    protected function removeFeatured($pk)
    {
        $dbo = $this->getDbo();
        $query = $dbo->getQuery(true)
            ->delete()
            ->from('#__content_frontpage')
            ->where('content_id = ' . $dbo->quote($pk));

        return $dbo->setQuery($query)->execute();
    }

    /**
     * Check if the article passes the I2C word filters
     *
     * @return  bool  True if passes
     */
    protected function checkI2CWordFilters()
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $helper = new RssFactoryFilterHelper($configuration);
        $filter = $helper->getI2CWordFilter($this->getFeed());

        if (false === $filter) {
            return true;
        }

        if (!$this->passesAllowedWordsFilter($filter['allowed'])) {
            return false;
        }

        if (!$this->passesBannedWordsFilter($filter['banned'])) {
            return false;
        }

        if (!$this->passesExactWordsFilter($filter['exact'])) {
            return false;
        }

        return true;
    }

    /**
     * Check if the article passes the allowed words filter
     *
     * @param   array  $filter  Allowed words filter
     * @return  bool  True if passes
     */
    protected function passesAllowedWordsFilter($filter)
    {
        if (!$filter) {
            return true;
        }

        foreach ($filter as $word) {
            if (false !== mb_strpos($this->title . $this->introtext . $this->fulltext, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the article passes the banned words filter
     *
     * @param   array  $filter  Banned words filter
     * @return  bool  True if passes
     */
    protected function passesBannedWordsFilter($filter)
    {
        if (!$filter) {
            return true;
        }

        foreach ($filter as $word) {
            if (false !== mb_strpos($this->title . $this->introtext . $this->fulltext, $word)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the article passes the exact words filter
     *
     * @param   array  $filter  Exact words filter
     * @return  bool  True if passes
     */
    protected function passesExactWordsFilter($filter)
    {
        if (!$filter) {
            return true;
        }

        foreach ($filter as $word) {
            if (false === mb_strpos($this->title . $this->introtext . $this->fulltext, $word)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Perform word replacements based on filters
     */
    protected function wordReplacements()
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $helper = new RssFactoryFilterHelper($configuration);
        $filter = $helper->getI2CWordFilter($this->getFeed());

        if (false === $filter || !$filter['replacements']) {
            return true;
        }

        $patterns = [];
        $replacements = [];

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

    /**
     * Add "read more" link to the article
     */
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

    /**
     * Add relevant stories to the article
     *
     * @return  bool  True if successful
     */
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
