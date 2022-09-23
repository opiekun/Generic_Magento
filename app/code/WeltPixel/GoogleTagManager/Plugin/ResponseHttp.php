<?php

namespace WeltPixel\GoogleTagManager\Plugin;

use Magento\Framework\App\Response\Http;

class ResponseHttp
{
    /**
     * @var \WeltPixel\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @param \WeltPixel\GoogleTagManager\Helper\Data $helper
     */
    public function __construct(
        \WeltPixel\GoogleTagManager\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\App\Http\Context $subject
     * @return null
     */
    public function beforeSendResponse(Http $subject)
    {
        $content = $subject->getContent();
        if ($this->helper->isEnabled() && $this->helper->isDevMoveJsBottomEnabled()) {
            $gtmCodeSnippet = $this->helper->getGtmCodeSnippetForHead();
            $content = str_replace("##gtm_snippet_scripts##", $gtmCodeSnippet, $content);
            $subject->setContent($content);
        }
    }
}
