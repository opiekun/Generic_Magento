<?php
namespace WeltPixel\QuickCart\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CarouselItemDisplayNumber
 * @package WeltPixel\QuickCart\Model\Config\Source
 */
class CarouselItemDisplayNumber implements ArrayInterface
{
    protected $_options = [
        1 => 1,
        2 => 2
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_options as $id => $optionType) :
            $options[] = [
                'value' => $id,
                'label' => $optionType
            ];
        endforeach;
        return $options;
    }
}
