<?php

namespace WeltPixel\GA4\Plugin;

class HttpContext
{

    /**
     * GA4 context
     */
    const CONTEXT_GA4 = 'weltpixel_ga4';

    /**
     * @var \WeltPixel\GA4\Helper\Data
     */
    protected $helper;

    /**
     * @param \WeltPixel\GA4\Helper\Data $helper
     */
    public function __construct(
        \WeltPixel\GA4\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\App\Http\Context $subject
     * @return null
     */
    public function beforeGetVaryString(
        \Magento\Framework\App\Http\Context $subject
    ) {
        if ($this->helper->isEnabled() && $this->helper->isCookieRestrictionModeEnabled()) {
            $subject->setValue(
                self::CONTEXT_GA4,
                'isEnabled',
                ''
            );
        }
        return null;
    }
}
