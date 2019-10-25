<?php
defined('_JEXEC') or die;


class PlgSystemWb3_Sef extends JPlugin
{


    public function onAfterInitialise()
    {
        self::$skipVars = explode(',', $this->params->get('skipKeys'));
        self::redirectToSef();
        self::sefUrl();
    }

    public function onAfterRender()
    {

        $app = JFactory::getApplication();
        if (!$app->isClient('site')) {
            return;
        }

        $db = JFactory::getDBO();
        $body = $app->getBody();

        preg_match_all('#href="?([^"]+)"#m', $body, $matches);
        foreach ($matches[1] as $match) {
            $sefurl = $db->SetQuery("select sefurl_sefurl from wb3_sefurl where origurl_sefurl ='" . $match . "'")->loadResult();
            if ($sefurl) {
                $body = str_replace($match, $sefurl, $body);
            }
        }
        $app->setBody($body);
    }


    public static $skipVars = array();

    private static function getCurrentUrl()
    {
        return urldecode($_SERVER['REQUEST_URI']);
    }

    private static function unsetKeys(array $array, array $keys)
    {
        foreach ($keys as $key) {
            unset($array[$key]);
        }
        return $array;
    }

    public static function sefUrl()
    {

        $urlArray = self::urlToArray(null);
        $origUrlData = self::sefToOrig($urlArray->url, 0);
        if ($origUrlData->origurl_sefurl) {

            if ($origUrlData->redirect_sefurl && $origUrlData->redirect_code_sefurl) {
                header(self::redirectCode($origUrlData->redirect_code_sefurl));
                header("Location: " . self::getDomain() . "/" . $origUrlData->redirect_sefurl);
                exit();
            }

            JComponentHelper::getParams('com_content')->set('sef_advanced', 0);
            $origUrlArray = self::urlToArray(htmlspecialchars_decode($origUrlData->origurl_sefurl));
            foreach ($origUrlArray->array as $key => $value) {
                self::setGetValue($key, $value);
            }
        }

    }


    public static function redirectToSef()
    {
        $urlArray = self::urlToArray(null);
        $urlArrayWithoutVars = self::unsetKeys($urlArray->array, self::$skipVars);
        $urlWithoutVars = self::arrayToUrl($urlArray->url, $urlArrayWithoutVars);
        $urlSefData = self::sefToOrig($urlWithoutVars, 1);
        if ($urlSefData) {
            foreach (self::$skipVars as $key) {
                $sefurlVars[$key] = $urlArray->array[$key];
            }
            $sefurl = self::arrayToUrl($urlSefData->sefurl_sefurl, $sefurlVars);
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $sefurl);
            exit();
        }
    }

    private static function sefToOrig($url, $flip)
    {
        $url = (!$url) ? self::getCurrentUrl() : $url;
        $field = ($flip == 1) ? 'origurl_sefurl' : 'sefurl_sefurl';
        return JFactory::getDbo()->setQuery("SELECT * FROM wb3_sefurl WHERE $field LIKE '$url'")->loadObject();
    }


    private static function setGetValue($name, $value)
    {
        $get = JFactory::getApplication()->input->get;
        $get->set($name, $value);
        $GLOBALS['_JREQUEST'][$name] = array();
        $_GET[$name] = $value;
        $GLOBALS['_JREQUEST'][$name]['SET.GET'] = true;
        $_REQUEST[$name] = $value;
        $GLOBALS['_JREQUEST'][$name]['SET.REQUEST'] = true;
        $mainframe = JFactory::getApplication();
        $router = $mainframe->getRouter();
        $router->setVar($name, $value);
    }

    public static function urlToArray($url)
    {
        $return = new stdClass();
        $url = (!$url) ? self::getCurrentUrl() : $url;
        $urlParts = explode('?', $url);
        $return->url = $urlParts[0];
        $varsUrl = $urlParts[1];
        $varsUrlArray = explode('&', $varsUrl);
        foreach ($varsUrlArray as $varItem) {
            $varData = explode('=', $varItem);
            if (sizeof($varData) > 0) {
                $return->array[$varData[0]] = $varData[1];
            }
        }
        return $return;
    }

    private static function arrayToUrl($url, $vars)
    {
        $item = array();
        $itemStr = null;
        $url = (!$url) ? self::getCurrentUrl() : $url;
        $vars = array_diff($vars, array(''));
        foreach ($vars as $key => $var) {
            $item[] = $key . "=" . $var;
        }
        if (sizeof($item) > 0) {
            $itemStr = "?" . implode('&', $item);
        } else {
            unset($item);
        }
        return $url . "" . $itemStr;
    }


    public static function redirectCode($code)
    {
        $redirectCode[101] = "HTTP/1.1 100 Continue";
        $redirectCode[102] = "HTTP/1.1 101 SwitchingProtocols";
        $redirectCode[200] = "HTTP/1.1 200 OK";
        $redirectCode[201] = "HTTP/1.1 201 Created";
        $redirectCode[202] = "HTTP/1.1 202 Accepted";
        $redirectCode[203] = "HTTP/1.1 203 Non-AuthoritativeInformation";
        $redirectCode[204] = "HTTP/1.1 204 NoContent";
        $redirectCode[205] = "HTTP/1.1 205 ResetContent";
        $redirectCode[300] = "HTTP/1.1 300 MultipleChoices";
        $redirectCode[301] = "HTTP/1.1 301 MovedPermanently";
        $redirectCode[302] = "HTTP/1.1 302 Found";
        $redirectCode[303] = "HTTP/1.1 303 SeeOther";
        $redirectCode[305] = "HTTP/1.1 305 UseProxy";
        $redirectCode[306] = "HTTP/1.1 306 (Unused)";
        $redirectCode[307] = "HTTP/1.1 307 TemporaryRedirect";
        $redirectCode[400] = "HTTP/1.1 400 BadRequest";
        $redirectCode[402] = "HTTP/1.1 402 PaymentRequired";
        $redirectCode[403] = "HTTP/1.1 403 Forbidden";
        $redirectCode[404] = "HTTP/1.1 404 NotFound";
        $redirectCode[405] = "HTTP/1.1 405 MethodNotAllowed";
        $redirectCode[406] = "HTTP/1.1 406 NotAcceptable";
        $redirectCode[408] = "HTTP/1.1 408 RequestTimeout";
        $redirectCode[409] = "HTTP/1.1 409 Conflict";
        $redirectCode[410] = "HTTP/1.1 410 Gone";
        $redirectCode[411] = "HTTP/1.1 411 LengthRequired";
        $redirectCode[413] = "HTTP/1.1 413 PayloadTooLarge";
        $redirectCode[414] = "HTTP/1.1 414 URITooLong";
        $redirectCode[415] = "HTTP/1.1 415 UnsupportedMediaType";
        $redirectCode[417] = "HTTP/1.1 417 ExpectationFailed";
        $redirectCode[426] = "HTTP/1.1 426 UpgradeRequired";
        $redirectCode[500] = "HTTP/1.1 500 InternalServerError";
        $redirectCode[501] = "HTTP/1.1 501 NotImplemented";
        $redirectCode[502] = "HTTP/1.1 502 BadGateway";
        $redirectCode[503] = "HTTP/1.1 503 ServiceUnavailable";
        $redirectCode[504] = "HTTP/1.1 504 GatewayTimeout";
        $redirectCode[505] = "HTTP/1.1 505 VersionNotSupported";
        return $redirectCode[$code];
    }


}

