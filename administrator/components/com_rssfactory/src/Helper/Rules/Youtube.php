<?php

/**
 * @package     Joomla.Component.Rssfactory
 * @subpackage  Administrator.Helper.Rules
 * @version     4.3.6
 * @author      thePHPfactory
 * @copyright   Copyright (C) 2011 SKEPSIS Consult SRL
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use voku\helper\HtmlDomParser;

class YouTubeRule
{
    protected string $label = 'YouTube Match';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'youtube';
    }

    public function getTemplate(): string
    {
        return '<div>{youtube}</div>';
    }

    public function parse($params, string $page, &$content, bool $debug): string
    {
        $html = [];
        $index    = (int) $params->get('index', 1);
        $selector = $params->get('selector', '');
        $height   = $params->get('resize.height');
        $width    = $params->get('resize.width');
        $counter  = 0;

        if (empty($selector)) {
            return '';
        }

        $dom = HtmlDomParser::str_get_html($page);

        if (!$dom) {
            return '';
        }

        $iframes = $dom->find($selector . ' iframe');

        foreach ($iframes as $iframe) {
            if (strpos($iframe->getAttribute('src'), 'http://www.youtube.com/') === false) {
                continue;
            }

            $counter++;

            if ($index && $index !== $counter) {
                continue;
            }

            $style = [];

            if ($height) {
                $style[] = 'max-height: ' . (int) $height . 'px;';
            }

            if ($width) {
                $style[] = 'max-width: ' . (int) $width . 'px;';
            }

            if (!empty($style)) {
                $iframe->setAttribute('style', implode(' ', $style));
            }

            $html[] = $iframe->outertext;
        }

        return implode("\n", $html);
    }
}
