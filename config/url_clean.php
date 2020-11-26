<?php

use Anax\Url\Url;

/**
 * Config file for url.
 */
#
if (!defined("ANAX_PRODUCTION")) {
    // For development environment
    return [
        "urlType"       => Url::URL_CLEAN,
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
    "urlType"       => Url::URL_CLEAN,
    //"urlType"       => Url::URL_APPEND,
];
