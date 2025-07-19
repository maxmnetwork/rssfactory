<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Rule;

class RssFactoryRulePageBreak extends Rule
{
    protected string $label = 'Page Break';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'pagebreak';
    }

    public function getTemplate(): string
    {
        return '<hr title="{title}" alt="{alias}" class="system-pagebreak" />';
    }

    public function parse($params, $page, &$content, bool $debug): string
    {
        $title = htmlspecialchars($params->get('title'), ENT_QUOTES);
        $alias = htmlspecialchars($params->get('alias'), ENT_QUOTES);

        return '<hr title="' . $title . '" alt="' . $alias . '" class="system-pagebreak" />';
    }
}
