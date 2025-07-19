<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

class ReadmoreRule
{
    protected string $label = 'Read More';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'readmore';
    }

    public function getTemplate(): string
    {
        return '<hr id="system-readmore" />';
    }

    public function parse($params, $page, &$content, bool $debug): string
    {
        return '<hr id="system-readmore" />';
    }
}
