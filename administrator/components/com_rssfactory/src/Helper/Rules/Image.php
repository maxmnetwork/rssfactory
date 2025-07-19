<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use voku\helper\HtmlDomParser;
use Joomla\Component\Rssfactory\Administrator\Helper\Rule;

class ImageRule extends Rule
{
    protected string $label = 'Image Match';

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return 'image';
    }

    public function getTemplate(): string
    {
        return '<img src="{src}" style="max-width:{width}px; max-height:{height}px;" />';
    }

    public function parse($params, $page, &$content, bool $debug): string
    {
        $html = [];
        $counter = 0;

        $index       = (int) $params->get('index', 0);
        $filter      = (string) $params->get('filter', '');
        $selector    = (string) $params->get('selector', '');
        $maxWidth    = (int) $params->get('resize.width', 0);
        $maxHeight   = (int) $params->get('resize.height', 0);
        $srcPrepend  = (string) $params->get('src.prepend', '');

        $dom = HtmlDomParser::str_get_html($page);

        if (!$dom) {
            return '';
        }

        $images = $dom->find($selector . ' img');

        foreach ($images as $image) {
            if ($filter !== '') {
                if (
                    stripos($image->src ?? '', $filter) === false &&
                    stripos($image->alt ?? '', $filter) === false &&
                    stripos($image->title ?? '', $filter) === false
                ) {
                    continue;
                }
            }

            $counter++;

            if ($index > 0 && $index !== $counter) {
                continue;
            }

            $style = [];

            if ($maxWidth > 0) {
                $style[] = 'max-width: ' . $maxWidth . 'px;';
            }

            if ($maxHeight > 0) {
                $style[] = 'max-height: ' . $maxHeight . 'px;';
            }

            $src = $this->getSource($image->src ?? '', $srcPrepend);
            $html[] = '<img src="' . htmlspecialchars($src, ENT_QUOTES) . '" style="' . implode(' ', $style) . '" />';
        }

        return implode("\n", $html);
    }

    private function getSource(string $source, string $prepend): string
    {
        if ($prepend === '') {
            return $source;
        }

        if (strpos($source, $prepend) === 0) {
            return $source;
        }

        return $prepend . $source;
    }
}
