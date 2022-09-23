<?php

namespace IWD\AddressValidation\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;

/**
 * Class ValidationMode
 * @package IWD\AddressValidation\Model\Config\Source
 */
class ValidationMode implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'ups', 'label' => __('UPS')],
            ['value' => 'usps', 'label' => __('USPS')],
            ['value' => 'google', 'label' => __('Google')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'ups' => __('UPS'),
            'usps' => __('USPS'),
            'google' => __('Google')
        ];
    }
}
