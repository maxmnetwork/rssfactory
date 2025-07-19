<?php

/**
 * RSS Factory Pro Parser for Joomla 4/5
 * Refactored for Joomla 4/5 standards
 * 
 * @author thePHPfactory
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.thePHPfactory.com
 * Technical Support: Forum - http://www.thePHPfactory.com/forum/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Filesystem\Path;
use Joomla\CMS\Table\Table;

class JRSSFactoryProParser
{
    public $parserName = null;
    public $parser = null;
    public $xmlEncoding = '';
    protected $featuredArticles = false;
    private $error = '';

    public function __construct($parserName)
    {
        $parserName = strtolower($parserName);
        $this->parserName = $parserName;

        $parserDir = RSS_FACTORY_COMPONENT_PATH . '/parsers/' . $this->parserName;

        if (!file_exists($parserDir)) {
            throw new \RuntimeException(Text::sprintf('Parser %s not found! No feeds refreshed!', strtoupper($this->parserName)), 500);
        }

        switch ($parserName) {
            case 'simplepie':
                require_once $parserDir . '/simplepie.inc';
                require_once $parserDir . '/idn/idna_convert.class.php';
                $this->parser = new SimplePie();
                $this->parser->enable_cache(false);
                break;
            case 'magpie':
                require_once $parserDir . '/rss_fetch.inc';
                define('MAGPIE_CACHE_ON', false);
                define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
                $this->parser = new MagpieRSSParser();
                break;
        }

        return $this->parser;
    }

    public static function getInstance($parserName = 'simplepie')
    {
        static $parser;

        if (!isset($parser) || $parser->parserName !== $parserName) {
            $parser = new self($parserName);
        }
        return $parser;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setEncoding()
    {
        switch ($this->parserName) {
            case 'simplepie':
                $this->xmlEncoding = $this->parser->get_encoding();
                break;
            case 'domit':
            case 'magpie':
                $this->xmlEncoding = 'UTF-8';
                break;
        }
    }

    public function getEncoding()
    {
        return $this->xmlEncoding;
    }

    public function parse2cache($feed)
    {
        if (!$this->parser) {
            return;
        }

        $this->archiveStoriesForFeed($feed->id);
        $this->deleteArchivedStoriesForFeed($feed->id);

        if ('http' == $feed->protocol && !$this->fetchURL($feed->url)) {
            return false;
        }

        if ('ftp' == $feed->protocol && !$this->fetchFTP($feed)) {
            return false;
        }

        $method = $this->parserName . '2cache';

        $results = $this->$method($feed);

        $this->error = '';

        return $results;
    }

    protected function fetchURL($url)
    {
        if (!$this->parser) {
            return;
        }

        switch ($this->parserName) {
            case 'simplepie':
                $this->parser->set_feed_url($url);
                $this->parser->enable_order_by_date(true);

                if (!$this->parser->init()) {
                    $this->error = $this->parser->error;
                    return false;
                }
                break;
            case 'magpie':
                if (!$this->parser->fetch_rss($url)) {
                    return false;
                }
                break;
        }

        $this->setEncoding();

        return true;
    }

    protected function fetchFTP($rssSource)
    {
        if (!$this->parser) {
            return;
        }

        $data = $this->getFtpFeed($rssSource);

        switch ($this->parserName) {
            case 'simplepie':
                $this->parser->set_raw_data($data);
                if (!$this->parser->init()) {
                    return false;
                }
                break;
            default:
                return false;
        }

        $this->setEncoding();

        return true;
    }

    protected function simplepie2cache($feed)
    {
        // Initialise variables.
        $data = [];
        $cachedItems = 0;
        $settings = Factory::getConfig();

        // Set title, description and link.
        $data['channel_title'] = $this->convert2utf8($this->parser->get_title());
        $data['channel_description'] = $this->convert2utf8($this->parser->get_description());
        $data['channel_link'] = $this->parser->get_link();

        // Get items.
        $limit = $this->parser->get_item_quantity($feed->nrfeeds);
        $items = $this->parser->get_items(0, $limit);

        Factory::getApplication()->register('Import2ContentHelper', JPATH_COMPONENT_SITE . '/parsers/import2content.php');
        PluginHelper::importPlugin('finder');

        // Parse items.
        foreach ($items as $item) {
            $title = $item->get_title();
            $description = $item->get_description();

            if (!$settings->get('i2c_convert_html_chars', 1)) {
                $title = html_entity_decode($title);
                $description = html_entity_decode($description);
            }

            $data['id']               = null;
            $data['item_title']       = $title;
            $data['item_description'] = $description;
            $data['item_date']        = Factory::getDate($item->get_date('U'))->toSql();
            $data['rssid']            = $feed->id;
            $data['rssurl']           = $feed->url;
            $data['item_link']        = $item->get_link();
            $data['item_source']      = $item->get_feed()->get_title();
            $data['item_enclosure']   = $this->getEnclosures($item);

            $cache = Table::getInstance('Cache', 'RssFactoryTable');
            $cache->setFeed($feed);

            if (!$cache->save($data)) {
                continue;
            }

            Factory::getApplication()->triggerEvent('onFinderAfterSave', ['com_rssfactory.story', $cache, true]);

            // Import 2 Content.
            if (class_exists('Import2ContentHelper')) {
                $article = Import2ContentHelper::storeArticle($feed, $cache);

                if ($article && $article->featured) {
                    $this->featuredArticles = true;
                }
            }

            $cachedItems++;
        }

        if ($this->featuredArticles && class_exists('Import2ContentHelper')) {
            Import2ContentHelper::reorderFeaturedArticles();
        }

        return $cachedItems;
    }

    protected function magpie2cache(&$rssSource)
    {
        $database = Factory::getDbo();
        $config = RFProHelper::getConfig();

        $resp = $this->parser->resp;
        $items = $resp->items;

        $nrItems = count($items);
        $limit = $rssSource->nrfeeds;
        $limit = ($limit == 0 || $limit > $nrItems) ? $nrItems : $limit;

        $row = new JRSSFactoryPRO_Cache($database);
        $now = date("Y-m-d H:i:s", time());

        $row->channel_title = isset($resp->channel['title']) ? $resp->channel['title'] : '';
        $row->channel_title = $this->convert2utf8($row->channel_title);
        $row->channel_description = isset($resp->channel['description']) ? $resp->channel['description'] : '';
        $row->channel_description = $this->convert2utf8($row->channel_description);
        $row->channel_link = isset($resp->channel['link']) ? $resp->channel['link'] : '';

        $nrCachedItems = 0;
        for ($i = 0; $i < $limit; $i++) {
            $item = $items[$i];

            $row->id = null;
            $row->item_title = isset($item['title']) ? $item['title'] : '';
            $row->item_title = $this->convert2utf8($row->item_title);
            $row->item_title = htmlentities($row->item_title, ENT_QUOTES, 'UTF-8');
            $row->item_description = isset($item['description']) ? $item['description'] : '';
            $row->item_description = $this->convert2utf8($row->item_description);
            $row->item_description = preg_replace('/<a /', '<a target="_blank" rel="nofollow" ', $row->item_description);

            if (!$row->preStoreFilter($config)) {
                continue;
            }

            $itemResume = $row->item_title . $row->item_description;
            $row->item_hash = sha1($this->prepareFeedSHA1($itemResume));

            $query = "SELECT `id` FROM `#__rssfactory_cache` WHERE `rssid`='" . $rssSource->id . "' AND `item_hash`='" . $row->item_hash . "'";
            $database->setQuery($query);
            $found_id = $database->loadResult();
            if ($found
