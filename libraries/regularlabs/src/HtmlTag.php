<?php

/**
 * @package         Regular Labs Library
 * @version         24.6.11852
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */
namespace RegularLabs\Library;

defined('_JEXEC') or die;
class HtmlTag
{
    /**
     * Combine 2 opening html tags into one
     */
    public static function combine(string $tag1, string $tag2): string
    {
        // Return if tags are the same
        if ($tag1 == $tag2) {
            return $tag1;
        }
        if (!\RegularLabs\Library\RegEx::match('<([a-z][a-z0-9]*)', $tag1, $tag_type)) {
            return $tag2;
        }
        $tag_type = $tag_type[1];
        $attribs = self::combineAttributes($tag1, $tag2);
        if (!$attribs) {
            return '<' . $tag_type . '>';
        }
        return '<' . $tag_type . ' ' . $attribs . '>';
    }
    /**
     * Combine attribute values from 2 given html tag strings (or arrays of attributes)
     * And return as a string of attributes (if $flatten = true)
     */
    public static function combineAttributes(string|array $string1, string|array $string2, bool $flatten = \true): string|array
    {
        $attribsutes1 = is_array($string1) ? $string1 : self::getAttributes($string1);
        $attribsutes2 = is_array($string2) ? $string2 : self::getAttributes($string2);
        $duplicate_attributes = array_intersect_key($attribsutes1, $attribsutes2);
        // Fill $attributes with the unique ids
        $attributes = array_diff_key($attribsutes1, $attribsutes2) + array_diff_key($attribsutes2, $attribsutes1);
        // List of attrubute types that can only contain one value
        $single_value_attributes = ['id', 'href'];
        // Add/combine the duplicate ids
        foreach ($duplicate_attributes as $key => $val) {
            if (in_array($key, $single_value_attributes, \true)) {
                $attributes[$key] = $attribsutes2[$key];
                continue;
            }
            // Combine strings, but remove duplicates
            // "aaa bbb" + "aaa ccc" = "aaa bbb ccc"
            // use a ';' as a concatenated for javascript values (keys beginning with 'on')
            // Otherwise use a space (like for classes)
            $glue = str_starts_with($key, 'on') ? ';' : ' ';
            $attributes[$key] = implode($glue, [...explode($glue, $attribsutes1[$key]), ...explode($glue, $attribsutes2[$key])]);
        }
        return $flatten ? self::flattenAttributes($attributes) : $attributes;
    }
    /**
     * Convert array or object of attributes to a html style string
     */
    public static function flattenAttributes(array|object $attributes, string $prefix = ''): string
    {
        $output = [];
        foreach ($attributes as $key => $val) {
            if (is_null($val) || $val === '') {
                continue;
            }
            if ($val === \false) {
                $val = 'false';
            }
            if ($val === \true) {
                $val = 'true';
            }
            $val = str_replace('"', '&quot;', $val);
            $output[] = $prefix . $key . '="' . $val . '"';
        }
        return implode(' ', $output);
    }
    /**
     * Extract attribute value from a html tag string by given attribute key
     */
    public static function getAttributeValue(string $key, string $string): string
    {
        if (empty($key) || empty($string)) {
            return '';
        }
        \RegularLabs\Library\RegEx::match(\RegularLabs\Library\RegEx::quote($key) . '="([^"]*)"', $string, $match);
        if (empty($match)) {
            return '';
        }
        return $match[1];
    }
    /**
     * Extract all attributes from a html tag string
     */
    public static function getAttributes(string $string): array
    {
        if (empty($string)) {
            return [];
        }
        \RegularLabs\Library\RegEx::matchAll('([a-z0-9-_]+)="([^"]*)"', $string, $matches);
        if (empty($matches)) {
            return [];
        }
        $attribs = [];
        foreach ($matches as $match) {
            $attribs[$match[1]] = $match[2];
        }
        return $attribs;
    }
    /**
     * Returns true/false based on whether the html tag type is a single tag
     */
    public static function isSelfClosingTag(string $type): bool
    {
        return in_array($type, ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'menuitem', 'meta', 'param', 'source', 'track', 'wbr'], \true);
    }
}
