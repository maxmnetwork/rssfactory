<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\Folder;

class HtmlRulesRegistry
{
    protected static ?array $rules = null;

    public static function options(): array
    {
        $options = [];

        foreach (self::getRules() as $rule) {
            $options[$rule->getType()] = $rule->getLabel();
        }

        return $options;
    }

    public static function templates(): string
    {
        $templates = [];

        foreach (self::getRules() as $rule) {
            $templates[] = 'data-template-' . $rule->getType() . '="' . htmlspecialchars($rule->getTemplate(), ENT_QUOTES) . '"';
        }

        return implode(' ', $templates);
    }

    protected static function getRules(): array
    {
        if (self::$rules === null) {
            self::$rules = [];

            $path = JPATH_COMPONENT_ADMINISTRATOR . '/src/Helper/Rules';
            $folders = Folder::folders($path);

            foreach ($folders as $folder) {
                $className = 'RssFactoryRule' . ucfirst($folder);

                // Attempt autoload; fallback to manual
                if (!class_exists($className)) {
                    $file = $path . '/' . $folder . '.php';

                    if (file_exists($file)) {
                        require_once $file;
                    }
                }

                if (class_exists($className)) {
                    self::$rules[$folder] = new $className();
                }
            }
        }

        return self::$rules;
    }
}
