<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Component\ComponentHelper;

class FactoryHelper
{
    /**
     * @var CMSApplication
     */
    protected $app;

    /**
     * @var DatabaseDriver
     */
    protected $db;

    /**
     * Constructor with Dependency Injection (DI).
     *
     * @param CMSApplication|null $app
     * @param DatabaseDriver|null $db
     */
    public function __construct(CMSApplication $app = null, DatabaseDriver $db = null)
    {
        // If DI is not provided, fallback to default methods.
        $this->app = $app ?: Factory::getApplication();
        $this->db  = $db ?: Factory::getDbo();
    }

    /**
     * Get a component parameter by name.
     *
     * @param string $param
     * @param mixed $default Default value to return if the parameter is not found
     * @return mixed
     */
    public function getParam(string $param, $default = null)
    {
        // Retrieve and return the parameter from the component's configuration.
        $params = ComponentHelper::getParams('com_rssfactory');
        return $params->get($param, $default);
    }

    /**
     * Log a message to Joomla's logging system.
     *
     * @param string $message
     * @param string $level Logging level ('info', 'error', etc.)
     */
    public function log($message, $level = 'info')
    {
        // Default log level is 'info'. You can specify other levels such as 'warning', 'error'.
        Log::add($message, Log::INFO, 'com_rssfactory');
    }

    /**
     * Get a translated string.
     *
     * @param string $key The language key to translate
     * @return string The translated string
     */
    public function t($key)
    {
        // Retrieve the translated string for the provided language key.
        return Text::_($key);
    }

    /**
     * Check if the user is authorized for a specific action.
     *
     * @param string $action The action to check
     * @return bool True if the user is authorized, false otherwise
     */
    public function isUserAuthorized($action)
    {
        // Check if the user is authorized to perform the given action.
        $user = $this->app->getIdentity();
        return $user->authorise($action, 'com_rssfactory');
    }

    /**
     * Get a formatted date from a timestamp.
     *
     * @param int $timestamp The timestamp to format
     * @return string The formatted date string
     */
    public function formatDate($timestamp)
    {
        // Use Joomla's Date class to format the timestamp.
        $date = new \Joomla\CMS\Date\Date($timestamp);
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get the name of a category based on its ID.
     *
     * @param int $categoryId The category ID
     * @return string The category name
     */
    public function getCategoryName($categoryId)
    {
        // Retrieve the category name from the database based on the ID.
        $query = $this->db->getQuery(true)
            ->select('title')
            ->from('#__categories')
            ->where('id = :categoryId')
            ->bind(':categoryId', $categoryId, \PDO::PARAM_INT);

        $this->db->setQuery($query);
        $result = $this->db->loadResult();
        return $result ? $result : 'Unknown Category';
    }

    /**
     * Retrieve the total number of published feeds.
     *
     * @return int The number of published feeds
     */
    public function getPublishedFeedsCount()
    {
        // Query to get the count of published feeds.
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__rssfactory')
            ->where('published = 1');

        $this->db->setQuery($query);
        return (int) $this->db->loadResult();
    }

    /**
     * Refresh the feed data based on a provided feed ID.
     *
     * @param int $feedId The feed ID
     * @return bool True if the refresh was successful, false otherwise
     */
    public function refreshFeed($feedId)
    {
        // Refresh the feed data for the specified feed ID.
        try {
            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__rssfactory')
                ->where('id = :feedId')
                ->bind(':feedId', $feedId, \PDO::PARAM_INT);

            $this->db->setQuery($query);
            $feed = $this->db->loadObject();

            if (!$feed) {
                $this->app->enqueueMessage(Text::_('COM_RSSFACTORY_FEED_NOT_FOUND'), 'error');
                return false;
            }

            // Example refresh logic (this could involve re-fetching the feed, updating cache, etc.)
            $this->log('Feed ' . $feedId . ' refreshed.', 'info');
            return true;
        } catch (\Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->log($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Generate a feed's icon.
     *
     * @param int $feedId The feed ID
     * @param string|false $url The URL for the icon (if false, defaults to internal icon)
     * @param array $attributes HTML attributes for the <img> tag
     * @return string The URL or HTML <img> tag for the feed icon
     */
    public function getFeedIcon($feedId, $url = false, array $attributes = [])
    {
        $filename = 'default.png';
        $path = JPATH_SITE . '/media/com_rssfactory/icos/ico_' . md5($feedId) . '.png';

        if (File::exists($path)) {
            $filename = 'ico_' . md5($feedId) . '.png';
        }

        $src = Uri::root() . 'media/com_rssfactory/icos/' . $filename;

        if ($url) {
            return $src;
        }

        return HTMLHelper::_('image', $src, 'ico' . $feedId, $attributes);
    }
}
