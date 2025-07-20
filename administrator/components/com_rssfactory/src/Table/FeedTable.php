<?php

/**
-------------------------------------------------------------------------
rssfactory - Rss Factory 4.3.6
-------------------------------------------------------------------------
 * @author thePHPfactory
 * @copyright Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Websites: http://www.thePHPfactory.com
 * Technical Support: Forum - http://www.thePHPfactory.com/forum/
-------------------------------------------------------------------------
*/

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;
use Joomla\CMS\HTTP\Transport\CurlTransport;

class FeedTable extends Table
{
    public $refreshallowedwords;
    public $refreshbannedwords;
    public $refreshexactmatchwords;
    public $i2c_enable_word_filter;
    public $i2c_words_white_list;
    public $i2c_words_black_list;
    public $i2c_words_exact_list;
    public $i2c_words_replacements;
    public $params;
    protected $filter = null;
    protected $filterI2C = null;

    /**
     * Constructor
     * 
     * @param   \JDatabaseDriver  $db  Database driver object
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__rssfactory', 'id', $db);
    }

    /**
     * Method to bind the data to the table object
     *
     * @param   array  $array   The data array
     * @param   string $ignore  The fields to ignore during bind
     *
     * @return  boolean  True on success, false on failure
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new Registry($array['params']);
            $array['params'] = $registry->toString();
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Method to check the data before saving it
     *
     * @return  boolean  True if valid, false if not
     */
    public function check()
    {
        // Call the parent check method first
        if (!parent::check()) {
            return false;
        }

        // Ensure URL is correctly formatted
        if ('' != $this->url) {
            $this->url = trim($this->url);

            if (false === strpos($this->url, 'http://') && false === strpos($this->url, 'https://')) {
                $this->url = 'http://' . $this->url;
            }
        }

        return true;
    }

    /**
     * Method to get the import-to-content rules
     *
     * @return  array  The array of import-to-content rules
     */
    public function getI2CRules()
    {
        $params = is_string($this->params) ? new Registry($this->params) : $this->params;

        return (array)$params->get('i2c_rules', []);
    }

    /**
     * Refresh the feed content by fetching it from its URL.
     *
     * @return void
     */
    public function refreshContent()
    {
        // Assuming the feed URL is stored in the 'url' field
        $url = $this->url;

        // Validate URL format
        if (empty($url)) {
            throw new \Exception('Invalid feed URL.');
        }

        // Fetch the content using Joomla 4 HTTP client
        $http = \Joomla\CMS\Http\HttpFactory::getHttp();
        $response = $http->get($url);

        if ($response->code === 200) {
            // Content fetched successfully, update the feed's content field
            $this->content = $response->body; // Assuming 'content' is the field where feed content is stored
            $this->last_refresh = Factory::getDate()->toSql();  // Set last refresh time
            $this->rsserror = 0;  // Reset error flag

        } else {
            // Log error if unable to fetch the content
            $this->rsserror = 1;
            $this->content = '';  // Clear existing content if refresh fails
        }
    }
}
