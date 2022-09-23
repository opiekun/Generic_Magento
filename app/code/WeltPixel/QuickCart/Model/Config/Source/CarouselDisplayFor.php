<?php
namespace WeltPixel\QuickCart\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CarouselDisplayFor
 * @package WeltPixel\QuickCart\Model\Config\Source
 */
class CarouselDisplayFor implements ArrayInterface
{
    const FIRST_ITEM = 'first_item';
    const LAST_ITEM = 'last_item';

    /**
     * @var array
     */
    protected $_options = [
        self::FIRST_ITEM => 'First item in cart',
        self::LAST_ITEM =>  'Last item added in cart'
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
