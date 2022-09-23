<?php

namespace ChargeLogic\Connect\Model\Source;

class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'DI', 'AE', 'JCB', 'DN');
    }
}
