<?php

namespace WeltPixel\GA4\Plugin;

class QuoteConfig
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
        )
    {
        $this->helper = $helper;
    }


    /**
     * Add the brand attribute to the quote item product collection as attribute
     * @param \Magento\Quote\Model\Quote\Config $config
     * @param $result
     * @return array
     */
    public function afterGetProductAttributes(
        \Magento\Quote\Model\Quote\Config $config,
        $result
    )
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        $brandAttribute =$this->helper->getBrandAttribute();
        $result[] = $brandAttribute;

        for ($i=1; $i<=5; $i++) {
            if ($this->helper->trackCustomAttribute($i)) {
                $result[] = $this->helper->getCustomAttributeCode($i);;
            }
        }

        return $result;
    }

}
