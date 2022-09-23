<?php
namespace PluginCompany\ProductPdf\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

abstract class AbstractOption implements ArrayInterface
{

    public function toOptionArray()
    {
        $options = array();
        foreach($this->toArray() as $k => $v){
            $options[] = array(
                'label' => __($v),
                'value' => $k
            );
        }
        return $options;
    }

}