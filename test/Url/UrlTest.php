<?php

namespace Anax\Url;

use \PHPUnit\Framework\TestCase;

/**
 * A helper to create urls.
 */
class UrlTest extends TestCase
{
    /**
     * Provider for various siteUrls
     *
     * @return array
     */
    public function providerSiteUrl()
    {
        return [
            [
                "http://dbwebb.se",
                "http://dbwebb.se",
                "http://dbwebb.se",
            ],
            [
                "http://dbwebb.se/",
                "http://dbwebb.se/",
                "http://dbwebb.se",
            ],
            [
                "//dbwebb.se",
                "//dbwebb.se",
                "//dbwebb.se",
            ],
            [
                "https://dbwebb.se",
                "https://dbwebb.se",
                "https://dbwebb.se",
            ],
        ];
    }



    /**
     * Test
     *
     * @param string $route the route part
     *
     * @return void
     *
     * @dataProvider providerSiteUrl
     *
     */
    public function testCreateAsSiteUrl($siteUrl, $route, $result)
    {
        $url = new \Anax\Url\Url();

        // create
        $res = $url->setSiteUrl($siteUrl);
        $this->assertInstanceOf(get_class($url), $res, "setSiteUrl did not return this.");

        $res = $url->createRelative($route);
        $this->assertEquals($result, $res, "Created url did not match expected.");

        // createRelative
        $res = $url->setBaseUrl($siteUrl);
        $this->assertInstanceOf(get_class($url), $res, "setBaseUrl did not return this.");

        $res = $url->createRelative($route);
        $this->assertEquals($result, $res, "Created url did not match expected.");

        // asset
        $res = $url->setStaticSiteUrl($siteUrl);
        $this->assertInstanceOf(get_class($url), $res, "setStaticSiteUrl did not return this.");

        $res = $url->setStaticBaseUrl($siteUrl);
        $this->assertInstanceOf(get_class($url), $res, "setStaticBaseUrl did not return this.");

        $res = $url->asset($route);
        $this->assertEquals($result, $res, "Created url did not match expected.");
    }



