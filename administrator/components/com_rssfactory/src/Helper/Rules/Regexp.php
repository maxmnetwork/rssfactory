<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

class RegexpRule
{
    protected string $label = 'Regular Expression';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'regexp';
    }

    public function getTemplate(): string
    {
        return '<div>{match}</div>';
    }

    public function parse($params, $page, &$content, bool $debug)
    {
        $expression   = $params->get('expression');
        $matchIndex   = $params->get('match', 0);
        $stripHtml    = $params->get('strip_html', 0);
        $allowedTags  = $params->get('allowed_tags', '');

        if (!preg_match($expression, $page, $matches)) {
            return '';
        }

        if (!isset($matches[$matchIndex])) {
            return '';
        }

        $text = $matches[$matchIndex];

        return $this->stripTags($stripHtml, $allowedTags, $text);
    }

    protected function stripTags(bool $stripTags, string $allowedTags, string $content): string
    {
        if (!$stripTags) {
            return $content;
        }

        // Prepare allowed tags
        $tags = array_filter(array_map('trim', explode(',', $allowedTags)));
        $allowed = implode('', array_map(fn($tag) => "<$tag>", $tags));

        return strip_tags($content, $allowed);
    }
}
