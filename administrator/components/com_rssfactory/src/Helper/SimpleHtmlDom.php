<?php
// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * SimpleHtmlDom helper class
 *
 * Handles parsing and manipulation of HTML DOM.
 */
class SimpleHtmlDom
{
    // Constants for node types
    const HDOM_TYPE_ELEMENT = 1;
    const HDOM_TYPE_COMMENT = 2;
    const HDOM_TYPE_TEXT = 3;
    const HDOM_TYPE_ENDTAG = 4;
    const HDOM_TYPE_ROOT = 5;
    const HDOM_TYPE_UNKNOWN = 6;
    
    const HDOM_QUOTE_DOUBLE = 0;
    const HDOM_QUOTE_SINGLE = 1;
    const HDOM_QUOTE_NO = 3;

    const HDOM_INFO_BEGIN = 0;
    const HDOM_INFO_END = 1;
    const HDOM_INFO_QUOTE = 2;
    const HDOM_INFO_SPACE = 3;
    const HDOM_INFO_TEXT = 4;
    const HDOM_INFO_INNER = 5;
    const HDOM_INFO_OUTER = 6;
    const HDOM_INFO_ENDSPACE = 7;

    const DEFAULT_TARGET_CHARSET = 'UTF-8';
    const DEFAULT_BR_TEXT = "\r\n";
    const DEFAULT_SPAN_TEXT = " ";
    const MAX_FILE_SIZE = 600000;

    /**
     * Get HTML DOM from a file
     */
    public static function file_get_html($url, $use_include_path = false, $context = null, $offset = -1, $maxLen = -1, $lowercase = true, $forceTagsClosed = true, $target_charset = self::DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = self::DEFAULT_BR_TEXT, $defaultSpanText = self::DEFAULT_SPAN_TEXT)
    {
        $dom = new SimpleHtmlDomNode(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);

        $contents = file_get_contents($url, $use_include_path, $context, $offset);

        if (empty($contents) || strlen($contents) > self::MAX_FILE_SIZE) {
            return false;
        }

        $dom->load($contents, $lowercase, $stripRN);
        return $dom;
    }

    /**
     * Get HTML DOM from a string
     */
    public static function str_get_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = self::DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = self::DEFAULT_BR_TEXT, $defaultSpanText = self::DEFAULT_SPAN_TEXT)
    {
        $dom = new SimpleHtmlDomNode(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);

        if (empty($str) || strlen($str) > self::MAX_FILE_SIZE) {
            $dom->clear();
            return false;
        }

        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }

    /**
     * Dump the HTML DOM tree
     */
    public static function dump_html_tree($node, $show_attr = true, $deep = 0)
    {
        $node->dump($node);
    }
}

class SimpleHtmlDomNode
{
    public $nodetype = SimpleHtmlDom::HDOM_TYPE_TEXT;
    public $tag = 'text';
    public $attr = [];
    public $children = [];
    public $nodes = [];
    public $parent = null;
    public $_ = [];
    public $tag_start = 0;
    private $dom = null;

    function __construct($dom)
    {
        $this->dom = $dom;
        $dom->nodes[] = $this;
    }

    function __destruct()
    {
        $this->clear();
    }

    function __toString()
    {
        return $this->outertext();
    }

    function clear()
    {
        $this->dom = null;
        $this->nodes = null;
        $this->parent = null;
        $this->children = null;
    }

    /**
     * Dump the node's tree for debugging
     */
    function dump($show_attr = true, $deep = 0)
    {
        $lead = str_repeat('    ', $deep);
        echo $lead . $this->tag;
        if ($show_attr && count($this->attr) > 0) {
            echo '(';
            foreach ($this->attr as $k => $v) {
                echo "[$k]=>\"" . $this->$k . '", ';
            }
            echo ')';
        }
        echo "\n";

        if ($this->nodes) {
            foreach ($this->nodes as $c) {
                $c->dump($show_attr, $deep + 1);
            }
        }
    }

    function parent($parent = null)
    {
        if ($parent !== null) {
            $this->parent = $parent;
            $this->parent->nodes[] = $this;
            $this->parent->children[] = $this;
        }
        return $this->parent;
    }

    function has_child()
    {
        return !empty($this->children);
    }

    function children($idx = -1)
    {
        if ($idx === -1) {
            return $this->children;
        }
        if (isset($this->children[$idx])) return $this->children[$idx];
        return null;
    }

    function first_child()
    {
        return count($this->children) > 0 ? $this->children[0] : null;
    }

    function last_child()
    {
        return count($this->children) > 0 ? $this->children[count($this->children) - 1] : null;
    }

    function next_sibling()
    {
        if ($this->parent === null) return null;

        $idx = 0;
        $count = count($this->parent->children);
        while ($idx < $count && $this !== $this->parent->children[$idx]) {
            ++$idx;
        }
        return (++$idx < $count) ? $this->parent->children[$idx] : null;
    }

    function prev_sibling()
    {
        if ($this->parent === null) return null;

        $idx = 0;
        $count = count($this->parent->children);
        while ($idx < $count && $this !== $this->parent->children[$idx]) {
            ++$idx;
        }
        return (--$idx >= 0) ? $this->parent->children[$idx] : null;
    }

    function find_ancestor_tag($tag)
    {
        $returnDom = $this;
        while ($returnDom !== null) {
            if ($returnDom->tag == $tag) break;
            $returnDom = $returnDom->parent;
        }
        return $returnDom;
    }

    function innertext()
    {
        if (isset($this->_[SimpleHtmlDom::HDOM_INFO_INNER])) return $this->_[SimpleHtmlDom::HDOM_INFO_INNER];
        if (isset($this->_[SimpleHtmlDom::HDOM_INFO_TEXT])) return $this->dom->restore_noise($this->_[SimpleHtmlDom::HDOM_INFO_TEXT]);

        return implode('', array_map(fn($n) => $n->outertext(), $this->nodes));
    }

    function outertext()
    {
        if (isset($this->_[SimpleHtmlDom::HDOM_INFO_OUTER])) return $this->_[SimpleHtmlDom::HDOM_INFO_OUTER];
        if (isset($this->_[SimpleHtmlDom::HDOM_INFO_TEXT])) return $this->dom->restore_noise($this->_[SimpleHtmlDom::HDOM_INFO_TEXT]);

        $ret = '<' . $this->tag;
        foreach ($this->attr as $key => $val) {
            $ret .= " $key=\"$val\"";
        }
        $ret .= '>';
        $ret .= $this->innertext();
        $ret .= "</{$this->tag}>";
        return $ret;
    }

    function text()
    {
        return isset($this->_[SimpleHtmlDom::HDOM_INFO_INNER]) ? $this->_[SimpleHtmlDom::HDOM_INFO_INNER] : $this->dom->restore_noise($this->_[SimpleHtmlDom::HDOM_INFO_TEXT]);
    }

    function getAttribute($name)
    {
        return $this->attr[$name] ?? null;
    }

    function setAttribute($name, $value)
    {
        $this->attr[$name] = $value;
    }
}
