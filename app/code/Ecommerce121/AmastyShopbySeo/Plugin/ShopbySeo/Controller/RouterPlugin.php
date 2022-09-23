<?php

declare(strict_types=1);

namespace Ecommerce121\AmastyShopbySeo\Plugin\ShopbySeo\Controller;

class RouterPlugin
{
    /**
     * @param $subject
     * @param callable $proceed
     * @param $request
     * @return mixed
     */
    public function aroundMatch($subject, callable $proceed, $request)
    {
        $pathInfo = $request->getPathInfo();

        if (strpos($pathInfo, "giftregistry/index/items/id")) {
            return false;
        }

        return $proceed($request);
    }
}
