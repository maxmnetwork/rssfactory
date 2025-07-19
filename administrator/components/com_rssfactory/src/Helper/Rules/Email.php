<?php
/**
 * @copilot migrate this file from Joomla 3 to Joomla 4 syntax
 * Retain full business logic, refactor deprecated APIs, apply DI pattern
 */
namespace Joomla\Component\Rssfactory\Administrator\Helper\Rules;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Table\Table;
use Joomla\Component\Rssfactory\Administrator\Helper\Factory\FactoryTextRss;

defined('_JEXEC') or die;

class EmailRule
{
    protected $label = 'Email';

    public function parse($params, $page, &$content, $debug)
    {
        $groups = $params->get('groups', array());
        $emails = $params->get('emails');
        $text = implode("\n", $content);

        $results = array();
        if ($groups) {
            $dbo = Factory::getDbo();
            $query = $dbo->getQuery(true)
                ->select('u.email')
                ->from($dbo->quoteName('#__users', 'u'))
                ->leftJoin($dbo->quoteName('#__user_usergroup_map', 'm') . ' ON m.user_id = u.id')
                ->where('m.group_id IN (' . implode(',', array_map('intval', $groups)) . ')');
            $results = $dbo->setQuery($query)->loadAssocList('email');
        }

        if ('' != trim($emails)) {
            $emails = explode("\n", $emails);
            foreach ($emails as &$email) {
                $email = trim($email);
            }
        } else {
            $emails = array();
        }

        $emails = array_unique(array_merge(array_keys($results), $emails));

        if (!$emails) {
            return true;
        }

        if (!$debug) {
            $config = Factory::getConfig();
            $mailer = Factory::getMailer();

            foreach ($emails as $email) {
                $mailer->addRecipient($email);
            }

            $mailer->setSender($config->get('mailfrom'));
            $mailer->setBody($text);
            $mailer->setSubject(FactoryTextRss::_('rule_email_subject'));
            $mailer->isHtml(true);

            $mailer->send();
        } else {
            return FactoryTextRss::sprintf('rule_email_debug_info', '<ul><li>' . implode('</li><li>', $emails) . '</li></ul>');
        }
    }
}
