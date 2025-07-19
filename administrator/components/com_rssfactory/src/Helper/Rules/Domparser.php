<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Rule;
use Joomla\Registry\Registry;
use voku\helper\HtmlDomParser;

class DomparserRule extends Rule
{
    protected string $label = 'Dom Parser';

    public function parse(Registry $params, string $page, &$content, bool $debug): string|false
    {
        $selector    = $params->get('selector');
        $index       = $params->get('index', 0);
        $stripHtml   = $params->get('strip_html', 0);
        $allowedTags = $params->get('allowed_tags', '');

        $html = HtmlDomParser::str_get_html($page);

        if (!$html) {
            return false;
        }

        $element = $html->find($selector, $index);

        if (!$element) {
            return false;
        }

        $text = $element->innertext;

        return $this->stripTags($stripHtml, $allowedTags, $text);
    }
}
