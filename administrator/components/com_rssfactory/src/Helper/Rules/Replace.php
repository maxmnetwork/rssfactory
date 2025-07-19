<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

class ReplaceRule
{
    protected string $label = 'Search & Replace';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'replace';
    }

    public function getTemplate(): string
    {
        return '<div>{content}</div>'; // You can update this as needed
    }

    public function parse($params, $page, &$content, bool $debug)
    {
        $search        = $params->get('search');
        $replace       = $params->get('replace');
        $caseSensitive = $params->get('case_sensitive', 0);

        if ($debug) {
            return FactoryTextRss::sprintf('rule_replace_debug_info', $search, $replace);
        }

        $output = [];

        foreach ((array) $content as $text) {
            if ($caseSensitive) {
                $output[] = str_replace($search, $replace, $text);
            } else {
                $output[] = str_ireplace($search, $replace, $text);
            }
        }

        $content = $output;

        return implode("\n", $output);
    }
}
