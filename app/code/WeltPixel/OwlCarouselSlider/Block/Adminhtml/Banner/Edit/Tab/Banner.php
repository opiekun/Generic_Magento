<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Edit\Tab;

use WeltPixel\OwlCarouselSlider\Model\Status;

/**
 * Banner Edit tab.
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Banner extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;

    /**
     * slider factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\SliderFactory
     */
    protected $_sliderFactory;

    /**
     * @var \WeltPixel\OwlCarouselSlider\Model\Banner
     */
    protected $_bannerModel;

    /**
     * available status.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\Status
     */
    private $_status;

    /**
     * constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                  $context
     * @param \Magento\Framework\Registry                                              $registry
     * @param \Magento\Framework\Data\FormFactory                                      $formFactory
     * @param \Magento\Framework\DataObjectFactory                                     $objectFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\Banner                                $banner
     * @param \WeltPixel\OwlCarouselSlider\Model\SliderFactory                         $sliderFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\Status                                $status
     * @param array                                                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \WeltPixel\OwlCarouselSlider\Model\Banner $bannerModel,
        \WeltPixel\OwlCarouselSlider\Model\SliderFactory $sliderFactory,
        \WeltPixel\OwlCarouselSlider\Model\Status $status,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->_objectFactory = $objectFactory;
        $this->_bannerModel   = $bannerModel;
        $this->_sliderFactory = $sliderFactory;
        $this->_status        = $status;
    }

    /**
     * prepare layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $pageTitle = $this->getPageTitle();

        $this->getLayout()->getBlock('page.title')->setPageTitle($pageTitle);

        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'WeltPixel\OwlCarouselSlider\Block\Adminhtml\Form\Renderer\Fieldset\Element', $this->getNameInLayout()
                .'_fieldset_element'
            )
        );

        return $this;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $dataObj = $this->_objectFactory->create();

        /**
         * @var \WeltPixel\OwlCarouselSlider\Model\Banner $bannerModel
         */
        $bannerModel = $this->_coreRegistry->registry('banner');

        if ($sliderId = $this->getRequest()->getParam('loaded_slider_id')) {
            $bannerModel->setSliderId($sliderId);
        }

        $dataObj->addData($bannerModel->getData());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix($this->_bannerModel->getFormFieldHtmlIdPrefix());

        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Banner Details')]);
        
        if ($bannerModel->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $elements = [];

        $elements['title'] = $fieldset->addField(
            'title',
            'text',
            [
                'name'     => 'title',
                'label'    => __('Title'),
                'title'    => __('Title'),
                'required' => true,
            ]
        );

        $elements['show_title'] = $fieldset->addField(
            'show_title',
            'select',
            [
                'name'     => 'show_title',
                'label'    => __('Show Title'),
                'title'    => __('Show Title'),
                'required' => false,
                'options'  => [
                    1 => __('Yes'),
                    0 => __('No'),
                ]
            ]
        );

        $elements['description'] = $fieldset->addField(
            'description',
            'text',
            [
                'name'     => 'description',
                'label'    => __('Description'),
                'title'    => __('Description'),
                'required' => false,
            ]
        );

        $elements['show_description'] = $fieldset->addField(
            'show_description',
            'select',
            [
                'name'     => 'show_description',
                'label'    => __('Show Description'),
                'title'    => __('Show Description'),
                'required' => false,
                'options'  => [
                    1 => __('Yes'),
                    0 => __('No'),
                ]
            ]
        );

        $elements['status'] = $fieldset->addField(
            'status',
            'select',
            [
                'label'    => __('Status'),
                'title'    => __('Banner Status'),
                'name'     => 'status',
                'required' => false,
                'options'  => $this->_status->getAllAvailableStatuses(),
            ]
        );

        $slider = $this->_sliderFactory->create()->load($sliderId);

        if ($slider->getId()) {
            $elements['slider_id'] = $fieldset->addField(
                'slider_id',
                'multiselect',
                [
                    'label'    => __('Slider'),
                    'name'     => 'slider_id',
                    'required' => false,
                    'values'   => [
                        [
                            'value' => $slider->getId(),
                            'label' => $slider->getTitle(),
                        ],
                    ],
                    'note'    => 'Select to which Slider you wish to assign current banner. The assignments of banner can be done also later from Sliders Manager > Edit or Add New 
                    Slider > Slider Banners grid.',
                ]
            );
        } else {
            $elements['slider_id'] = $fieldset->addField(
                'slider_id',
                'multiselect',
                [
                    'label'    => __('Slider'),
                    'name'     => 'slider_id',
                    'required' => false,
                    'values'   => $bannerModel->getAvailableSliders(),
                    'note'    => 'Select to which Slider you wish to assign current banner. The assignments of banner can be done also later from Sliders Manager > Edit or Add New 
                    Slider > Slider Banners grid.',
                ]
            );
        }

        $elements['url'] = $fieldset->addField(
            'url',
            'text',
            [
                'title'    => __('URL'),
                'label'    => __('URL'),
                'name'     => 'url',
                'required' => false,
                'note'    => 'Set the URL where the banner/button/custom HTML content should link when clicked on. URL Note 1: set the URL without your store base url, ex. /women.html; URL Note 2: if 
                "Text Button" option is filled, URL will be linked to the button instead of banner.',
            ]
        );

        if($this->showGaFields()){
            $elements['ga_promo_id'] = $fieldset->addField(
                'ga_promo_id',
                'text',
                [
                    'title'    => __('GA Promo ID'),
                    'label'    => __('GA Promo ID'),
                    'name'     => 'ga_promo_id',
                    'required' => false
                ]
            );
            $elements['ga_promo_name'] = $fieldset->addField(
                'ga_promo_name',
                'text',
                [
                    'title'    => __('GA Promo Name'),
                    'label'    => __('GA Promo Name'),
                    'name'     => 'ga_promo_name',
                    'required' => false
                ]
            );
            $elements['ga_promo_creative'] = $fieldset->addField(
                'ga_promo_creative',
                'text',
                [
                    'title'    => __('GA Promo Creative'),
                    'label'    => __('GA Promo Creative'),
                    'name'     => 'ga_promo_creative',
                    'required' => false
                ]
            );
            $elements['ga_promo_position'] = $fieldset->addField(
                'ga_promo_position',
                'text',
                [
                    'title'    => __('GA Promo Position'),
                    'label'    => __('GA Promo Position'),
                    'name'     => 'ga_promo_position',
                    'required' => false
                ]
            );
        }



        $elements['target'] = $fieldset->addField(
            'target',
            'select',
            [
                'label'  => __('Target'),
                'name'   => 'target',
                'values' => [
                    [
                        'value' => '_self',
                        'label' => __('Same Window'),
                    ],
                    [
                        'value' => '_blank',
                        'label' => __('New Window Tab'),
                    ],
                ],
                'required' => false,
                'note'    => 'Choose how the URL should be opened: same window or in a new tab.',
            ]
        );

        $elements['button_text'] = $fieldset->addField(
            'button_text',
            'text',
            [
                'title'    => __('Button Text'),
                'label'    => __('Button Text'),
                'name'     => 'button_text',
                'required' => false,
                'note'     => __('Insert the text which is displayed on the button. To display the button make sure you fill the URL field also.')
            ]
        );

        $elements['banner_type'] = $fieldset->addField(
            'banner_type',
            'select',
            [
                'label'    => __('Banner Type'),
                'name'     => 'banner_type',
                'values'   => $bannerModel->getAvailableBannerType(),
                'required' => false,
                'note'     => 'Choose banner type: Image, Video or Custom. In Custom you can insert HTML content.',
            ]
        );

        $elements['video'] = $fieldset->addField(
            'video',
            'textarea',
            [
                'title'    => __('Video'),
                'label'    => __('Video'),
                'name'     => 'video',
                'required' => false,
            ]
        );

        $elements['image'] = $fieldset->addField(
            'image',
            'image',
            [
                'title'    => __('Desktop Image'),
                'label'    => __('Desktop Image'),
                'name'     => 'image',
                'note'     => 'Accepted images: jpg, jpeg, gif, png',
                'required' => false,
            ]
        );


        $elements['mobile_image'] = $fieldset->addField(
            'mobile_image',
            'image',
            [
                'title'    => __('Mobile Image'),
                'label'    => __('Mobile Image'),
                'name'     => 'mobile_image',
                'note'     => 'Accepted images: jpg, jpeg, gif, png',
                'required' => false,
            ]
        );

        $elements['thumb_image'] = $fieldset->addField(
            'thumb_image',
            'image',
            [
                'title'    => __('Thumb Image'),
                'label'    => __('Thumb Image'),
                'name'     => 'thumb_image',
                'note'     => 'Accepted images: jpg, jpeg, gif, png',
                'required' => false,
            ]
        );

        $elements['custom'] = $fieldset->addField(
            'custom',
            'textarea',
            [
                'title'    => __('Create Custom Banner here'),
                'label'    => __('Create Custom Banner here'),
                'name'     => 'custom',
                'required' => false,
            ]
        );

        $elements['alt_text'] = $fieldset->addField(
            'alt_text',
            'text',
            [
                'title'    => __('Alt Text'),
                'label'    => __('Alt Text'),
                'name'     => 'alt_text',
                'required' => false,
                'note'     => 'Specify an alternate text for image in case the image cannot be displayed.',
            ]
        );

        if ($bannerModel->getId()) {
            $elements['bannerclass'] = $fieldset->addField(
                'bannerclass',
                'label',
                [
                    'title' => __('Banner Class'),
                    'label' => __('Banner Class'),
                    'name' => 'banner_class',
                    'value' => 'banner-' . $bannerModel->getId(),
                    'note' => __('Wrapper class name of the current banner. It can be used in Custom CSS field to style each banner from the slider. This is displayed only after you 
                    saved the banner.')
                ]
            );
        }

        $elements['custom_content'] = $fieldset->addField(
            'custom_content',
            'textarea',
            [
                'title'    => __('Custom HTML Content'),
                'label'    => __('Custom HTML Content'),
                'name'     => 'custom_content',
                'required' => false,
            ]
        );

        $elements['wrap_link'] = $fieldset->addField(
            'wrap_link',
            'select',
            [
                'name'     => 'wrap_link',
                'label'    => __('Set Link For Custom HTML Content'),
                'title'    => __('Wrap Link'),
                'required' => false,
                'options'  => [
                    1 => __('Yes'),
                    0 => __('No'),
                ],
                'note'    => 'If set to YES, custom HTML content block will be set as link/anchor. Make sure you fill the URL field also. '
            ]
        );
    
        $elements['custom_css'] = $fieldset->addField(
            'custom_css',
            'textarea',
            [
                'title'    => __('Custom CSS'),
                'label'    => __('Custom CSS'),
                'name'     => 'custom_css',
                'required' => false,
                'note'     => 'Insert your custom CSS style.',
            ]
        );
        
        $dateFormat = $this->_localeDate->getDateFormatWithLongYear();
        $timeFormat = $this->_localeDate->getTimeFormat();

        if ($dataObj->hasData('valid_from')) {
            $datetime = new \DateTime($dataObj->getData('valid_from'));
            $dataObj->setData('valid_from',
                $datetime->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone())));
        }

        if ($dataObj->hasData('valid_to')) {
            $datetime = new \DateTime($dataObj->getData('valid_to'));
            $dataObj->setData('valid_to',
                $datetime->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone())));
        }

        $style = 'color: #000;background-color: #fff; font-weight: bold; font-size: 13px;';
        $elements['valid_from'] = $fieldset->addField(
            'valid_from',
            'date',
            [
                'name'        => 'valid_from',
                'label'       => __('Banner Valid From'),
                'title'       => __('Valid From'),
                'required'    => true,
                'readonly'    => true,
                'style'       => $style,
                'class'       => 'required-entry',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'note'        => implode(' ', [$dateFormat, $timeFormat]) . '<br/>The banner is displayed from selected date and time.' .
                            '<br/><b>For schedules to work make sure to enable Ajax Scheduled Banners from Slider Details.</b>',
            ]
        );

        $elements['valid_to'] = $fieldset->addField(
            'valid_to',
            'date',
            [
                'name'        => 'valid_to',
                'label'       => __('Banner Valid To'),
                'title'       => __('Valid To'),
                'required'    => true,
                'readonly'    => true,
                'style'       => $style,
                'class'       => 'required-entry',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'note'        => implode(' ', [$dateFormat, $timeFormat]) . '<br/>The Banner is displayed until selected date and time.' .
                    '<br/><b>For schedules to work make sure to enable Ajax Scheduled Banners from Slider Details.</b>',
            ]
        );

        $form->addValues($dataObj->getData());

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap(
                    "{$htmlIdPrefix}banner_type",
                    'banner_type'
                )
                ->addFieldMap(
                    "{$htmlIdPrefix}video",
                    'video'
                )
                ->addFieldMap(
                    "{$htmlIdPrefix}image",
                    'image'
                )
                ->addFieldMap(
                    "{$htmlIdPrefix}mobile_image",
                    'mobile_image'
                )
                ->addFieldMap(
                    "{$htmlIdPrefix}custom",
                    'custom'
                )
                ->addFieldMap(
                    "{$htmlIdPrefix}alt_text",
                    'alt_text'
                )
                ->addFieldDependence(
                    'image',
                    'banner_type',
                    '1'
                )
                ->addFieldDependence(
                    'mobile_image',
                    'banner_type',
                    '1'
                )
                ->addFieldDependence(
                    'alt_text',
                    'banner_type',
                    '1'
                )
                ->addFieldDependence(
                    'video',
                    'banner_type',
                    '2'
                )
                ->addFieldDependence(
                    'custom',
                    'banner_type',
                    '3'
                )
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve the banner model.
     *
     * @return \WeltPixel\OwlCarouselSlider\Model\Banner
     */
    public function getBanner()
    {
        return $this->_coreRegistry->registry('banner');
    }

    /**
     * Return the page title.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getBanner()->getId()
            ? __("Edit Banner '%1'", $this->escapeHtml($this->getBanner()->getTitle())) : __('New Banner');
    }

    /**
     * Prepare tab label.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Banner Details');
    }

    /**
     * Prepare tab title.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Banner Details');
    }

    /**
     * Can show tab in tabs.
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Checks if Google Analytics Tracking for banners is enabled
     *
     * @return boolean
     */
    public function showGaFields()
    {
        $sysPath = 'weltpixel_owl_slider_config/general/enable_google_tracking';
        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
