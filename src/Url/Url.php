<?php

namespace Anax\Url;

/**
 * A helper to create urls.
 *
 */
class Url implements \Anax\Common\ConfigureInterface
{
    use \Anax\Common\ConfigureTrait;



    /**
     * @const URL_CLEAN  controller/action/param1/param2
     * @const URL_APPEND index.php/controller/action/param1/param2
     * @var   $urlType   What type of urls to generate, select from
     *                   URL_CLEAN or URL_APPEND.
     */
    const URL_CLEAN  = 'clean';
    const URL_APPEND = 'append';
    private $urlType = self::URL_APPEND;



    /**
     * @var $siteUrl    Siteurl to prepend to all absolute urls created.
     * @var $baseUrl    Baseurl to prepend to all relative urls created.
     * @var $scriptName Name of the frontcontroller script.
     */
    private $siteUrl = null;
    private $baseUrl = null;
    private $scriptName = null;



    /**
     * @var $staticSiteUrl    Siteurl to prepend to all absolute urls for
     *                        assets.
     * @var $staticBaseUrl    Baseurl to prepend to all relative urls for
     *                        assets.
     */
    private $staticSiteUrl = null;
    private $staticBaseUrl = null;



    /**
     * Set default values from configuration.
     *
     * @return this.
     */
    public function setDefaultsFromConfiguration()
    {
        $set = [
            "urlType",
            "siteUrl",
            "baseUrl",
            "staticSiteUrl",
            "staticBaseUrl",
            "scriptName",
        ];
        
        foreach ($set as $item) {
            if (!isset($this->config[$item])) {
                continue;
            }
            
            $this->$item = $this->config[$item];
        }

        return $this;
    }



    /**
     * Create an url and prepending the baseUrl.
     *
     * @param string $uri     part of uri to use when creating an url.
     *                        "" or null means baseurl to current
     *                        frontcontroller.
     * @param string $baseuri optional base to prepend uri.
     *
     * @return string as resulting url.
     */
    public function create($uri = null, $baseuri = null)
    {
        if (empty($uri) && empty($baseuri)) {
            // Empty uri means baseurl
            return $this->baseUrl
                . (($this->urlType == self::URL_APPEND)
                    ? "/$this->scriptName"
                    : null);
        } elseif (empty($uri)) {
            // Empty uri means baseurl with appended $baseuri
            ;
        } elseif (substr($uri, 0, 7) == "http://"
            || substr($uri, 0, 8) == "https://"
            || substr($uri, 0, 2) == "//"
        ) {
            // Fully qualified, just leave as is.
            return rtrim($uri, "/");
        } elseif ($uri[0] == "/") {
            // Absolute url, prepend with siteUrl
            return rtrim($this->siteUrl . rtrim($uri, '/'), '/');
        } elseif ($uri[0] == "#"
            || $uri[0] == "?"
        ) {
            // Hashtag url to local page, or query part, leave as is.
            return $uri;
        } elseif (substr($uri, 0, 7) == "mailto:"
            || substr(html_entity_decode($uri), 0, 7) == "mailto:") {
            // Leave mailto links as is
            // The odd fix is for markdown converting mailto: to UTF-8
            // Might be a better way to solve this...
            return $uri;
        }

        // Prepend uri with baseuri
        $uri = rtrim($uri, "/");
        if (!empty($baseuri)) {
            $uri = rtrim($baseuri, "/") . "/$uri";
        }

        // Remove the trailing index part of the url
        if (basename($uri) == "index") {
            $uri = dirname($uri);
        }

        if ($this->urlType == self::URL_CLEAN) {
            return rtrim($this->baseUrl . "/" . $uri, "/");
        } else {
            return rtrim($this->baseUrl . "/" . $this->scriptName . "/" . $uri, "/");
        }
    }



