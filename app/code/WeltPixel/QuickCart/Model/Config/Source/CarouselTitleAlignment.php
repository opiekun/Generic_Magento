<?php
namespace WeltPixel\QuickCart\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CarouselTitleAlignment
 * @package WeltPixel\QuickCart\Model\Config\Source
 */
class CarouselTitleAlignment implements ArrayInterface
{
    const ALIGN_LEFT = 'align_left';
    const ALIGN_CENTER = 'align_center';
    const ALIGN_RIGHT = 'align_right';

    /**
     * @var array
     */
    protected $_options = [
        self::ALIGN_LEFT => 'Left',
        self::ALIGN_CENTER => 'Center',
        self::ALIGN_RIGHT =>  'Right'
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
