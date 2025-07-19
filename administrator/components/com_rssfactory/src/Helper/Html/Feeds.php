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

namespace Joomla\Component\Rssfactory\Administrator\Helper\Html;

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

class Feeds
{
    /**
     * Get the icon URL or HTML <img> tag for the feed.
     *
     * @param   int     $feedId      The feed ID
     * @param   bool    $url         If true, return the URL instead of the image tag
     * @param   array   $attributes  Additional attributes for the image tag
     *
     * @return  string  The icon URL or the image HTML tag
     */
    public static function icon(int $feedId, bool $url = false, array $attributes = []): string
    {
        $filename = 'default.png';
        $path = JPATH_SITE . '/media/com_rssfactory/icos/ico_' . md5($feedId) . '.png';

        // Check if the feed icon exists
        if (File::exists($path)) {
            $filename = 'ico_' . md5($feedId) . '.png';
        }

        $src = Uri::root() . 'media/com_rssfactory/icos/' . $filename;

        if ($url) {
            return $src;
        }

        // Return the image HTML tag with the icon source
        return HTMLHelper::_('image', $src, 'ico' . $feedId, $attributes);
    }
}
