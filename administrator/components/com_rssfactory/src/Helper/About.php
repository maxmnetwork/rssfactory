<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Http\HttpFactory;

class AboutHelper
{
    /** @var \Joomla\CMS\Layout\FileLayout */
    private $layout;

    /** @var SimpleXMLElement */
    private $remoteManifest;

    private $localManifest;
    private $remoteManifestUrl;
    private $localManifestPath;
    private $extension;

    /**
     * Constructor with Dependency Injection
     *
     * @param string $extension The name of the extension
     */
    public function __construct(string $extension)
    {
        $this->extension = $extension;
        $this->layout = new FileLayout('about_layout', __DIR__);

        // Loading the manifest files
        $this->remoteManifest = @simplexml_load_string($this->fetchUrl($this->getRemoteManifestUrl()));
        $this->localManifest = Installer::parseXMLInstallFile($this->getLocalManifestPath());
    }

    /**
     * Get the URL for the remote manifest.
     *
     * @return string
     */
    public function getRemoteManifestUrl(): string
    {
        if (null === $this->remoteManifestUrl) {
            $this->remoteManifestUrl = 'http://thephpfactory.com/versions/com_' . $this->extension . 'factory.xml';
        }

        return $this->remoteManifestUrl;
    }

    /**
     * Set the remote manifest URL.
     *
     * @param string $remoteManifestUrl The URL for the remote manifest.
     */
    public function setRemoteManifestUrl(string $remoteManifestUrl)
    {
        $this->remoteManifestUrl = $remoteManifestUrl;
    }

    /**
     * Get the local path for the manifest file.
     *
     * @return string
     */
    public function getLocalManifestPath(): string
    {
        if (null === $this->localManifestPath) {
            $this->localManifestPath = JPATH_ADMINISTRATOR . '/components/com_' . $this->extension . 'factory/' . $this->extension . 'factory.xml';
        }

        return $this->localManifestPath;
    }

    /**
     * Set the local manifest path.
     *
     * @param string $localManifestPath
     */
    public function setLocalManifestPath(string $localManifestPath)
    {
        $this->localManifestPath = $localManifestPath;
    }

    /**
     * Render the About layout.
     *
     * @return string
     */
    public function render(): string
    {
        $this->layout->setData([
            'data' => $this->getData(),
        ]);

        return $this->layout->render();
    }

    /**
     * Prepare data for the About layout.
     *
     * @return Registry
     */
    private function getData(): Registry
    {
        return new Registry([
            'version' => [
                'current' => $this->currentVersion(),
                'latest'  => $this->latestVersion(),
            ],
            'isUpdateAvailable' => $this->isUpdateAvailable(),
            'versionHistory'    => $this->versionHistory(),
            'supportAndUpdates' => $this->supportAndUpdates(),
            'otherProducts'     => $this->otherProducts(),
            'aboutCompany'      => $this->aboutCompany(),
            'translation'       => [
                'release_notes'           => $this->translate('release_notes', 'Latest Release Notes'),
                'current_version'         => $this->translate('current_version', 'Your installed version'),
                'latest_version'          => $this->translate('latest_version', 'Latest version available'),
                'update_is_available'     => $this->translate('update_is_available', 'New version available'),
                'update_is_not_available' => $this->translate('update_is_not_available', 'No new version available'),
                'support_and_updates'     => $this->translate('support_and_updates', 'Support and Updates'),
                'other_products'          => $this->translate('other_products', 'Other Products'),
                'about_company'           => $this->translate('about_company', 'About thePHPFactory'),
            ],
        ]);
    }

    /**
     * Get the latest version from the remote manifest.
     *
     * @return string
     */
    private function latestVersion(): string
    {
        return (string)$this->remoteManifest->latestversion;
    }

    /**
     * Get the current version from the local manifest.
     *
     * @return string
     */
    private function currentVersion(): string
    {
        return (string)$this->localManifest['version'];
    }

    /**
     * Check if an update is available.
     *
     * @return bool
     */
    private function isUpdateAvailable(): bool
    {
        return version_compare($this->latestVersion(), $this->currentVersion(), '>');
    }

    /**
     * Get the support and updates link from the remote manifest.
     *
     * @return string
     */
    private function supportAndUpdates(): string
    {
        return (string)$this->remoteManifest->downloadlink;
    }

    /**
     * Get the other products list from the remote manifest.
     *
     * @return string
     */
    private function otherProducts(): string
    {
        return (string)$this->remoteManifest->otherproducts;
    }

    /**
     * Get the company information from the remote manifest.
     *
     * @return string
     */
    private function aboutCompany(): string
    {
        return (string)$this->remoteManifest->aboutfactory;
    }

    /**
     * Get the version history from the remote manifest.
     *
     * @return string
     */
    private function versionHistory(): string
    {
        return (string)$this->remoteManifest->versionhistory;
    }

    /**
     * Fetch content from a URL and cache it.
     *
     * @param string $url
     * @return string
     */
    private function fetchUrl(string $url): string
    {
        $cache = JPATH_CACHE . '/' . md5($url);

        // Cache if not present or if cached file is older than 1 hour.
        if (!file_exists($cache) || time() - filemtime($cache) > 60 * 60) {
            $response = HttpFactory::getHttp()->get($url);

            if ($response->code == 200) {
                file_put_contents($cache, $response->body);
            }
        }

        return file_get_contents($cache);
    }

    /**
     * Translate a string based on the current language.
     *
     * @param string $string The string key
     * @param string $default Default translation if not found
     * @return string Translated string
     */
    private function translate(string $string, string $default): string
    {
        $language = Factory::getLanguage();

        // Translate the string if a language key exists
        if ($language->hasKey($key = 'COM_' . $this->extension . 'FACTORY_ABOUT_HEADING_' . $string)) {
            return Text::_($key);
        }

        return $default;
    }
}
