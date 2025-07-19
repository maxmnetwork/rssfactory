<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * RSS Factory Feeds Helper
 */
class FeedsHelper
{
    /**
     * Get the feed icon URL or HTML <img> tag.
     *
     * @param int $feedId
     * @param string|false $url
     * @param array $attributes
     * @return string
     */
    public static function icon($feedId, $url = false, array $attributes = [])
    {
        $filename = 'default.png';
        $path = JPATH_SITE . '/media/com_rssfactory/icos/ico_' . md5($feedId) . '.png';

        // Check if the icon file exists and use it, otherwise fallback to default
        if (File::exists($path)) {
            $filename = 'ico_' . md5($feedId) . '.png';
        }

        $src = Uri::root() . 'media/com_rssfactory/icos/' . $filename;

        if ($url) {
            return $src;
        }

        // Return the image HTML tag with the given attributes
        return HTMLHelper::_('image', $src, 'ico' . $feedId, $attributes);
    }

    /**
     * Get the feed title (with fallback to "Untitled Feed").
     *
     * @param object $feed
     * @return string
     */
    public static function getTitle($feed)
    {
        // Fallback to a default title if the feed title is empty
        return !empty($feed->title) ? $feed->title : 'Untitled Feed';
    }

    /**
     * Get the feed status label.
     *
     * @param object $feed
     * @return string
     */
    public static function getStatus($feed)
    {
        // Return 'Published' if the feed is published, otherwise 'Unpublished'
        return $feed->published ? 'Published' : 'Unpublished';
    }

    // Additional helper methods for RSS Factory feeds business logic can be added as needed.
}
