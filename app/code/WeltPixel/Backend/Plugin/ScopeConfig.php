<?php

namespace WeltPixel\Backend\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ScopeConfig
{
    public function afterIsSetFlag(
        ScopeConfigInterface $subject,
        $result,
        $path,
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        if ($path == 'csp/mode/storefront/report_only') {
            if ($subject->getValue('weltpixel_backend_developer/csp/change_system_value')) {
                $result = (boolean) $subject->getValue('weltpixel_backend_developer/csp/report_only');
            }
        }
        return $result;
    }
}
