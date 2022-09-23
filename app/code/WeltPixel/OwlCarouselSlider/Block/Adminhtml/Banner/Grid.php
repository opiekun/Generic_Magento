<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner;

/**
 * Banner grid.
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * banner collection factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_bannerCollectionFactory;

    /**
     * slider collection factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\CollectionFactory
     */
    protected $_sliderCollectionFactory;

    /**
     * available status.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\Status
     */
    private $_status;

    /**
     * construct.
     *
     * @param \Magento\Backend\Block\Template\Context                                   $context
     * @param \Magento\Backend\Helper\Data                                              $backendHelper
     * @param \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\Status                                 $status
     * @param array                                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory,
        \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\CollectionFactory $sliderCollectionFactory,
        \WeltPixel\OwlCarouselSlider\Model\Status $status,
        array $data = []
    ) {
        $this->_bannerCollectionFactory = $bannerCollectionFactory;
        $this->_sliderCollectionFactory = $sliderCollectionFactory;
        $this->_status = $status;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('bannerGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        /** @var \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\Collection $collection */
        $collection = $this->_bannerCollectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('Banner ID'),
                'type'   => 'number',
                'index'  => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'slider_id',
            [
                'header' => __('Slider ID'),
                'type'   => 'number',
                'index'  => 'slider_id',
                'renderer' => 'WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Helper\Renderer\SliderIds',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'  => __('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => $this->_status->getAllAvailableStatuses(),
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index'  => 'title',
                'width'  => '50px',
            ]
        );

        $this->addColumn(
            'image',
            [
                'header'   => __('Desktop Image'),
                'width'    => '50px',
                'filter'   => false,
                'renderer' => 'WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Helper\Renderer\Image',
                'align'  => 'center',
            ]
        );

        $this->addColumn(
            'mobile_image',
            [
                'header'   => __('Mobile Image'),
                'width'    => '50px',
                'filter'   => false,
                'renderer' => 'WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Helper\Renderer\MobileImage',
                'align'  => 'center',
            ]
        );

        $this->addColumn(
            'thumb_image',
            [
                'header'   => __('Thumb Image'),
                'width'    => '50px',
                'filter'   => false,
                'renderer' => 'WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Helper\Renderer\ThumbImage',
                'align'  => 'center',
            ]
        );

        $this->addColumn(
            'valid_from',
            [
                'header'   => __('Valid From'),
                'type'     => 'datetime',
                'index'    => 'valid_from',
                'width'    => '50px',
                'timezone' => true,
            ]
        );

        $this->addColumn(
            'valid_to',
            [
                'header'   => __('Valid To'),
                'type'     => 'datetime',
                'index'    => 'valid_to',
                'width'    => '50px',
                'timezone' => true,
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header'  => __('Edit'),
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url'     => ['base' => '*/*/edit'],
                        'field'   => 'id',
                    ],
                ],
                'filter'   => false,
                'sortable' => false,
                'index'    => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get slider available option.
     *
     * @return array
     */
    public function getSliderAvailableOption()
    {
        $option = [];

        $sliderCollection = $this->_sliderCollectionFactory->create()->addFieldToSelect(['title']);

        if (count($sliderCollection)) {
            foreach ($sliderCollection as $slider) {
                $option[$slider->getId()] = $slider->getTitle();
            }
        }

        return $option;
    }

    /**
     * Prepare grid massaction actions
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');

        $this->getMassactionBlock()->setFormFieldName('banner');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label'   => __('Delete'),
                'url'     => $this->getUrl('weltowlcarousel/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        $status = $this->_status->getAllAvailableStatuses();
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change Status'),
                'url'   => $this->getUrl('weltowlcarousel/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => __('Status'),
                        'values' => $status,
                    ],
                ],
            ]
        );

        return $this;
    }

    /**
     * Retrieve grid reload url
     *
     * @return string;
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
