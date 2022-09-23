<?php

namespace Clearsale\Integration\Model\Config\Source;

class Environment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {

        return [
            ['value' => 'https://integration.clear.sale/', 'label' => __('Production')],
            ['value' => 'https://sandbox.clear.sale/', 'label' => __('SandBox')]
        ];
    }
}
