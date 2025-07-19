<?php
// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Helper\Html;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;

class RssFactoryFeedsHtml
{
    protected static bool $assetsLoaded = false;

    /**
     * Display feeds based on mode.
     * 
     * @param array $feeds
     * @param array $config
     * @param mixed $ads
     * @return string
     */
    public static function display(array $feeds, array $config = [], $ads = null): string
    {
        if (!$feeds) {
            return Text::_('COM_RSSFACTORY_FEEDS_NO_FEEDS_FOUND');
        }

        $config = self::getConfig($config);
        $html = [];

        switch ($config['mode']) {
            case 'tiled':
                $html[] = self::displayTiled($feeds, $config, $ads);
                break;
            case 'tabbed':
                $html[] = self::displayTabbed($feeds, $config, $ads);
                break;
            case 'slider':
                $html[] = self::displaySliders($feeds, $config, $ads);
                break;
            case 'list':
                $html[] = self::displayList($feeds, $config, $ads);
                break;
        }

        return implode("\n", $html);
    }

    /**
     * Display stories based on configuration.
     * 
     * @param array $stories
     * @param Pagination|null $pagination
     * @param array $config
     * @param mixed $ads
     * @return string
     */
    public static function displayStories(array $stories, ?Pagination $pagination = null, array $config = [], $ads = null): string
    {
        if (!$stories) {
            return Text::_('COM_RSSFACTORY_FEED_NO_STORIES_FOUND');
        }

        $config = self::getConfig($config);

        self::loadAssets();

        $html = [];
        $html[] = '<div class="feed-stories">';
        $html[] = '<ul class="stories ' . ($config['voting'] ? 'voting' : '') . '">';

        foreach (array_values($stories) as $i => $story) {
            $html[] = self::displayStory($story, $config);
            $html[] = self::getRandomAd($ads, $i);
        }

        $html[] = '</ul>';
        $html[] = self::displayPagination($pagination, $config);
        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * Display votes for a story.
     * 
     * @param object $story
     * @param array $config
     * @return string
     */
    public static function displayStoryVotes(object $story, array $config): string
    {
        if (empty($config['voting'])) {
            return '';
        }

        $html = [];
        $html[] = '<div class="story-votes">';
        $html[] = '<div class="badge badge-secondary story-votes-counter">' . $story->votes_total . '</div>';

        if (!empty($config['votingArrows'])) {
            if (is_null($story->vote_value)) {
                $html[] = '<a href="' . Route::_('index.php?option=com_rssfactory&task=story.vote&format=raw&story_id=' . $story->id . '&vote=1') . '" class="text-muted muted small story-vote-up"><i class="icon-arrow-up"></i></a>';
                $html[] = '<a href="' . Route::_('index.php?option=com_rssfactory&task=story.vote&format=raw&story_id=' . $story->id . '&vote=-1') . '" class="text-muted muted small story-vote-down"><i class="icon-arrow-down"></i></a>';
            } else {
                $html[] = '<span class="small story-vote-up ' . (1 == $story->vote_value ? '' : 'muted text-muted') . '"><i class="icon-arrow-up"></i></span>';
                $html[] = '<span class="small story-vote-down ' . (-1 == $story->vote_value ? '' : 'muted text-muted') . '"><i class="icon-arrow-down"></i></span>';
            }
        }

        $html[] = '</div>';
        return implode("\n", $html);
    }

    /**
     * Display a single story.
     * 
     * @param object $story
     * @param array $config
     * @return string
     */
    protected static function displayStory(object $story, array $config): string
    {
        $original = clone($story);
        $story = self::changeStoryEncoding($story, $config);
        $story = self::prepareItemForDisplay($story, $config);

        $html = [];
        $html[] = '<li id="story-' . $story->id . '" class="story story-' . $story->id . '">';
        $html[] = self::displayStoryVotes($story, $config);
        $html[] = '<div class="story-link">';
        $html[] = self::displayStoryTitle($story, $config);
        $html[] = self::displayChannelTitle($story, $config);
        $html[] = self::displayStoryDate($story, $config);
        $html[] = self::displayStoryComments($story, $config);
        $html[] = self::displayStoryDescription($story, $config, $original);
        $html[] = '</div>';
        $html[] = '</li>';

        return implode("\n", $html);
    }

    /**
     * Get configuration options.
     * 
     * @param array $config
     * @return array
     */
    protected static function getConfig(array $config): array
    {
        $configuration = ComponentHelper::getParams('com_rssfactory');
        $defaults = [
            'mode'          => $configuration->get('liststyle', 'tiled'),
            'columns'       => $configuration->get('liststylecolumns', 2),
            'tabs_position' => 'top',
            'bookmarks'     => true,
            'voting'        => true,
            'comments'      => true,
            'dateFormat'    => $configuration->get('date_format', 'l, d F Y'),
            'date'          => !$configuration->get('hideDate', 0),
            'votingArrows'  => true,
            'pagination'    => true,
            'description_display'        => $configuration->get('showfeeddescription', 'tooltip'),
            'description_strip_tags'     => $configuration->get('strip_html_tags', 1),
            'description_allow_tags'     => $configuration->get('allowed_html_tags', ''),
            'force_output_charset'       => $configuration->get('force_output_charset', ''),
            'route_links'                => $configuration->get('readmore_options', 0),
            'story_source_link_target'   => $configuration->get('story_source_link_target', 'new_window'),
            'story_source_link_behavior' => $configuration->get('story_source_link_behavior', 'link'),
            'use_favicons'               => $configuration->get('use_favicons', 1),
            'show_enclosures'            => $configuration->get('show_enclosures', 0),
            'show_empty_feeds'           => $configuration->get('showemptysources', 0),
            'story_title_trim'           => 0,
            'story_desc_trim'            => 0,
            'list_style_channel_title_display' => $configuration->get('list_style_channel_title_display', 1),
        ];

        $config = array_merge($defaults, $config);

        // Permissions (Joomla 4 ACL checks)
        $user = Factory::getApplication()->getIdentity();
        if ($config['bookmarks'] && !$user->authorise('frontend.favorites', 'com_rssfactory')) {
            $config['bookmarks'] = false;
        }
        if ($config['voting'] && !$user->authorise('frontend.voting', 'com_rssfactory')) {
            $config['voting'] = false;
        }
        if ($config['comments'] && !$user->authorise('frontend.comment.view', 'com_rssfactory')) {
            $config['comments'] = false;
        }

        if (is_array($config['columns'])) {
            $config['columns'] = $config['columns'][0];
        }

        return $config;
    }

    /**
     * Load assets only once.
     * 
     * @return bool
     */
    protected static function loadAssets(): bool
    {
        if (!self::$assetsLoaded) {
            HTMLHelper::_('jquery.framework');
            HTMLHelper::_('stylesheet', 'com_rssfactory/feeds.css', ['version' => 'auto', 'relative' => true]);
            HTMLHelper::_('script', 'com_rssfactory/feeds.js', ['version' => 'auto', 'relative' => true]);
            HTMLHelper::_('script', 'com_rssfactory/growl.js', ['version' => 'auto', 'relative' => true]);
            self::$assetsLoaded = true;
        }
        return true;
    }

    /**
     * Display pagination links.
     * 
     * @param Pagination $pagination
     * @param array $config
     * @return string
     */
    protected static function displayPagination(Pagination $pagination, array $config): string
    {
        if (empty($config['pagination']) || !$pagination instanceof Pagination) {
            return '';
        }

        $html = [];
        $html[] = '<div class="pagination pagination-small pagination-sm">';
        $html[] = $pagination->getPagesLinks();
        $html[] = '</div>';
        $html[] = '<div class="progress progress-striped active" style="display: none;">';
        $html[] = '<div class="bar" style="width: 100%;"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
