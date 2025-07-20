<?php

namespace Joomla\Component\Rssfactory\Administrator\View\About;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Rssfactory\Administrator\Model\AboutModel;

class AboutHtml extends HtmlView
{
    /**
     * @var array
     */
    protected $information;

    /**
     * @var string
     */
    protected $current_version;

    /**
     * @var string
     */
    protected $new_version;

    /**
     * Display the About view
     *
     * @param string|null $tpl Template file to include
     *
     * @return void
     */
    public function display($tpl = null): void
    {
        // Get the model and fetch information
        /** @var AboutModel $model */
        $model = $this->getModel();

        // Fetch information from the model
        $info = $model->getInformation();
        $this->information = [
            'latestversion'  => $info->latestVersion,
            'versionhistory' => $info->versionHistory,
            'downloadlink'   => $info->downloadLink,
            'otherproducts'  => $info->otherProducts,
            'aboutfactory'   => $info->aboutFactory,
        ];
        $this->current_version = $info->currentVersion;
        $this->new_version     = $info->newVersion;

        // Set the document title
        $document = Factory::getDocument();
        $document->setTitle(Text::_('COM_RSSFACTORY_ABOUT_TITLE'));

        // Render the view layout
        parent::display($tpl);
    }
}
