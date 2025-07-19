<?php

namespace Joomla\Component\Rssfactory\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

/**
 * RssFactoryFeedRulesPreviewField Class
 *
 * A custom form field for displaying RSS feed preview rules.
 */
class RssFactoryFeedRulesPreviewField extends FormField
{
    /**
     * @var string The field type
     */
    protected $type = 'RssFactoryFeedRulesPreview';

    /**
     * @var int The limit of stories to display
     */
    protected $limit = 10;

    /**
     * Method to get the label for the field
     *
     * @return string The field label or empty string if disabled
     */
    protected function getLabel(): string
    {
        if ('false' === (string)$this->element['hasLabel']) {
            return '';
        }

        return parent::getLabel();
    }

    /**
     * Method to get the input for the field
     *
     * @return string The HTML input for the field
     */
    protected function getInput(): string
    {
        $id = $this->form->getValue('id');
        $stories = $this->getStories($id);

        $output = [];

        $output[] = '<div id="' . htmlspecialchars((string)$this->element['name'], ENT_QUOTES, 'UTF-8') . '">';

        if ($stories) {
            $output[] = FactoryTextRss::sprintf('field_rules_preview_info', min($this->limit, count($stories)));
            $output[] = '<ul class="latest-stories">';
            foreach ($stories as $story) {
                $output[] = '<li><input type="radio" name="' . $this->name . '" value="' . urlencode($story->item_link) . '" /><a href="' . $story->item_link . '" target="_blank">' . htmlspecialchars($story->item_title, ENT_QUOTES, 'UTF-8') . '</a></li>';
            }
            $output[] = '</ul>';
            $output[] = '<button type="button" class="btn btn-primary preview-rules"><i class="icon-search icon-white"></i>&nbsp;' . FactoryTextRss::_('field_rules_preview_button') . '</button>';
        } else {
            $output[] = FactoryTextRss::_('field_rules_preview_info_no_stories_cached');
        }

        $output[] = '</div>';

        return implode("\n", $output);
    }

    /**
     * Method to get the stories for the field
     *
     * @param int $id The ID of the feed
     *
     * @return object|null The list of stories or null if no stories are found
     */
    protected function getStories(int $id): ?array
    {
        $dbo = Factory::getDbo();
        $query = $dbo->getQuery(true)
            ->select('c.id, c.item_link, c.item_title')
            ->from('#__rssfactory_cache c')
            ->where('c.rssid = ' . $dbo->quote($id))
            ->order('c.date DESC');

        $results = $dbo->setQuery($query, 0, $this->limit)
            ->loadObjectList();

        return $results ?: null;
    }
}
