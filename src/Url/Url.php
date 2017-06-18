<?php

namespace Anax\Url;

use Anax\Uri\Uri;

/**
 * A helper to create urls.
 *
 */
class Url implements \Anax\Configure\ConfigureInterface
{
    use \Anax\Configure\ConfigureTrait;



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
    private $siteUrl;
    private $baseUrl;
    private $scriptName;



    /**
     * @var $staticSiteUrl    Siteurl to prepend to all absolute urls for
     *                        assets.
     * @var $staticBaseUrl    Baseurl to prepend to all relative urls for
     *                        assets.
     */
    private $staticSiteUrl;
    private $staticBaseUrl;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->siteUrl = new Uri("");
        $this->baseUrl = new Uri("");
        $this->scriptName = new Uri("");
        $this->staticSiteUrl = new Uri("");
        $this->staticBaseUrl = new Uri("");
    }



    /**
     * Set default values from configuration.
     *
     * @param mixed $configSource optional config source.
     *
     * @return self
     */
    public function setDefaultsFromConfiguration($configSource = null)
    {
        $config = is_null($configSource)
            ? $this->config
            : $this->configure($configSource)->config;

        foreach ($config as $key => $value) {
            switch ($key) {
                case "urlType":
                    $this->setUrlType($value);
                    break;
                case "siteUrl":
                case "baseUrl":
                case "staticSiteUrl":
                case "staticBaseUrl":
                case "scriptName":
                    $this->$key = new Uri($value);
                    break;
            }
        }

        return $this;
    }



    /**
     * Create an url and prepending the baseUrl.
     *
     * @param string $uri     part of uri to use when creating an url.
     *                        empty means baseurl to current
     *                        frontcontroller.
     * @param string $baseuri optional base to prepend uri.
     *
     * @return string as resulting url.
     */
    public function create($uri = "", $baseuri = "")
    {
        $uri = new Uri($uri);
        $baseuri = new Uri($baseuri);

        /**
         * Cases with quick return
         */
        if ($uri->startsWith("http://", "https://", "//")) {
            /** Fully qualified, just leave as is. */
            return $uri->uri();
        }

        if ($uri->startsWith("#", "?")) {
            /** Hashtag url to local page, or query part, leave as is. */
            return $uri->uri();
        }

        if ($uri->startsWith("mailto:") || substr(html_entity_decode($uri->uri()), 0, 7) == "mailto:") {
            /**
             * Leave mailto links as is
             *
             * The odd fix is for markdown converting mailto: to UTF-8
             * Might be a better way to solve this...
             */
            return $uri->uri();
        }

        if ($uri->startsWith("/")) {
            /** Absolute url, prepend with siteUrl. */
            return $uri->prepend($this->siteUrl)->uri();
        }

        /**
         * Other cases
         */

        /** Remove the trailing 'index' part of the url. */
        $uri->removeBasename("index");

        if ($this->urlType != self::URL_CLEAN) {
            $uri->prepend($this->scriptName);
        }

        return $uri
            ->prepend($baseuri)
            ->prepend($this->baseUrl)
            ->uri();
    }



    /**
     * Create an url and prepend the baseUrl to the directory of
     * the frontcontroller.
     *
     * @param string $uri part of uri to use when creating an url.
     *                    empty means baseurl to directory of
     *                    the current frontcontroller.
     *
     * @return string as resulting url.
     */
    public function createRelative($uri = "")
    {
        $uri = new Uri($uri);

        /**
         * Catch early returns
         */
        if ($uri->startsWith("http://", "https://", "//")) {
            /** Fully qualified, return as is */
            return $uri->uri();
        }

        if ($uri->startsWith("/")) {
            /** Absolute url, prepend with siteUrl */
            return $uri->prepend($this->siteUrl)->uri();
        }

        return $uri->prepend($this->baseUrl)->uri();
    }



    /**
     * Create an url for a static asset.
     *
     * @param string $uri part of uri to use when creating an url.
     *
     * @return string as resulting url.
     */
    public function asset($uri = "")
    {
        $uri = new Uri($uri);

        /**
         * Catch early returns
         */
        if ($uri->startsWith("http://", "https://", "//")) {
            /** Fully qualified, return as is */
            return $uri->uri();
        }

        if ($uri->startsWith("/")) {
            /** Absolute url, prepend with staticSiteUrl */
            return $uri->prepend($this->staticSiteUrl)->uri();
        }

        $baseUrl = $this->staticBaseUrl->isEmpty()
            ? $this->baseUrl
            : $this->staticBaseUrl;

        return $uri->prepend($baseUrl)->uri();
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
        $this->siteUrl = new Uri($url);
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
        $this->baseUrl = new Uri($url);
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
        $this->staticSiteUrl = new Uri($url);
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
        $this->staticBaseUrl = new Uri($url);
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
        $this->scriptName = new Uri($name);
        return $this;
    }



    /**
     * Set the type of urls to be generated, URL_CLEAN, URL_APPEND.
     *
     * @param string $type what type of urls to create.
     *
     * @return self
     *
     * @throws Exception
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
     * @return string the formatted slug.
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
