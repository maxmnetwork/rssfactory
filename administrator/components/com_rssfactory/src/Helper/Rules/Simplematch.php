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

class SimpleMatchRule
{
    protected string $label = 'Simple Match';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'simplematch';
    }

    public function getTemplate(): string
    {
        return '<div>{simplematch}</div>';
    }

    public function parse($params, string $page, &$content, bool $debug): string
    {
        $csStart     = (bool) $params->get('start.case_sensitive', 0);
        $csEnd       = (bool) $params->get('end.case_sensitive', 0);
        $startFrom   = $params->get('start.position');
        $endAt       = $params->get('end.position');
        $stripHtml   = (bool) $params->get('strip_html', 0);
        $allowedTags = $params->get('allowed_tags', '');

        if (!$startFrom || !$endAt) {
            return '';
        }

        // Find start and end positions
        $start = $csStart ? strpos($page, $startFrom) : stripos($page, $startFrom);
        $end   = $csEnd ? strpos($page, $endAt) : stripos($page, $endAt);

        if ($start === false || $end === false || $end <= $start) {
            return '';
        }

        $text = substr($page, $start, $end - $start);

        return $this->stripTags($stripHtml, $allowedTags, $text);
    }

    protected function stripTags(bool $stripHtml, string $allowedTags, string $text): string
    {
        if (!$stripHtml) {
            return $text;
        }

        return strip_tags($text, $allowedTags);
    }
}
