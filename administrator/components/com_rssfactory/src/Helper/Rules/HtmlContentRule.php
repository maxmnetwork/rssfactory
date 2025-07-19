<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\Component\Rssfactory\Administrator\Helper\Rule;

class HtmlContentRule extends Rule
{
    protected string $label = 'Html';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'html';
    }

    public function getTemplate(): string
    {
        return '<div>{html}</div>';
    }

    public function parse(Registry $params, string $page, &$content, bool $debug): string
    {
        return $params->get('html', '');
    }
}
