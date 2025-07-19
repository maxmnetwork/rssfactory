<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Helper\Factory;

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class FactoryFilter
{
    /**
     * @var Registry
     */
    protected Registry $configuration;

    /**
     * FactoryFilter constructor.
     * 
     * @param Registry $configuration
     */
    public function __construct(Registry $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get the word filter for a feed (refresh).
     * Returns array with allowed, banned, exact words or false if disabled.
     *
     * @param object $feed
     * @return array|false
     */
    public function getWordFilter(object $feed)
    {
        $feedFilterState = $feed->enablerefreshwordfilter ?? 0;
        $globalFilterState = $this->configuration->get('enablerefreshwordfilter', 0);

        if (!$feedFilterState) {
            return false;
        }
        if ($feedFilterState == -1 && !$globalFilterState) {
            return false;
        }

        return [
            'allowed' => $this->getAllowedWordFilter($feed),
            'banned'  => $this->getBannedWordFilter($feed),
            'exact'   => $this->getExactWordFilter($feed),
        ];
    }

    /**
     * Get allowed words for feed refresh filter.
     *
     * @param object $feed
     * @return array
     */
    protected function getAllowedWordFilter(object $feed): array
    {
        $words = [];
        $allowed = $feed->refreshallowedwords ?? '';

        if (!empty($feed->params) && $feed->params->get('merge_refreshallowedwords', 0)) {
            $allowed = $this->configuration->get('refreshallowedwords', '') . ',' . $allowed;
        }

        foreach (explode(',', $allowed) as $word) {
            $word = trim($word);
            if ($word !== '') {
                $words[] = $word;
            }
        }
        return $words;
    }

    /**
     * Get banned words for feed refresh filter.
     *
     * @param object $feed
     * @return array
     */
    protected function getBannedWordFilter(object $feed): array
    {
        $words = [];
        $banned = $feed->refreshbannedwords ?? '';

        if (!empty($feed->params) && $feed->params->get('merge_refreshbannedwords', 0)) {
            $banned = $this->configuration->get('refreshbannedwords', '') . ',' . $banned;
        }

        foreach (explode(',', $banned) as $word) {
            $word = trim($word);
            if ($word !== '') {
                $words[] = $word;
            }
        }
        return $words;
    }

    /**
     * Get exact match words for feed refresh filter.
     *
     * @param object $feed
     * @return array
     */
    protected function getExactWordFilter(object $feed): array
    {
        $words = [];
        $exact = $feed->refreshexactmatchwords ?? '';

        if (!empty($feed->params) && $feed->params->get('merge_refreshexactmatchwords', 0)) {
            $exact = $this->configuration->get('refreshexactmatchwords', '') . "\n" . $exact;
        }

        foreach (explode("\n", $exact) as $word) {
            $word = trim($word);
            if ($word !== '') {
                $words[] = $word;
            }
        }
        return $words;
    }

    /**
     * Get Import2Content word filter for a feed.
     * Returns array with allowed, banned, exact, replacements or false if disabled.
     *
     * @param object $feed
     * @return array|false
     */
    public function getI2CWordFilter(object $feed)
    {
        if (!$this->configuration->get('enablei2cwordfilter', 0)) {
            return false;
        }
        if (empty($feed->i2c_enable_word_filter)) {
            return false;
        }

        $filter = [
            'allowed'      => [],
            'banned'       => [],
            'exact'        => [],
            'replacements' => [],
        ];

        $whiteList = trim($feed->i2c_words_white_list ?? '');
        $blackList = trim($feed->i2c_words_black_list ?? '');

        if ($whiteList) {
            $filter['allowed'] = explode(',', $whiteList);
        } elseif ('' !== trim($this->configuration->get('i2callowedwords', ''))) {
            $filter['allowed'] = explode(',', trim($this->configuration->get('i2callowedwords', '')));
        }

        if ($blackList) {
            $filter['banned'] = explode(',', $blackList);
        } elseif ('' !== trim($this->configuration->get('i2cbannedwords', ''))) {
            $filter['banned'] = explode(',', trim($this->configuration->get('i2cbannedwords', '')));
        }

        if (!empty($feed->i2c_words_exact_list)) {
            $filter['exact'] = explode(',', $feed->i2c_words_exact_list);
        }
        if (!empty($feed->i2c_words_replacements)) {
            $filter['replacements'] = explode(',', $feed->i2c_words_replacements);
        }

        return $filter;
    }
}
