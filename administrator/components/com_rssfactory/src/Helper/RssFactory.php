<?php
namespace Joomla\Component\Rssfactory\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class RssFactoryHelper
{
    protected static $extension = 'com_rssfactory';

    /**
     * Add submenu entries in the Joomla Administrator sidebar.
     *
     * @param string $vName The active view name
     */
    public static function addSubmenu(string $vName): void
    {
        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_FEEDS'),
            'index.php?option=' . self::$extension . '&view=feeds',
            $vName === 'feeds'
        );
        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_COMMENTS'),
            'index.php?option=' . self::$extension . '&view=comments',
            $vName === 'comments'
        );
        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_ADS'),
            'index.php?option=' . self::$extension . '&view=ads',
            $vName === 'ads'
        );
        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_SUBMITTED_FEEDS'),
            'index.php?option=' . self::$extension . '&view=submittedfeeds',
            $vName === 'submittedfeeds'
        );
        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&extension=' . self::$extension,
            $vName === 'categories'
        );

        if (self::isUserAuthorised('backend.settings')) {
            HTMLHelper::_('sidebar.addEntry',
                Text::_('COM_RSSFACTORY_SUBMENU_CONFIGURATION'),
                'index.php?option=' . self::$extension . '&view=configuration',
                $vName === 'configuration'
            );
        }

        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_BACKUP'),
            'index.php?option=' . self::$extension . '&view=backup',
            $vName === 'backup'
        );
        HTMLHelper::_('sidebar.addEntry',
            Text::_('COM_RSSFACTORY_SUBMENU_ABOUT'),
            'index.php?option=' . self::$extension . '&view=about',
            $vName === 'about'
        );
    }

    /**
     * Check if the user is authorized to perform the given action.
     *
     * @param string $action The action to check authorization for
     * @return bool True if the user is authorized, otherwise false
     */
    public static function isUserAuthorised(string $action): bool
    {
        $user = Factory::getApplication()->getIdentity();
        $notPublic = ['frontend.favorites'];
        
        // Check if the user is guest and the action is not public
        if ($user->guest && in_array($action, $notPublic)) {
            return false;
        }
        
        // Use Joomla's authorization method to check for the given action
        return $user->authorise($action, self::$extension);
    }

    /**
     * Retrieve and store the site icon based on the provided URL.
     *
     * @param int    $id  The feed or site ID
     * @param string $url The URL to retrieve the icon from
     * @return bool True on success, false on failure
     */
    public static function getSiteIcon(int $id, string $url): bool
    {
        if (!$url) {
            return false;
        }

        $ico_path = JPATH_SITE . '/media/com_rssfactory/icos';
        $ico_name = 'ico_' . md5($id);

        $host = parse_url($url);
        $picurl = 'http://' . $host['host'] . '/favicon.ico';

        try {
            $loader = new \Elphin\IcoFileLoader\IcoFileService;
            $im = $loader->extractIcon($picurl, 32, 32);
            imagepng($im, $ico_path . DIRECTORY_SEPARATOR . $ico_name . '.png');
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * Read the content of a remote URL.
     *
     * @param string $uri The URI to fetch
     * @param bool   $use_http_headers Flag to use HTTP headers
     * @param bool   $returnInfo Flag to return additional information
     * @return mixed The content of the URL, or false if failure
     */
    public static function remoteReadUrl(string $uri, bool $use_http_headers = true, bool $returnInfo = false)
    {
        $ret = false;

        if (function_exists('curl_init')) {
            $handle = curl_init();

            curl_setopt($handle, CURLOPT_URL, $uri);
            curl_setopt($handle, CURLOPT_MAXREDIRS, 20);
            curl_setopt($handle, CURLOPT_AUTOREFERER, true);
            curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)');
            curl_setopt($handle, CURLOPT_ENCODING, '');
            curl_setopt($handle, CURLOPT_HTTPHEADER, [
                'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
                'Accept-Language: en,de-de;q=0.8,de;q=0.5,en-us;q=0.3',
                'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
                'Keep-Alive: 300',
                'Connection: keep-alive',
                'Pragma: no-cache',
                'Cache-Control: no-cache'
            ]);
            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($handle, CURLOPT_TIMEOUT, 20);

            $cookie = JPATH_COMPONENT_ADMINISTRATOR . '/helpers/cookie.txt';
            file_put_contents($cookie, '');
            curl_setopt($handle, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($handle, CURLOPT_COOKIEFILE, $cookie);

            $buffer = curl_exec($handle);

            $ret = $buffer;
            if ($returnInfo) {
                $ret = ['info' => curl_getinfo($handle), 'buffer' => $buffer];
            }
            curl_close($handle);
        } elseif (ini_get('allow_url_fopen')) {
            $fp = @fopen($uri, 'r');
            if (!$fp) {
                return false;
            }
            stream_set_timeout($fp, 20);
            $linea = '';
            while ($remote_read = fread($fp, 4096)) {
                $linea .= $remote_read;
            }

            $info = stream_get_meta_data($fp);
            fclose($fp);

            if ($info['timed_out']) {
                return false;
            }

            $ret = $linea;
            if ($returnInfo) {
                $redirectUrls = [];
                foreach ($info['wrapper_data'] as $inf) {
                    if (preg_match('#^Location: (.*?)$#i', $inf, $m)) {
                        $redirectUrls[] = $m[1];
                    }
                }
                $redirectUrls = array_reverse($redirectUrls);

                if (isset($redirectUrls[0]) && strpos($redirectUrls[0], 'http') !== 0) {
                    $relativeUrl = $redirectUrls[0];

                    while (strpos(reset($redirectUrls), 'http') !== 0) {
                        array_shift($redirectUrls);
                    }

                    $uriObj = Uri::getInstance(reset($redirectUrls));
                    $siteUri = $uriObj->toString(['scheme', 'host']);

                    $url = $siteUri . $relativeUrl;
                } else {
                    $url = $redirectUrls[0] ?? '';
                }

                $ret = ['info' => ['url' => $url], 'buffer' => $linea];
            }
        }

        return $ret;
    }

    /**
     * Parse and retrieve the full article content based on given rules.
     *
     * @param string $url The article URL
     * @param array  $rules The parsing rules
     * @param bool   $debug Flag to enable debugging
     * @return string The parsed content
     */
    public static function parseFullArticle(string $url, array $rules, bool $debug = false): string
    {
        $url = trim($url);

        $page = self::readUrlAndConvertUtf8($url);
        $content = [];

        // Remove inline javascript and css.
        $page = preg_replace('#<script.*?>.*?</script>#is', '', $page);
        $page = preg_replace('#<script.*?/>#is', '', $page);
        $page = preg_replace('#<style.*?>.*?</style>#is', '', $page);

        foreach ($rules as $rule) {
            if (is_object($rule)) {
                $rule = (array)$rule;
            }

            if (empty($rule['enabled'])) {
                continue;
            }

            $params = $rule['params'] ?? '';

            $instance = \RssFactoryRule::getInstance($rule['type']);
            $content[] = $instance->getParsedOutput($params, $page, $content, $debug);
        }

        return implode("\n", $content);
    }

    /**
     * Generate the pseudo cron HTML to refresh the feed.
     *
     * @return string The iframe HTML for the pseudo cron refresh
     */
    public static function getPseudoCronHtml(): string
    {
        $password = md5(time() . mt_rand(0, 99999));
        $session = Factory::getApplication()->getSession();
        $session->set('com_rssfactory.pseudocron.key', $password);

        $url = Uri::root() . 'components/com_rssfactory/helpers/refresh.php?type=pseudocron&password=' . $password;
        $name = 'com_rssfactory_pseudo_refresh';
        $attribs = 'style="width:0; height:0" frameborder="0" width="0" height="0"';

        return HTMLHelper::_('iframe', $url, $name, $attribs);
    }

    /**
     * Read the URL and ensure the content is UTF-8 encoded.
     *
     * @param string $url The URL to fetch
     * @return string The content in UTF-8 encoding
     */
    protected static function readUrlAndConvertUtf8(string $url): string
    {
        $header = [
            'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
            'Accept-Language: en,de-de;q=0.8,de;q=0.5,en-us;q=0.3',
            'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Keep-Alive: 300',
            'Connection: keep-alive',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
        ];
        $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)';

        $cookie = JPATH_COMPONENT_ADMINISTRATOR . '/helpers/cookie.txt';
        file_put_contents($cookie, '');

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_USERAGENT      => $agent,
            CURLOPT_ENCODING       => '',
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_COOKIEJAR      => $cookie,
            CURLOPT_COOKIEFILE     => $cookie,
        ];

        $ch = curl_init();

        curl_setopt_array($ch, $options);
        $data = self::curl_exec_utf8($ch, 20);

        if ($error = curl_errno($ch)) {
            throw new \RuntimeException(curl_error($ch), $error);
        }

        curl_close($ch);

        return $data;
    }

    /**
     * Execute the curl request and handle UTF-8 encoding.
     *
     * @param resource $ch The curl handle
     * @param int $redirects The number of allowed redirects
     * @return string The response data
     */
    protected static function curl_exec_utf8($ch, $redirects = 20)
    {
        $data = self::curl_exec_follow($ch, $redirects);

        if (!is_string($data)) {
            return $data;
        }

        unset($charset);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        // 1: HTTP Content-Type: header
        preg_match('@([\w/+]+)(;\s*charset=(\S+))?@i', $content_type, $matches);
        if (isset($matches[3])) {
            $charset = $matches[3];
        }

        // 2: <meta> element in the page
        if (!isset($charset)) {
            preg_match('@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s*charset=([^\s"]+))?@i', $data, $matches);
            if (isset($matches[3])) {
                $charset = $matches[3];
            }
        }

        // 3: <xml> element in the page
        if (!isset($charset)) {
            preg_match('@<\?xml.+encoding="([^\s"]+)@si', $data, $matches);
            if (isset($matches[1])) {
                $charset = $matches[1];
            }
        }

        // 4: PHP's heuristic detection
        if (!isset($charset)) {
            $encoding = mb_detect_encoding($data);
            if ($encoding) {
                $charset = $encoding;
            }
        }

        // 5: Default for HTML
        if (!isset($charset)) {
            if (strstr($content_type, "text/html") === 0) {
                $charset = "ISO 8859-1";
            }
        }

        // Convert it if it is anything but UTF-8
        if (isset($charset) && strtoupper($charset) != "UTF-8") {
            $data = iconv($charset, 'UTF-8', $data);
        }

        return $data;
    }

    /**
     * Follow redirects in the curl request.
     *
     * @param resource $ch The curl handle
     * @param int $maxredirect The number of allowed redirects
     * @return string The response data
     */
    protected static function curl_exec_follow($ch, &$maxredirect = null)
    {
        $mr = $maxredirect === null ? 5 : intval($maxredirect);
        if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' || !ini_get('safe_mode'))) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            if ($mr > 0) {
                $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

                $rch = curl_copy_handle($ch);
                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
                do {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ($code == 301 || $code == 302) {
                            preg_match('/Location:(.*?)\n/', $header, $matches);
                            $newurl = trim(array_pop($matches));
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$mr);
                curl_close($rch);
                if (!$mr) {
                    if ($maxredirect === null) {
                        trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                    } else {
                        $maxredirect = 0;
                    }
                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $newurl);
            }
            return curl_exec($ch);
        }
        return curl_exec($ch);
    }
}
