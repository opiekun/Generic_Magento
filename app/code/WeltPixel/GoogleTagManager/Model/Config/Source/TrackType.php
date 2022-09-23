<?php
namespace WeltPixel\GoogleTagManager\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TrackType
 * @package WeltPixel\GoogleTagManager\Model\Config\Source
 */
class TrackType implements ArrayInterface
{
    /**
     * Return list of Id Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \WeltPixel\GoogleTagManager\Model\Dimension::DIMENSION_TYPE,
                'label' => __('Dimension')
            ),
            array(
                'value' => \WeltPixel\GoogleTagManager\Model\Dimension::METRIC_TYPE,
                'label' => __('Metric')
            )
        );
    }
}