    /**
     * Provider for routes
     *
     * @return array
     */
    public function providerRoute()
    {
        $siteUrl = "http://dbwebb.se";
        $baseUrl = $siteUrl;
        $scriptName = "index.php";
        $urlType = \Anax\Url\Url::URL_APPEND;

        return [
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "",
                "$baseUrl/$scriptName",
            ],
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "/",
                "$siteUrl/",
            ],
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "?",
                "?",
            ],
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "#",
                "#",
            ],
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "/someother/path",
                "$siteUrl/someother/path",
            ],
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "controller",
                "$baseUrl/$scriptName/controller",
            ],
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "controller/action",
                "$baseUrl/$scriptName/controller/action",
            ],
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "controller/action/arg1",
                "$baseUrl/$scriptName/controller/action/arg1",
            ],
            [
                $siteUrl,
                $baseUrl,
                $scriptName,
                $urlType,
                "controller/action/arg1/arg2",
                "$baseUrl/$scriptName/controller/action/arg1/arg2",
            ],
        ];
    }



    /**
     * Test
     *
     * @param string $route the route part
     *
     * @return void
     *
     * @dataProvider providerRoute
     *
     */
    public function testCreateUrlAppend($siteUrl, $baseUrl, $scriptName, $urlType, $route, $result)
    {
        $url = new \Anax\Url\Url();

        $res = $url->setSiteUrl($siteUrl);
        $this->assertInstanceOf(get_class($url), $res, "setSiteUrl did not return this.");

        $res = $url->setBaseUrl($baseUrl);
        $this->assertInstanceOf(get_class($url), $res, "setBaseUrl did not return this.");

        $res = $url->setScriptName($scriptName);
        $this->assertInstanceOf(get_class($url), $res, "setScriptName did not return this.");

        $res = $url->setUrlType($urlType);
        $this->assertInstanceOf(get_class($url), $res, "setUrlType did not return this.");

        $res = $url->create($route);
        $this->assertEquals($result, $res, "Created url did not match expected.");
    }


    /**
     * Provider for routes
     *
     * @return array
     */
    public function providerRoute2()
    {
        $siteUrl = "http://dbwebb.se";
        $baseUrl = $siteUrl . "/kod-exempel/anax-mvc";
        $urlType = \Anax\Url\Url::URL_CLEAN;

        return [
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "",
                "$baseUrl",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "/",
                "$siteUrl/",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "?",
                "?",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "#",
                "#",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "/someother/path",
                "$siteUrl/someother/path",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "controller",
                "$baseUrl/controller",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "controller/action",
                "$baseUrl/controller/action",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "controller/action/arg1",
                "$baseUrl/controller/action/arg1",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "controller/action/arg1/arg2",
                "$baseUrl/controller/action/arg1/arg2",
            ],
        ];
    }



    /**
     * Test
     *
     *
     * @return void
     *
     * @dataProvider providerRoute2
     *
     */
    public function testCreateUrlAppend2($siteUrl, $baseUrl, $urlType, $route, $result)
    {
        $url = new \Anax\Url\Url();

        $res = $url->setSiteUrl($siteUrl);
        $this->assertInstanceOf(get_class($url), $res, "setSiteUrl did not return this.");

        $res = $url->setBaseUrl($baseUrl);
        $this->assertInstanceOf(get_class($url), $res, "setBaseUrl did not return this.");

        $res = $url->setUrlType($urlType);
        $this->assertInstanceOf(get_class($url), $res, "setUrlType did not return this.");

        $res = $url->create($route);
        $this->assertEquals($result, $res, "Created url did not match expected.");
    }



    /**
     * Provider for routes
     *
     * @return array
     */
    public function providerRoute3()
    {
        $siteUrl = "http://dbwebb.se";
        $baseUrl = $siteUrl . "/kod-exempel/anax-mvc";
        $urlType = \Anax\Url\Url::URL_CLEAN;
        $extraUrl = "doc";

        return [
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "",
                $extraUrl,
                "$baseUrl/$extraUrl",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "/",
                $extraUrl,
                "$siteUrl/",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "?",
                $extraUrl,
                "?",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "#",
                $extraUrl,
                "#",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "/someother/path",
                $extraUrl,
                "$siteUrl/someother/path",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "controller",
                $extraUrl,
                "$baseUrl/$extraUrl/controller",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "controller/action",
                $extraUrl,
                "$baseUrl/$extraUrl/controller/action",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "controller/action/arg1",
                $extraUrl,
                "$baseUrl/$extraUrl/controller/action/arg1",
            ],
            [
                $siteUrl,
                $baseUrl,
                $urlType,
                "controller/action/arg1/arg2",
                $extraUrl,
                "$baseUrl/$extraUrl/controller/action/arg1/arg2",
            ],
        ];
    }



    /**
     * Test
     *
     *
     * @return void
     *
     * @dataProvider providerRoute3
     *
     */
    public function testCreateUrlAppend3($siteUrl, $baseUrl, $urlType, $route, $extraUrl, $result)
    {
        $url = new \Anax\Url\Url();

        $res = $url->setSiteUrl($siteUrl);
        $this->assertInstanceOf(get_class($url), $res, "setSiteUrl did not return this.");

        $res = $url->setBaseUrl($baseUrl);
        $this->assertInstanceOf(get_class($url), $res, "setBaseUrl did not return this.");

        $res = $url->setUrlType($urlType);
        $this->assertInstanceOf(get_class($url), $res, "setUrlType did not return this.");

        $res = $url->create($route, $extraUrl);
        $this->assertEquals($result, $res, "Created url did not match expected.");
    }



    /**
     * Provider for asset
     *
     * @return array
     */
    public function providerAsset()
    {
        $staticSiteUrl = "http://dbwebb.se";
        $staticBaseUrl = $staticSiteUrl . "/kod-exempel/anax-mvc";

        return [
            [
                $staticSiteUrl,
                $staticBaseUrl,
                "http://dbwebb.se/css/style.css",
                "http://dbwebb.se/css/style.css",
            ],
            [
                $staticSiteUrl,
                $staticBaseUrl,
                "//dbwebb.se/css/style.css",
                "//dbwebb.se/css/style.css",
            ],
            [
                $staticSiteUrl,
                $staticBaseUrl,
                "/css/style.css",
                "$staticSiteUrl/css/style.css",
            ],
            [
                $staticSiteUrl,
                $staticBaseUrl,
                "css/style.css",
                "$staticBaseUrl/css/style.css",
            ],
        ];
    }



    /**
     * Test
     *
     * @return void
     *
     * @dataProvider providerAsset
     *
     */
    public function testCreateAsset($staticSiteUrl, $staticBaseUrl, $asset, $result)
    {
        $url = new \Anax\Url\Url();

        $res = $url->setStaticSiteUrl($staticSiteUrl);
        $this->assertInstanceOf(get_class($url), $res, "setStaticSiteUrl did not return this.");

        $res = $url->setStaticBaseUrl($staticBaseUrl);
        $this->assertInstanceOf(get_class($url), $res, "setStaticBaseUrl did not return this.");

        $res = $url->asset($asset);
        $this->assertEquals($result, $res, "Created url did not match expected.");
    }



    /**
     * Provider for asset
     *
     * @return array
     */
    public function providerAsset2()
    {
        $baseUrl = "http://dbwebb.se/kod-exempel/anax-mvc";

        return [
            [
                $baseUrl,
                "http://dbwebb.se/css/style.css",
                "http://dbwebb.se/css/style.css",
            ],
            [
                $baseUrl,
                "//dbwebb.se/css/style.css",
                "//dbwebb.se/css/style.css",
            ],
            [
                $baseUrl,
                "/css/style.css",
                "/css/style.css",
            ],
            [
                $baseUrl,
                "css/style.css",
                "$baseUrl/css/style.css",
            ],
        ];
    }



    /**
     * Test
     *
     * @return void
     *
     * @dataProvider providerAsset2
     *
     */
    public function testCreateAsset2($baseUrl, $asset, $result)
    {
        $url = new \Anax\Url\Url();

        $res = $url->setBaseUrl($baseUrl);
        $this->assertInstanceOf(get_class($url), $res, "setBaseUrl did not return this.");

        $res = $url->asset($asset);
        $this->assertEquals($result, $res, "Created url did not match expected.");
    }



    /**
     * Test
     *
     * @expectedException Exception
     *
     * @return void
     *
     */
    public function testWrongUrlType()
    {
        $url = new \Anax\Url\Url();

        $url->setUrlType('NO_SUCH_TYPE');
    }
}
