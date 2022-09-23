<?php

namespace WeltPixel\GA4\Plugin;

use Magento\Framework\App\Response\Http;

class ResponseHttp
{
    /**
     * @var \WeltPixel\GA4\Helper\Data
     */
    protected $helper;

    /**
     * @param \WeltPixel\GA4\Helper\Data $helper
     */
    public function __construct(
        \WeltPixel\GA4\Helper\Data $helper
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
            $gtmCodeSnippet = $this->helper->getDataLayerScript();
            $gtmCodeSnippet .= "\n";
            $gtmCodeSnippet .= $this->helper->getGtmCodeSnippetForHead();
            $content = str_replace("##ga4_snippet_scripts##", $gtmCodeSnippet, $content);
            $subject->setContent($content);
        }
    }
}
