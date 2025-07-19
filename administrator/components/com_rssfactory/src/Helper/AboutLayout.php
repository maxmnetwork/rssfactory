<?php

namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

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

/** @var array $displayData */
/** @var \Joomla\Registry\Registry $data */
$data = $displayData['data'];
?>

<style>
    ul.about {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    ul.about li {
        margin-bottom: 20px;
        border: 1px solid rgba(0, 0, 0, 0.2);
        overflow: hidden;
        border-radius: 1px;
    }

    ul.about h2 {
        background-color: #f7f7f7;
        margin: 0;
        padding: 15px 20px;
        border-bottom: 1px solid #ebebeb;
    }

    ul.about div.content {
        padding: 15px 20px;
        background-color: #ffffff;
    }

    ul.about div.versions {
        width: 240px;
    }

    ul.about div.new-version {
        margin-top: 20px;
        font-weight: bold;
        background-color: #ff0000;
        color: #ffffff;
        display: inline-block;
        padding: 3px 10px;
    }

    ul.about div.latest-version {
        margin-top: 20px;
        font-weight: bold;
        background-color: #3c763d;
        color: #ffffff;
        display: inline-block;
        padding: 3px 10px;
    }

    ul.about div.fb-wrapper {
        margin-left: 50px;
    }

    ul.about div.version-history {
        margin-top: 20px;
    }
</style>

<ul class="about">
    <li>
        <h2>
            <?php echo $data->get('translation.release_notes'); ?>
        </h2>

        <div class="content">

            <div class="pull-left versions">
                <div>
                    <?php echo $data->get('translation.current_version'); ?>:
                    <b class="pull-right"><?php echo $data->get('version.current'); ?></b>
                </div>

                <div>
                    <?php echo $data->get('translation.latest_version'); ?>:
                    <b class="pull-right"><?php echo $data->get('version.latest'); ?></b>
                </div>

                <?php if ($data->get('isUpdateAvailable')): ?>
                    <div class="new-version">
                        <?php echo $data->get('translation.update_is_available'); ?>
                    </div>
                <?php else: ?>
                    <div class="latest-version">
                        <?php echo $data->get('translation.update_is_not_available'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pull-left fb-wrapper">
                <div class="fb-like" data-href="https://www.facebook.com/theFactoryJoomla" data-send="false"
                     data-layout="box_count" data-width="450" data-show-faces="false"></div>
            </div>

            <div style="clear: both;"></div>

            <div class="version-history">
                <?php echo $data->get('versionHistory'); ?>
            </div>
        </div>
    </li>

    <li>
        <h2>
            <?php echo $data->get('translation.support_and_updates'); ?>
        </h2>

        <div class="content">
            <?php echo $data->get('supportAndUpdates'); ?>
        </div>
    </li>

    <li>
        <h2>
            <?php echo $data->get('translation.other_products'); ?>
        </h2>

        <div class="content">
            <?php echo $data->get('otherProducts'); ?>
        </div>
    </li>

    <li>
        <h2>
            <?php echo $data->get('translation.about_company'); ?>
        </h2>

        <div class="content">
            <?php echo $data->get('aboutCompany'); ?>
        </div>
    </li>
</ul>
