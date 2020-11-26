<?php

namespace Anax\Url;

use PHPUnit\Framework\TestCase;

/**
 * Additionel url tests since UrlTest grew to large.
 */
class UrlComplementingTest extends TestCase
{
    /**
     * Provider to create urls from absolute urls.
     */
    public function providerAbsoluteUrls()
    {
        return [
            [
                "http://dbwebb.se",
            ],
            [
                "https://dbwebb.se/",
            ],
            [
                "//dbwebb.se",
            ],
            [
                "mailto:someone@somewhere.se",
            ],
        ];
    }



    /**
     * Create url from absolute url.
     *
     * @dataProvider providerAbsoluteUrls
     */
    public function testCreateAbsoluteUrl($input)
    {
        $url = new Url();

        $res = $url->create($input);
        $this->assertEquals($input, $res);
    }



    /**
     * Remove trailing index from url.
     */
    public function testRemoveTrailingIndexFromUrl()
    {
        $url = new Url();
        $url->setSiteUrl("http://site.se/");
        $url->setBaseUrl("http://site.se/base");
        $url->setUrlType(Url::URL_CLEAN);

        // Index should be removed
        $res = $url->create("somewhere/index");
        $exp = "http://site.se/base/somewhere";
        $this->assertEquals($exp, $res);

        // Index should NOT be removed
        $res = $url->create("/somewhere/index");
        $exp = "http://site.se/somewhere/index";
        $this->assertEquals($exp, $res);
    }



    /**
     * Provider to test slugs.
     */
    public function providerSlugify()
    {
        return [
            [
                "åäö", "aao"
            ],
            [
                "a_a-o", "a-a-o"
            ],
            [
                "abcABC", "abcabc"
            ],
            [
                " abc ", "abc"
            ],
        ];
    }



    /**
     * Create slugs.
     *
     * @dataProvider providerSlugify
     */
    public function testSlugify($str, $exp)
    {
        $url = new Url();

        $res = $url->slugify($str);
        $this->assertEquals($exp, $res);
    }



    /**
     * Create assets.
     */
    public function testAsset()
    {
        $url = new Url();

        $res = $url->asset("");
        $this->assertEquals("", $res);
    }



    /**
     * Create realative url.
     */
    public function testRelativeUrl()
    {
        $url = new Url();
        $url->setSiteUrl("http://site.se/");
        $url->setBaseUrl("http://site.se/base");

        $res = $url->createRelative();
        $this->assertEquals("http://site.se/base", $res);

        $res = $url->createRelative("/");
        $this->assertEquals("http://site.se", $res);

        $res = $url->createRelative("controller");
        $this->assertEquals("http://site.se/base/controller", $res);
    }
}
