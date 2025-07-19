<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use Joomla\Component\Rssfactory\Administrator\Helper\Rule;

class LinebreakRule extends Rule
{
    protected string $label = 'Line Break';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'linebreak';
    }

    public function getTemplate(): string
    {
        return '<div style="height:{height}px"></div>';
    }

    public function parse($params, $page, &$content, bool $debug): string
    {
        $height = (int) $params->get('height', 0);
        return '<div style="height:' . $height . 'px"></div>';
    }
}
