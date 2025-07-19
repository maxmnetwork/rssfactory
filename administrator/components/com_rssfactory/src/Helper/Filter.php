<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\Component\Rssfactory\Administrator\Table\RssFactoryTableFeed;

/**
 * RSS Factory Filter Helper - Refactored for Joomla 4/5
 *
 * @since 4.3.6
 */
class RssFactoryFilterHelper
{
    /**
     * @var Registry
     */
    private $configuration;

    /**
     * Constructor with DI
     * 
     * @param Registry $configuration
     */
    public function __construct(Registry $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns word filter settings for a given feed
     * 
     * @param RssFactoryTableFeed $feed
     * @return array|false
     */
    public function getWordFilter(RssFactoryTableFeed $feed)
    {
        $feedFilterState = $feed->enablerefreshwordfilter;
        $globalFilterState = $this->configuration->get('enablerefreshwordfilter', 0);

        // Check if feed has specifically disabled the word filter
        if (!$feedFilterState) {
            return false;
        }

        // Check if feed is using global word filter state
        if (-1 == $feedFilterState && !$globalFilterState) {
            return false;
        }

        return [
            'allowed' => $this->getAllowedWordFilter($feed),
            'banned'  => $this->getBannedWordFilter($feed),
            'exact'   => $this->getExactWordFilter($feed),
        ];
    }

    /**
     * Returns I2C word filter settings for a given feed
     * 
     * @param RssFactoryTableFeed $feed
     * @return array|false
     */
    public function getI2CWordFilter(RssFactoryTableFeed $feed)
    {
        // Check if filter is enabled from settings
        if (!$this->configuration->get('enablei2cwordfilter', 0)) {
            return false;
        }

        // Check if filter is enabled for feed
        if (!$feed->i2c_enable_word_filter) {
            return false;
        }

        $filter = [
            'allowed'      => [],
            'banned'       => [],
            'exact'        => [],
            'replacements' => [],
        ];

        // Set allowed and banned filters
        $whiteList = trim($feed->i2c_words_white_list);
        $blackList = trim($feed->i2c_words_black_list);

        // Set allowed filter
        if ($whiteList) {
            $filter['allowed'] = strlen($whiteList) ? explode(',', $whiteList) : [];
        } elseif ('' != trim($this->configuration->get('i2callowedwords', ''))) {
            $filter['allowed'] = explode(',', trim($this->configuration->get('i2callowedwords', '')));
        }

        // Set banned filter
        if ($blackList) {
            $filter['banned'] = strlen($blackList) ? explode(',', $blackList) : [];
        } elseif ('' != trim($this->configuration->get('i2cbannedwords', ''))) {
            $filter['banned'] = explode(',', trim($this->configuration->get('i2cbannedwords', '')));
        }

        // Set exact filter
        if (trim($feed->i2c_words_exact_list)) {
            $filter['exact'] = explode(',', $feed->i2c_words_exact_list);
        }

        // Set replacements filter
        if (trim($feed->i2c_words_replacements)) {
            $filter['replacements'] = explode(',', $feed->i2c_words_replacements);
        }

        return $filter;
    }

    /**
     * Returns the allowed words filter for a given feed
     * 
     * @param RssFactoryTableFeed $feed
     * @return array
     */
    private function getAllowedWordFilter(RssFactoryTableFeed $feed)
    {
        $words = [];
        $allowed = $feed->refreshallowedwords;

        // Merge with global settings if required
        if ($feed->params->get('merge_refreshallowedwords', 0)) {
            $allowed = $this->configuration->get('refreshallowedwords') . ',' . $allowed;
        }

        $allowed = explode(',', $allowed);

        foreach ($allowed as $word) {
            $word = trim($word);

            if ('' != $word) {
                $words[] = $word;
            }
        }

        return $words;
    }

    /**
     * Returns the banned words filter for a given feed
     * 
     * @param RssFactoryTableFeed $feed
     * @return array
     */
    private function getBannedWordFilter(RssFactoryTableFeed $feed)
    {
        $words = [];
        $banned = $feed->refreshbannedwords;

        // Merge with global settings if required
        if ($feed->params->get('merge_refreshbannedwords', 0)) {
            $banned = $this->configuration->get('refreshbannedwords') . ',' . $banned;
        }

        $banned = explode(',', $banned);

        foreach ($banned as $word) {
            $word = trim($word);

            if ('' != $word) {
                $words[] = $word;
            }
        }

        return $words;
    }

    /**
     * Returns the exact match words filter for a given feed
     * 
     * @param RssFactoryTableFeed $feed
     * @return array
     */
    private function getExactWordFilter(RssFactoryTableFeed $feed)
    {
        $words = [];
        $exact = $feed->refreshexactmatchwords;

        // Merge with global settings if required
        if ($feed->params->get('merge_refreshexactmatchwords', 0)) {
            $exact = $this->configuration->get('refreshexactmatchwords') . "\n" . $exact;
        }

        $exact = explode("\n", $exact);

        foreach ($exact as $word) {
            $word = trim($word);

            if ('' != $word) {
                $words[] = $word;
            }
        }

        return $words;
    }
}
