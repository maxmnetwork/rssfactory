<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @author      thePHPfactory
 * @copyright   Copyright (C) 2011 SKEPSIS Consult SRL. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Rssfactory\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * Model for the About view
 */
class AboutModel extends BaseDatabaseModel
{
        /**
     * Returns information for the About view.
     *
     * @return object
     */
    public function getInformation()
    {
        // Example data structure, replace with actual logic as needed
        return (object) [
            'latestVersion'  => '5.0.0',
            'versionHistory' => [],
            'downloadLink'   => '',
            'otherProducts'  => [],
            'aboutFactory'   => 'RSS Factory is a Joomla extension.',
            'currentVersion' => '5.0.0',
            'newVersion'     => '5.0.1'
        ];
    }
    
    /**
     * Get the version of the component and other related data
     *
     * @return Registry The version and other details.
     */
    public function getAboutData()
    {
        $data = new Registry;

        // Get version
        $data->set('version', $this->getVersion());

        // Get other data like links, support etc.
        $data->set('support_url', 'http://www.thephpfactory.com/forum/');
        $data->set('documentation_url', 'http://www.thephpfactory.com/doku.php?id=joomla');

        return $data;
    }

    /**
     * Get the version of the component
     *
     * @return string The version string.
     */
    private function getVersion()
    {
        // Component version based on the manifest file
        $version = 'Unknown';
        $manifestPath = JPATH_ADMINISTRATOR . '/components/com_rssfactory/rssfactory.xml';

        if (File::exists($manifestPath)) {
            $xml = simplexml_load_file($manifestPath);
            if ($xml && isset($xml->version)) {
                $version = (string) $xml->version;
            }
        }

        return $version;
    }
}
