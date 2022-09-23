<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Enable, adjust and copy this code for each store you run
 *
 * Store #0, default one
 *
 * if (isHttpHost("example.com")) {
 *    $_SERVER["MAGE_RUN_CODE"] = "default";
 *    $_SERVER["MAGE_RUN_TYPE"] = "store";
 * }
 *
 * @param string $host
 * @return bool
 */
function isHttpHost(string $host)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return $_SERVER['HTTP_HOST'] === $host;
}

if (isHttpHost("mcprod.westmusic.com") ||
    isHttpHost("mcstaging.westmusic.com") ||
    isHttpHost("westmusic.com") ||
    isHttpHost("www.westmusic.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "west_music";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
} elseif (isHttpHost("mcprod.percussionsource.com") ||
    isHttpHost("mcstaging.percussionsource.com") ||
    isHttpHost("percussionsource.com") ||
    isHttpHost("www.percussionsource.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "percussion_source";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}
