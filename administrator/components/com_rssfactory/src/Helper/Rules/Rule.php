<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;
use Joomla\Registry\Registry;
use Exception;

abstract class Rule
{
    protected ?string $type = null;
    protected string $label = '';

    public function __construct()
    {
    }

    public function getTemplate(string $order = '__i__', array $data = [], string $name = 'jform[params][i2c_rules]'): string
    {
        $html = [];
        $control = $name . '[' . $order . ']';
        $collapsed = $data['collapse'] ?? 0;
        $enabled = $data['enabled'] ?? 1;

        $form = $this->getForm($this->getType(), $order, $control, $data);

        $html[] = '<fieldset class="fieldset-rule">';
        $html[] = '<legend><i class="icon-move" style="cursor: pointer;"></i>&nbsp;' . $this->getLabel() . '</legend>';
        $html[] = '<div class="params" style="display: ' . ($collapsed ? 'none' : '') . '">';

        if ($form) {
            foreach ($form->getFieldset('params') as $field) {
                $html[] = '<div class="control-group">';
                $html[] = '<div class="control-label">' . $field->label . '</div>';
                $html[] = '<div class="controls">' . $field->input . '</div>';
                $html[] = '</div>';
            }
        }

        $html[] = '<input type="hidden" name="' . $control . '[type]" value="' . $this->getType() . '" />';
        $html[] = '<input type="hidden" name="' . $control . '[order]" value="' . $order . '" />';
        $html[] = '<input type="hidden" name="' . $control . '[collapse]" value="' . $collapsed . '" />';
        $html[] = '</div>';

        $html[] = '<div style="background-color: #cccccc; padding: 10px 10px 5px 10px; border-radius: 5px;">';
        $html[] = HTMLHelper::_(
            'select.genericlist',
            [
                ['value' => 0, 'text' => FactoryTextRss::_('rule_disabled')],
                ['value' => 1, 'text' => FactoryTextRss::_('rule_enabled')]
            ],
            $control . '[enabled]',
            'style="width: 100px;"',
            'value',
            'text',
            $enabled
        );

        $icon = $collapsed ? 'down' : 'up';
        $html[] = '<a href="#" class="btn btn-small btn-toggle-rule" style="vertical-align:top;"><i class="icon-arrow-' . $icon . '"></i>&nbsp;' . FactoryTextRss::_('rule_toggle') . '</a>';
        $html[] = '&nbsp;';
        $html[] = '<a href="#" class="btn btn-small button-remove-rule" style="vertical-align:top;"><i class="icon-delete"></i>&nbsp;' . FactoryTextRss::_('rule_remove') . '</a>';
        $html[] = '</div>';
        $html[] = '</fieldset>';

        return implode('', $html);
    }

    public static function getInstance(string $type): object
    {
        $class = 'Joomla\\Component\\Rssfactory\\Administrator\\Helper\\Rules\\' . ucfirst($type) . 'Rule';

        if (!class_exists($class)) {
            $file = __DIR__ . '/' . ucfirst($type) . 'Rule.php';

            if (!file_exists($file)) {
                throw new Exception(FactoryTextRss::sprintf('rule_not_found', $type));
            }

            require_once $file;
        }

        return new $class();
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        if ($this->type === null) {
            $classParts = explode('\\', static::class);
            $shortName = end($classParts); // e.g., SimpleMatchRule
            $this->type = strtolower(str_replace('Rule', '', $shortName));
        }

        return $this->type;
    }

    public function getParsedOutput($params, $page, &$content, bool $debug): string
    {
        $params = new Registry($params);
        $parsed = $this->parse($params, $page, $content, $debug);

        if (!$debug) {
            return $parsed;
        }

        return <<<HTML
<div class="rule-debug-title">{$this->getLabel()}</div>
<div class="rule-debug">
{$parsed}
</div>
HTML;
    }

    public function parse($params, $page, &$content, bool $debug)
    {
        return '';
    }

    protected function getForm(string $type, string $order, string $control, array $data): ?Form
    {
        $path = JPATH_COMPONENT_ADMINISTRATOR . '/src/Helper/Rules/';
        $formPath = $path . $type;

        if (!file_exists($formPath . '/' . $type . '.xml')) {
            return null;
        }

        Form::addFormPath($formPath);

        $form = Form::getInstance($type . '_' . $order, $type, ['control' => $control]);
        $form->bind($data);

        $this->setLabelAndDescription($form);

        return $form;
    }

    protected function setLabelAndDescription(Form $form): void
    {
        foreach ($form->getFieldset('params') as $field) {
            $label = $form->getFieldAttribute($field->fieldname, 'label', null, $field->group);
            if ($label === null) {
                $labelKey = 'rule_' . $this->getType() . '_' . str_replace('.', '_', $field->group) . '_' . $field->fieldname . '_label';
                $form->setFieldAttribute($field->fieldname, 'label', FactoryTextRss::_($labelKey), $field->group);
            }

            $desc = $form->getFieldAttribute($field->fieldname, 'description', null, $field->group);
            if ($desc === null) {
                $descKey = 'rule_' . $this->getType() . '_' . str_replace('.', '_', $field->group) . '_' . $field->fieldname . '_desc';
                $form->setFieldAttribute($field->fieldname, 'description', FactoryTextRss::_($descKey), $field->group);
            }
        }
    }

    protected function stripTags(bool $stripTags, string $allowedTags, string $content): string
    {
        if (!$stripTags) {
            return $content;
        }

        $tags = array_filter(array_map('trim', explode(',', $allowedTags)));
        $tags = array_map(fn($tag) => "<{$tag}>", $tags);

        return strip_tags($content, implode('', $tags));
    }
}
