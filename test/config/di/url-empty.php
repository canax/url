<?php
/**
 * Configuration file for DI container.
 */
return [
    "services" => [
        "url-empty" => [
            "shared" => true,
            "callback" => function () {
                $url = new \Anax\Url\Url();
                $request = $this->get("request");
                $url->setSiteUrl($request->getSiteUrl());
                $url->setBaseUrl($request->getBaseUrl());
                $url->setStaticSiteUrl($request->getSiteUrl());
                $url->setStaticBaseUrl($request->getBaseUrl());
                $url->setScriptName($request->getScriptName());
                $url->configure(__DIR__ . "/../url-empty.php");
                $url->setDefaultsFromConfiguration();
                return $url;
            }
        ],
    ],
];
