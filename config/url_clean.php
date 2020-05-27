<?php
/**
 * Config file for url.
 */
#
if (!defined("ANAX_PRODUCTION")) {
    // For development environment
    return [
        "urlType"       => self::URL_CLEAN,
    ];
}

// For production environment
return [
    // Defaults to use when creating urls.
    //"siteUrl"       => null,
    //"baseUrl"       => null,
    //"staticSiteUrl" => null,
    //"staticBaseUrl" => null,
    //"scriptName"    => null,
    "urlType"       => \Anax\Url\Url::URL_CLEAN,
    //"urlType"       => \Anax\Url\Url::URL_APPEND,
];
