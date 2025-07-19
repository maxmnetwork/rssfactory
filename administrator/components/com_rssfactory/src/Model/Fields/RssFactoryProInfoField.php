<?php

namespace Joomla\Component\Rssfactory\Administrator\Model\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\Factory;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;

/**
 * RssFactoryProInfoField class
 * Custom field for displaying various RSS Factory Pro information
 */
class RssFactoryProInfoField extends TextField
{
    protected $type = 'RssFactoryProInfo';

    /**
     * Get the field label
     *
     * @return string
     */
    protected function getLabel(): string
    {
        if ((string) $this->element['hasLabel'] === 'false') {
            return '';
        }

        return parent::getLabel();
    }

    /**
     * Get the input HTML for the field
     *
     * @return string
     */
    protected function getInput(): string
    {
        $output = [];

        $output[] = '<div id="' . htmlspecialchars((string) $this->element['name'], ENT_QUOTES, 'UTF-8') . '">';
        $output[] = $this->getOutput((string) $this->element['option']);
        $output[] = '</div>';

        return implode("\n", $output);
    }

    /**
     * Generate the output based on the option
     *
     * @param string $option The option to determine the output
     * @return string
     */
    protected function getOutput(string $option): string
    {
        $output = [];
        /** @var DatabaseInterface $db */
        $db = Factory::getContainer()->get('db');

        switch ($option) {
            case 'cache_content':
                $query = $db->getQuery(true)
                    ->select('COUNT(c.id)')
                    ->from($db->quoteName('#__rssfactory_cache', 'c'))
                    ->where($db->quoteName('c.archived') . ' = :archived')
                    ->bind(':archived', 0, \PDO::PARAM_INT);
                $db->setQuery($query);
                $result = (int) $db->loadResult();

                $output[] = FactoryTextRss::plural('form_field_rssfactoryproinfo_cache_content', $result);
                break;

            case 'cache_table_status':
                // Using SHOW TABLE STATUS to get table information
                $tableName = $db->replacePrefix('#__rssfactory_cache');
                $query = 'SHOW TABLE STATUS LIKE ' . $db->quote($tableName);
                $db->setQuery($query);
                $result = $db->loadAssoc();
                $dataFree = isset($result['Data_free']) ? (float) $result['Data_free'] : 0.0;
                $formatted = number_format(($dataFree / 1024), 2);

                $output[] = FactoryTextRss::sprintf('form_field_rssfactoryproinfo_cache_table_status', $formatted);
                break;

            case 'refresh_link':
                $password = $this->form->getValue('refresh_password');
                $link = Uri::root() . 'components/com_rssfactory/helpers/refresh.php?password=' . urlencode((string) $password);
                $output[] = '<a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '" target="_blank">' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</a>';
                break;

            case 'text':
                $output[] = FactoryTextRss::_((string) $this->element['default']);
                break;

            case 'link':
                $link = (string) $this->element['link'];
                $output[] = '<a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">' . FactoryTextRss::_((string) $this->element['default']) . '</a>';
                break;
        }

        return implode("\n", $output);
    }
}
