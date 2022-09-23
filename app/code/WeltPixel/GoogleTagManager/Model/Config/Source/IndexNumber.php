<?php

namespace WeltPixel\GoogleTagManager\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class IndexNumber
 *
 * @package WeltPixel\GoogleTagManager\Model\Config\Source
 */
class IndexNumber implements ArrayInterface
{
    /**
     * Return list of Id Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $result = [];
        foreach (range(0, 100) as $index) {
            $result[] = [
                'value' => $index,
                'label' => $index
            ];
        }

        return $result;
    }
}