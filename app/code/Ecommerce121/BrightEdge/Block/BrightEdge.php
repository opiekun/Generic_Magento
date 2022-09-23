<?php

declare(strict_types=1);

namespace Ecommerce121\BrightEdge\Block;

use \BrightEdge\BEIXFClient;
use Magento\Framework\View\Element\Template;

class BrightEdge extends Template
{
    /**
     * @return BEIXFClient
     */
    public function getBeiXClient()
    {
        return new BEIXFClient($this->getConfiguration());
    }

    /**
     * @return array
     */
    private function getConfiguration()
    {
        return array(
            BEIXFClient::$CAPSULE_MODE_CONFIG => BEIXFClient::$REMOTE_PROD_CAPSULE_MODE,
            BEIXFClient::$ACCOUNT_ID_CONFIG => "f00000000105274",

            BEIXFClient::$API_ENDPOINT_CONFIG => "https://ixfd1-api.bc0a.com",
            //BEIXFClient::$CANONICAL_HOST_CONFIG => "www.domain.com",
            //BEIXFClient::$CANONICAL_PROTOCOL_CONFIG  => "https",

            // BE IXF: By default, all URL parameters are ignored. If you have URL parameters that add value to
            // page content.  Add them to this config value, separated by the pipe character (|).
            BEIXFClient::$WHITELIST_PARAMETER_LIST_CONFIG => "ixf",

        );
    }
}
