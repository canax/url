<?php

namespace Anax\Url;

use Anax\Configure\Configuration;
use Anax\DI\DIFactoryConfig;
use Anax\Request\Request;
use PHPUnit\Framework\TestCase;

/**
 * Try that a DI service can be created from the di config file.
 */
class DiServiceTest extends TestCase
{
    /**
     * Create the service from default config file.
     */
    public function testCreateDiService()
    {
        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");

        // $cfg = new Configuration();
        // $cfg->setBaseDirectories([ANAX_INSTALL_PATH . "/config"]);
        // $di->set("configuration", $cfg);

        $request = new Request();
        $request->init();
        $di->set("request", $request);

        $url = $di->get("url");
        $this->assertInstanceOf(Url::class, $url);
    }



    /**
     * The configuration is empty.
     */
    public function testEmptyConfigArray()
    {
        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/test/config/di");

        $request = new Request();
        $request->init();
        $di->set("request", $request);

        $url = $di->get("url-empty");
        $this->assertInstanceOf(Url::class, $url);
    }
}
