<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_rssfactory
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// @copilot migrate this file from Joomla 3 to Joomla 4 syntax
// Retain full business logic, refactor deprecated APIs, apply DI pattern

namespace Joomla\Component\Rssfactory\Administrator\Helper\Factory;

defined('_JEXEC') or die;

use Joomla\CMS\Pagination\Pagination;

class FactoryPagination extends Pagination
{
    /**
     * @var string|null
     */
    protected ?string $anchor = null;

    /**
     * Set the anchor for pagination links.
     *
     * @param string $anchor
     */
    public function setAnchor(string $anchor): void
    {
        $this->anchor = $anchor;
    }

    /**
     * Get the anchor for pagination links.
     *
     * @return string|null
     */
    public function getAnchor(): ?string
    {
        return $this->anchor;
    }

    /**
     * Build the pagination data object with anchor link.
     *
     * @return object
     */
    protected function _buildDataObject(): object
    {
        $data = parent::_buildDataObject();

        // If an anchor is set, append it to pagination links
        if (null !== $anchor = $this->getAnchor()) {
            $pages = ['start', 'previous', 'next', 'end', 'all'];

            foreach ($pages as $page) {
                if (isset($data->$page)) {
                    $data->$page->link .= '#' . $anchor;
                }
            }

            foreach ($data->pages as &$page) {
                $page->link .= '#' . $anchor;
            }
        }

        return $data;
    }
}
