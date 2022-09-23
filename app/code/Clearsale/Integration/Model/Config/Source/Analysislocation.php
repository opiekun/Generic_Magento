<?php

namespace Clearsale\Integration\Model\Config\Source;

class Analysislocation implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {

        return [
            ['value' => 'USA', 'label' => __('USA')],
            ['value' => 'BRA', 'label' => __('BRA')]
        ];
    }
}
