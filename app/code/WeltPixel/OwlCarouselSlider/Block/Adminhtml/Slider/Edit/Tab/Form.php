<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\Slider\Edit\Tab;

use WeltPixel\OwlCarouselSlider\Model\Status;
use WeltPixel\OwlCarouselSlider\Model\Slider;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
/**
 * Slider Form.
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const FIELD_NAME = 'slider';

    /**
     * @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory
     */
    protected $_fieldFactory;

    /**
     * [$_bannersliderHelper description].
     *
     * @var \WeltPixel\OwlCarouselSlider\Helper\Data
     */
    protected $_bannersliderHelper;

    /**
     * available status.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\Status
     */
    private $_status;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Template\Context                                $context
     * @param \WeltPixel\OwlCarouselSlider\Helper\Data                               $bannersliderHelper
     * @param \Magento\Framework\Registry                                            $registry
     * @param \Magento\Framework\Data\FormFactory                                    $formFactory
     * @param \Magento\Store\Model\System\Store                                      $systemStore
     * @param \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\Status                              $status
     * @param array                                                                  $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \WeltPixel\OwlCarouselSlider\Helper\Data $bannersliderHelper,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        \WeltPixel\OwlCarouselSlider\Model\Status $status,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->_bannersliderHelper = $bannersliderHelper;
        $this->_fieldFactory       = $fieldFactory;
        $this->_status       = $status;
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $slider = $this->getSlider();
        $isElementDisabled = true;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        /*
         * declare dependence
         */
        $dependenceBlock = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence'
        );

        // dependence field map array
        $elements = [];

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Slider Details')]);

        if ($slider->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name'     => 'title',
                'label'    => __('Title'),
                'title'    => __('Title'),
                'required' => true,
                'class'    => 'required-entry'
            ]
        );

        $fieldset->addField(
            'show_title',
            'radios',
            [
                'name'     => 'show_title',
                'label'    => __('Show Title'),
                'title'    => __('Show Title'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('Yes')],
                    ['value' => 0, 'label' => __('No')]
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $elements['status'] = $fieldset->addField(
            'status',
            'select',
            [
                'label'    => __('Slider Status'),
                'title'    => __('Slider Status'),
                'name'     => 'status',
                'options'  => $this->_status->getAllAvailableStatuses(),
                'disabled' => false,
                'required' => false,
                'note'     => 'If Enabled, the slider will be displayed. Make sure you inserted the Slider in the page. Insert the slider by following documentation.',
            ]
        );

        $elements['scheduled_ajax'] = $fieldset->addField(
            'scheduled_ajax',
            'select',
            [
                'name'     => 'scheduled_ajax',
                'label'    => __('Ajax Scheduled Banners'),
                'title'    => __('Ajax Scheduled Banners'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'if True, Ajax Frontend call will be used for Scheduled Banners.',
            ]
        );

        ########### Slider General Configuration ###########

        $elements['separator_one'] = $fieldset->addField(
            'separator_one',
            'note',
            [
                'text' => __('<strong>Slider General Configuration</strong>'),
            ]
        );

        $elements['nav'] = $fieldset->addField(
            'nav',
            'select',
            [
                'name'     => 'nav',
                'label'    => __('Next/Prev Buttons'),
                'title'    => __('Next/Prev Buttons'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, next/prev buttons are displayed. <br/>Slider Responsive Breakpoints options overwrites this option. Please check below Slider Responsive
                Breakpoints options.',
            ]
        );

        $elements['dots'] = $fieldset->addField(
            'dots',
            'select',
            [
                'name'     => 'dots',
                'label'    => __('Dots'),
                'title'    => __('Dots'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, dots navigation are displayed.',
            ]
        );

        $elements['dotsEach'] = $fieldset->addField(
            'dotsEach',
            'select',
            [
                'name'     => 'dotsEach',
                'label'    => __('Dots Each'),
                'title'    => __('Dots Each'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, Show dots for each item.',
            ]
        );

        $elements['thumbs'] = $fieldset->addField(
            'thumbs',
            'select',
            [
                'name'     => 'thumbs',
                'label'    => __('Thumbs'),
                'title'    => __('Thumbs'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, thumbs navigation are displayed.',
            ]
        );

        $elements['transition'] = $fieldset->addField(
            'transition',
            'select',
            [
                'name'     => 'transition',
                'label'    => __('Banner Transition Effect'),
                'title'    => __('Banner Transition Effect'),
                'required' => false,
                'values'   => Slider::getAvailableTransition(),
                'note'     => 'Effect of transition between slides.'
            ]
        );

        $elements['navSpeed'] = $fieldset->addField(
            'navSpeed',
            'text',
            [
                'name'     => 'navSpeed',
                'label'    => __('Navigation Speed'),
                'title'    => __('Navigation Speed'),
                'required' => false,
                'note'     => 'Navigation Speed. Insert value in ms. Write “4000” for a 4 seconds timeout'
            ]
        );

        $elements['dotsSpeed'] = $fieldset->addField(
            'dotsSpeed',
            'text',
            [
                'name'     => 'dotsSpeed',
                'label'    => __('Pagination Speed'),
                'title'    => __('Pagination Speed'),
                'required' => false,
                'note'     => 'Pagination Speed. Insert value in ms. Write “4000” for a 4 seconds timeout'
            ]
        );

        $elements['center'] = $fieldset->addField(
            'center',
            'select',
            [
                'name'     => 'center',
                'label'    => __('Center Item'),
                'title'    => __('Center Item'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'     => 'Center item. Works well with even an odd number of items.'
            ]
        );

        $elements['stagePadding'] = $fieldset->addField(
            'stagePadding',
            'text',
            [
                'name'     => 'stagePadding',
                'label'    => __('StagePadding'),
                'title'    => __('StagePadding'),
                'required' => false,
                'note'     => 'Padding left and right on stage (can see neighbours).'
            ]
        );

        $elements['margin'] = $fieldset->addField(
            'margin',
            'text',
            [
                'name'     => 'margin',
                'label'    => __('Margin'),
                'title'    => __('Margin'),
                'required' => false,
                'note'     => 'Set right margin for each item in carousel.',
            ]
        );

        $elements['items'] =$fieldset->addField(
            'items',
            'text',
            [
                'name'     => 'items',
                'label'    => __('Items'),
                'title'    => __('Items'),
                'required' => true,
                'class'    => 'validate-greater-than-zero',
                'note'     => 'The number of items you want to see on the screen. <br/>Slider Responsive Breakpoints options overwrites this option. Please check below Slider
                Responsive Breakpoints options.'
            ]
        );

        $elements['loop'] = $fieldset->addField(
            'loop',
            'select',
            [
                'name'     => 'loop',
                'label'    => __('Loop'),
                'title'    => __('Loop'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'     => 'Infinite loop. Duplicate last and first items to get loop illusion.'
            ]
        );

        $elements['lazyLoad'] = $fieldset->addField(
            'lazyLoad',
            'select',
            [
                'name'     => 'lazyLoad',
                'label'    => __('LazyLoad'),
                'title'    => __('LazyLoad'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'      => 'Lazy Load delays loading of images. Images outside of viewport are not loaded until user scrolls to them.'
            ]
        );

        $elements['autoplay'] = $fieldset->addField(
            'autoplay',
            'select',
            [
                'name'     => 'autoplay',
                'label'    => __('Autoplay'),
                'title'    => __('Autoplay'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
            ]
        );

        $elements['autoplayTimeout'] = $fieldset->addField(
            'autoplayTimeout',
            'text',
            [
                'name'     => 'autoplayTimeout',
                'label'    => __('AutoplayTimeout'),
                'title'    => __('AutoplayTimeout'),
                'required' => false,
                'note'     => 'Autoplay interval timeout. Insert value in ms. Write “4000” for a 4 seconds timeout'
            ]
        );

        $elements['autoplayHoverPause'] = $fieldset->addField(
            'autoplayHoverPause',
            'select',
            [
                'name'     => 'autoplayHoverPause',
                'label'    => __('AutoplayHoverPause'),
                'title'    => __('AutoplayHoverPause'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'     => 'Pause on mouse hover.'
            ]
        );

        $elements['autoHeight'] = $fieldset->addField(
            'autoHeight',
            'select',
            [
                'name'     => 'autoHeight',
                'label'    => __('Auto Height'),
                'title'    => __('Auto Height'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, each banner will adjust height.',
            ]
        );

        $elements['rtl'] = $fieldset->addField(
            'rtl',
            'select',
            [
                'name'     => 'rtl',
                'label'    => __('Direction Right To Left'),
                'title'    => __('Direction Right To Left'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, each banner will slide from right to left.',
            ]
        );

        ########### Slider Responsive Breakpoint ###########

        $elements['separator_two'] = $fieldset->addField(
            'separator_two',
            'note',
            [
               'text' => __('<strong>Slider Responsive Breakpoints.<br/>This configuration will overwrite the main slider options.</strong>'),
            ]
        );

        // Breakpoint 1
        $elements['s1'] = $fieldset->addField(
            's1',
            'note',
            [
                'text' => __('Breakpoint 1'),
            ]
        );

        $elements['nav_brk1'] = $fieldset->addField(
            'nav_brk1',
            'select',
            [
                'name'     => 'nav_brk1',
                'label'    => __('Next/Prev Buttons'),
                'title'    => __('Next/Prev Buttons'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, next/prev buttons are displayed.',
            ]
        );

        $elements['items_brk1'] = $fieldset->addField(
            'items_brk1',
            'text',
            [
                'name'     => 'items_brk1',
                'label'    => __('Items'),
                'title'    => __('Items'),
                'required' => true,
                'class'    => 'validate-greater-than-zero',
                'note'     => 'The number of items you want to see at this breakpoint and higher.'
            ]
        );

        // Breakpoint 2

        $elements['s2'] = $fieldset->addField(
            's2',
            'note',
            [
                'text' => __('Breakpoint 2'),
            ]
        );

        $elements['nav_brk2'] = $fieldset->addField(
            'nav_brk2',
            'select',
            [
                'name'     => 'nav_brk2',
                'label'    => __('Next/Prev Buttons'),
                'title'    => __('Next/Prev Buttons'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, next/prev buttons are displayed.',
            ]
        );

        $elements['items_brk2'] = $fieldset->addField(
            'items_brk2',
            'text',
            [
                'name'     => 'items_brk2',
                'label'    => __('Items'),
                'title'    => __('Items'),
                'required' => true,
                'class'    => 'validate-greater-than-zero',
                'note'     => 'The number of items you want to see at this breakpoint and higher.'
            ]
        );

        // Breakpoint 3

        $elements['s3'] = $fieldset->addField(
            's3',
            'note',
            [
                'text' => __('Breakpoint 3'),
            ]
        );

        $elements['nav_brk3'] = $fieldset->addField(
            'nav_brk3',
            'select',
            [
                'name'     => 'nav_brk3',
                'label'    => __('Next/Prev Buttons'),
                'title'    => __('Next/Prev Buttons'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, next/prev buttons are displayed.',
            ]
        );

        $elements['items_brk3'] = $fieldset->addField(
            'items_brk3',
            'text',
            [
                'name'     => 'items_brk3',
                'label'    => __('Items'),
                'title'    => __('Items'),
                'required' => true,
                'class'    => 'validate-greater-than-zero',
                'note'     => 'The number of items you want to see at this breakpoint and higher.'
            ]
        );

        // Breakpoint 4

        $elements['s4'] = $fieldset->addField(
            's4',
            'note',
            [
                'text' => __('Breakpoint 4'),
            ]
        );

        $elements['nav_brk4'] = $fieldset->addField(
            'nav_brk4',
            'select',
            [
                'name'     => 'nav_brk4',
                'label'    => __('Next/Prev Buttons'),
                'title'    => __('Next/Prev Buttons'),
                'required' => false,
                'values'   => [
                    ['value' => 1, 'label' => __('True')],
                    ['value' => 0, 'label' => __('False')]
                ],
                'note'    => 'If True, next/prev buttons are displayed.',
            ]
        );

        $elements['items_brk4'] = $fieldset->addField(
            'items_brk4',
            'text',
            [
                'name'     => 'items_brk4',
                'label'    => __('Items'),
                'title'    => __('Items'),
                'required' => true,
                'class'    => 'validate-greater-than-zero',
                'note'     => 'The number of items you want to see at this breakpoint and higher.'
            ]
        );

        /*
          * Add field map
          */
        foreach ($elements as $fieldMap) {
            $dependenceBlock->addFieldMap($fieldMap->getHtmlId(), $fieldMap->getName());
        }

        $mappingFieldDependence = $this->getMappingFieldDependence();
        /*
         * Add field dependence
         */
        foreach ($mappingFieldDependence as $dependence) {
            $negative = isset($dependence['negative']) && $dependence['negative'];
            if (is_array($dependence['fieldName'])) {
                foreach ($dependence['fieldName'] as $fieldName) {
                    $dependenceBlock->addFieldDependence(
                        $elements[$fieldName]->getName(),
                        $elements[$dependence['fieldNameFrom']]->getName(),
                        $this->getDependencyField($dependence['refField'], $negative)
                    );
                }
            } else {
                $dependenceBlock->addFieldDependence(
                    $elements[$dependence['fieldName']]->getName(),
                    $elements[$dependence['fieldNameFrom']]->getName(),
                    $this->getDependencyField($dependence['refField'], $negative)
                );
            }
        }

        /*
         * add child block dependence
         */
        $this->setChild('form_after', $dependenceBlock);

        if (!$slider->getId()) {
            $slider->setStatus($isElementDisabled ? Status::STATUS_ENABLED : Status::STATUS_DISABLED);
        }

        $form->setValues($slider->getData());
        $form->addFieldNameSuffix(self::FIELD_NAME);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * get dependency field.
     *
     * @return Magento\Config\Model\Config\Structure\Element\Dependency\Field [description]
     */
    public function getDependencyField($refField, $negative = false, $separator = ',', $fieldPrefix = '')
    {
        return $this->_fieldFactory->create(
            ['fieldData' => [
                'value' => (string)$refField,
                'negative' => $negative,
                'separator' => $separator],
                'fieldPrefix' => $fieldPrefix
            ]
        );
    }

    public function getMappingFieldDependence()
    {
        return [
            [
                'fieldName'     => 'center',
                'fieldNameFrom' => 'transition',
                'refField'      => 'fadeOut',
                'negative'      => true,
            ],
            [
                'fieldName'     => 'margin',
                'fieldNameFrom' => 'transition',
                'refField'      => 'fadeOut',
                'negative'      => true,
            ],
            [
                'fieldName'     => 'navSpeed',
                'fieldNameFrom' => 'transition',
                'refField'      => 'fadeOut',
                'negative'      => true,
            ],
            [
                'fieldName'     => 'dotsEach',
                'fieldNameFrom' => 'dots',
                'refField'      => '0',
                'negative'      => true,
            ],
            [
                'fieldName'     => 'dotsSpeed',
                'fieldNameFrom' => 'transition',
                'refField'      => 'fadeOut',
                'negative'      => true,
            ],
            [
                'fieldName'     => 'stagePadding',
                'fieldNameFrom' => 'transition',
                'refField'      => 'fadeOut',
                'negative'      => true,
            ],
            [
                'fieldName'     => 'stagePadding',
                'fieldNameFrom' => 'center',
                'refField'      => '1',
                'negative'      => true,
            ],
        ];
    }

    public function getSlider()
    {
        return $this->_coreRegistry->registry('slider');
    }

    public function getPageTitle()
    {
        return $this->getSlider()->getId()
            ? __("Edit Slider '%1'", $this->escapeHtml($this->getSlider()->getTitle()))
            : __('New Slider');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Slider Details');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Slider Details');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
