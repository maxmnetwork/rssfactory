<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;

class RssFactoryCache
{
    /**
     * Singleton instance of the cache handler.
     *
     * @var RssFactoryCache
     */
    protected static $instance;

    /**
     * In-memory cache.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Get the singleton instance of the RssFactoryCache class.
     *
     * @return RssFactoryCache
     */
    public static function getInstance(): RssFactoryCache
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retrieve a cached value by its key.
     *
     * @param string $key The cache key.
     * @return mixed|null The cached value, or null if not found.
     */
    public function get(string $key)
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $session = Factory::getSession(); // Using the updated session class in Joomla 4
        $value = $session->get($key, null, 'com_rssfactory'); // Returns null by default if not found
        $this->cache[$key] = $value;
        return $value;
    }

    /**
     * Store a value in the cache.
     *
     * @param mixed $value The value to store.
     * @param string $key The cache key.
     * @return bool True on success.
     */
    public function store($value, string $key): bool
    {
        $session = Factory::getSession(); // Using the updated session class in Joomla 4
        $session->set($key, $value, 'com_rssfactory');
        $this->cache[$key] = $value;
        return true;
    }
}
