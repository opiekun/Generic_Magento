<?php


namespace WeltPixel\QuickCart\Model\Config\Source;


class QuantitySignTypes implements \Magento\Framework\Option\ArrayInterface
{
    const QTY_ARROWS = 'arrows';
    const QTY_PLUSMINUS = 'plusminus';
    const QTY_DEFAULT = 'default';

    /**
     * Return list of QtyType Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::QTY_ARROWS,
                'label' => __('Arrows')
            ),
            array(
                'value' => self::QTY_PLUSMINUS,
                'label' => __('Plus-Minus')
            ),
            array(
                'value' => self::QTY_DEFAULT,
                'label' => __('Default Input')
            )
        );
    }
}
