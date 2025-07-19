<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper\Factory;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Layout\FileLayout;
use DOMDocument;
use DOMXpath;

class FactoryViewHelp
{
    protected $component;
    protected $override = 'http://wiki.thephpfactory.com/doku.php?id=joomla{major}0:{component}:{keyref}';
    protected $xpath = '//div[@class="dokuwiki"]/div[@class="page"]/div/ul/li/div/a';
    protected $cache = 24;

    public function __construct(array $config = [])
    {
        $this->component = $config['component'] ?? str_replace('com_', '', Factory::getApplication()->input->getString('option'));
        $this->override = $config['override'] ?? $this->override;
        $this->xpath = $config['xpath'] ?? $this->xpath;
        $this->cache = $config['cache'] ?? $this->cache;
    }

    public function render($ref)
    {
        $pages = $this->getAvailablePages();

        if (!$pages || !in_array($ref, $pages)) {
            $ref = $this->component;
        }

        ToolbarHelper::help($ref, false, $this->override, $this->component);
    }

    protected function readUrl($url)
    {
        $hash = md5($url);
        $path = JPATH_ADMINISTRATOR . '/cache/com_' . $this->component;

        if (!Folder::exists($path)) {
            Folder::create($path);
        }

        $cacheFile = $path . '/' . $hash;
        if (!File::exists($cacheFile) || time() - 60 * 60 * $this->cache > filemtime($cacheFile)) {
            $data = $this->getUrl($url);
            file_put_contents($cacheFile, $data);
        } else {
            $data = file_get_contents($cacheFile);
        }

        return $data;
    }

    protected function parseHtml($html)
    {
        $pages = [];
        if ($html == strip_tags($html)) {
            return $pages;
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_use_internal_errors(false);

        $xpath = new DOMXpath($doc);
        $items = $xpath->query($this->xpath);

        foreach ($items as $item) {
            /** @var \DOMElement $item */
            $href = $item->getAttribute('href');
            $explode = explode(':', $href);
            $page = end($explode);

            if (false !== strpos($page, '#')) {
                list($page, $anchor) = explode('#', $page);
            }

            $pages[] = $page;
        }

        return $pages;
    }

    protected function getAvailablePages()
    {
        // J4: Use Route::_ or direct URL as needed
        $url = str_replace(
            ['{major}', '{component}', '{keyref}'],
            [Version::MAJOR_VERSION, $this->component, $this->component],
            $this->override
        );
        $html = $this->readUrl($url);

        return $this->parseHtml($html);
    }

    protected function getUrl($url)
    {
        $data = $this->getUrlCurl($url);

        if (false !== $data) {
            return $data;
        }

        $data = $this->getUrlFileOpen($url);

        if (false !== $data) {
            return $data;
        }

        $data = $this->getUrlFSockOpen($url);

        return $data;
    }

    protected function getUrlCurl($url)
    {
        if (!function_exists('curl_init')) {
            return false;
        }

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
        ));

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }

    protected function getUrlFileOpen($url)
    {
        if (!ini_get('allow_url_fopen')) {
            return false;
        }

        return file_get_contents($url);
    }

    protected function getUrlFSockOpen($url)
    {
        $uri = Uri::getInstance($url);
        $fp = @fsockopen($uri->getHost(), 80, $errno, $errstr, 30);

        if (!$fp) {
            return false;
        }

        $data = [];
        $out = [
            'GET ' . $uri->getPath() . ($uri->getQuery() ? '?' . $uri->getQuery() : '') . ' HTTP/1.1' . "\r\n",
            'Host: ' . $uri->getHost() . "\r\n",
            'Connection: Close' . "\r\n\r\n",
        ];

        fwrite($fp, implode($out));

        while (!feof($fp)) {
            $data[] = fgets($fp, 128);
        }

        fclose($fp);

        return implode($data);
    }
}
