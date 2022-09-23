<?php
namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class WidgetCustom extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * slider factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\CollectionFactory
     */
    protected $_slideCollectionFactory;

    /**
     * [__construct description].
     *
     * @param \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\CollectionFactory   $slideCollectionFactory
     */
    public function __construct(
        \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\CollectionFactory $slideCollectionFactory

    ) {
        $this->_slideCollectionFactory = $slideCollectionFactory;
    }

    /**
     * Retrieve the slider collection.
     *
     * @return \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\Collection
     */
    public function getSliderCollection()
    {
        $collection = $this->_slideCollectionFactory->create();

        return $collection;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $sliderCollection = $this->getSliderCollection();

        $values = [];

        $values[] = [
            'value' => 0, 'label' => __('Select Custom Slider...')
        ];
        
        foreach ($sliderCollection as $slider) {
            $values[] = [
                'value' => $slider->getId(), 'label' => $slider->getTitle()
            ];
        }

        return $values;
    }
}
