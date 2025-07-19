<?php

namespace Joomla\Component\Rssfactory\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;

/**
 * Custom field type for RSS Factory that extends ListField.
 */
class RssFactoryExtendListField extends ListField
{
    private ?\SimpleXMLElement $xml = null;

    /**
     * Get the field options.
     *
     * @return array The field option objects.
     */
    protected function getOptions(): array
    {
        Factory::getLanguage()->load('com_rssfactory');

        $options = [];

        if ($this->useGlobal()) {
            $options[] = $this->getGlobalOption();
        }

        $options = array_merge($options, $this->getParentOptions(), parent::getOptions());

        // Convert arrays to \Joomla\CMS\Form\FormFieldOption objects
        return array_map(function ($option) {
            return is_array($option) ? (object) $option : $option;
        }, $options);
    }

    /**
     * Check if the global option should be used.
     *
     * @return bool
     */
    private function useGlobal(): bool
    {
        $useGlobal = $this->element['useGlobal'] ?? 'true';
        return $useGlobal !== 'false';
    }

    /**
     * Get the global option.
     *
     * @return array
     */
    private function getGlobalOption(): array
    {
        $settings = ComponentHelper::getParams('com_rssfactory');
        $extendedFieldName = (string) $this->element['extend'];
        $globalValue = $settings->get($extendedFieldName);

        $optionNode = $this->getXml()->xpath('//field[@name="' . $extendedFieldName . '"]/option[@value="' . $globalValue . '"]');
        $globalName = '';
        if (!empty($optionNode)) {
            $globalName = Text::_((string) $optionNode[0]);
        }

        return [
            'value' => 'global',
            'text'  => Text::sprintf('COM_RSSFACTORY_USE_GLOBAL', $globalName),
        ];
    }

    /**
     * Get the parent options from the XML.
     *
     * @return array
     */
    private function getParentOptions(): array
    {
        $extendedFieldName = (string) $this->element['extend'];
        $options = [];

        $optionNodes = $this->getXml()->xpath('//field[@name="' . $extendedFieldName . '"]/option');
        if (!empty($optionNodes)) {
            foreach ($optionNodes as $option) {
                $options[] = [
                    'value' => (string) $option['value'],
                    'text'  => Text::_((string) $option),
                ];
            }
        }

        return $options;
    }

    /**
     * Get the configuration XML.
     *
     * @return \SimpleXMLElement
     */
    private function getXml(): \SimpleXMLElement
    {
        if ($this->xml === null) {
            $configPath = JPATH_ADMINISTRATOR . '/components/com_rssfactory/configuration.xml';
            if (!File::exists($configPath)) {
                throw new \RuntimeException('Configuration XML not found: ' . $configPath);
            }
            $this->xml = simplexml_load_file($configPath);
        }

        return $this->xml;
    }
}