    /**
     * Create an url and prepend the baseUrl to the directory of
     * the frontcontroller.
     *
     * @param string $uri part of uri to use when creating an url.
     *                    "" or null means baseurl to directory of
     *                    the current frontcontroller.
     *
     * @return string as resulting url.
     */
    public function createRelative($uri = null)
    {
        if (empty($uri)) {
            // Empty uri means baseurl
            return $this->baseUrl;
        } elseif (substr($uri, 0, 7) == "http://"
            || substr($uri, 0, 8) == "https://"
            || substr($uri, 0, 2) == "//"
        ) {
            // Fully qualified, just leave as is.
            return rtrim($uri, '/');
        } elseif ($uri[0] == '/') {
            // Absolute url, prepend with siteUrl
            return rtrim($this->siteUrl . rtrim($uri, '/'), '/');
        }

        $uri = rtrim($uri, '/');
        return $this->baseUrl . '/' . $uri;
    }



    /**
     * Create an url for a static asset.
     *
     * @param string $uri part of uri to use when creating an url.
     *
     * @return string as resulting url.
     */
    public function asset($uri = null)
    {
        if (empty($uri)) {
            // Allow empty
        } elseif (substr($uri, 0, 7) == "http://"
            || substr($uri, 0, 8) == "https://"
            || substr($uri, 0, 2) == "//"
        ) {
            // Fully qualified, just leave as is.
            return rtrim($uri, '/');
        } elseif ($uri[0] == '/') {
            // Absolute url, prepend with staticSiteUrl
            return rtrim($this->staticSiteUrl . rtrim($uri, '/'), '/');
        }

        $baseUrl = isset($this->staticBaseUrl)
            ? $this->staticBaseUrl
            : $this->baseUrl;

        return empty($uri)
            ? $baseUrl
            : $baseUrl . '/' . $uri;
    }



    /**
     * Set the siteUrl to prepend all absolute urls created.
     *
     * @param string $url part of url to use when creating an url.
     *
     * @return self
     */
    public function setSiteUrl($url)
    {
        $this->siteUrl = rtrim($url, '/');
        return $this;
    }



    /**
     * Set the baseUrl to prepend all relative urls created.
     *
     * @param string $url part of url to use when creating an url.
     *
     * @return self
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = rtrim($url, '/');
        return $this;
    }



    /**
     * Set the siteUrl to prepend absolute urls for assets.
     *
     * @param string $url part of url to use when creating an url.
     *
     * @return self
     */
    public function setStaticSiteUrl($url)
    {
        $this->staticSiteUrl = rtrim($url, '/');
        return $this;
    }



    /**
     * Set the baseUrl to prepend relative urls for assets.
     *
     * @param string $url part of url to use when creating an url.
     *
     * @return self
     */
    public function setStaticBaseUrl($url)
    {
        $this->staticBaseUrl = rtrim($url, '/');
        return $this;
    }



    /**
     * Set the scriptname to use when creating URL_APPEND urls.
     *
     * @param string $name as the scriptname, for example index.php.
     *
     * @return self
     */
    public function setScriptName($name)
    {
        $this->scriptName = $name;
        return $this;
    }



    /**
     * Set the type of urls to be generated, URL_CLEAN, URL_APPEND.
     *
     * @param string $type what type of urls to create.
     *
     * @return self
     *
     * @throws Anax\Url\Exception
     */
    public function setUrlType($type)
    {
        if (!in_array($type, [self::URL_APPEND, self::URL_CLEAN])) {
            throw new Exception("Unsupported Url type.");
        }

        $this->urlType = $type;
        return $this;
    }



    /**
     * Create a slug of a string, to be used as url.
     *
     * @param string $str the string to format as slug.
     *
     * @return str the formatted slug.
     */
    public function slugify($str)
    {
        $str = mb_strtolower(trim($str));
        $str = str_replace(array('å','ä','ö'), array('a','a','o'), $str);
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = trim(preg_replace('/-+/', '-', $str), '-');
        return $str;
    }
}
