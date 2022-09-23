<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Data\Element;

abstract class AbstractElement extends \Magento\Framework\DataObject
{
    const TAB_GENERAL    = 'tab_general';
    const TAB_DISPLAY    = 'tab_display';
    const TAB_DESIGN     = 'tab_design';
    const TAB_RESPONSIVE = 'tab_responsive';

    /**
     * @var Magezon\Builder\Data\Form\Element\Fieldset
     */
    protected $_formTab;

    /**
     * @var array
     */
    protected $_formFields;

    /**
     * @var \Magezon\Builder\Data\Form
     */
    protected $_form;

    /**
     * @var \Magezon\Builder\Helper\Data
     */
    protected $builderHelper;

    /**
     * @var array
     */
    protected $_defaultValues = [];

    /**
     * @var array
     */
    protected $_allFields = [];

    /**
     * @param \Magezon\Builder\Data\FormFactory $formFactory 
     * @param \Magezon\Builder\Helper\Data      $builderHelper  
     * @param array                             $data        
     */
    public function __construct(
        \Magezon\Builder\Data\FormFactory $formFactory,
        \Magezon\Builder\Helper\Data $builderHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_construct();
        $this->_form         = $formFactory->create();
        $this->builderHelper = $builderHelper;
    }

    /**
     * Internal constructor, that is called from real constructor
     *
     * @return void
     */
    protected function _construct()
    {

    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $this->prepareForm();
        $config           = $this->getData();
        $config['form']   = $this->getFormFields();
        $config['fields'] = $this->getAllFields();
        $defaultValues    = $this->getFormDefaultValues();
        if ($this->getData('default')) {
            $defaultValues = array_replace_recursive($this->getData('default'), $defaultValues);
        }
        $config['defaultValues'] = $defaultValues;
        return $config;
    }

    /**
     * @return array
     */
    public function getFormDefaultValues()
    {
        $this->getFormFields();
        $defaultValues = $this->_defaultValues;
        if ($this->getDefaultValues()) {
            $defaultValues = array_replace_recursive($defaultValues, $this->getDefaultValues());
        }
        if ($this->getData('default')) {
            $defaultValues = array_replace_recursive($defaultValues, $this->getData('default'));
        }
        return $defaultValues;
    }

    /**
     * @return array
     */
    public function getAllFields()
    {
        $this->getFormFields();
        return $this->_allFields;
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }

    /**
     * Get form
     *
     * @return \Magezon\Builder\Data\Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @return array
     */
    public function getElementFields($elements, $key = '')
    {
        $excludeFields = ['sortOrder'];
        $result = [];
        $fields = [];
        foreach ($elements as $k => $element) {
            $orgiKey = $key;
            $id      = $element->getId();
            $type    = $element->getType();
            $field   = $element->getConfig();
            if (!isset($field['config']['sortOrder'])) $field['config']['sortOrder'] = 0;

            if (isset($field['config']['key'])) {
                if ($key) {
                    $key .= '/' . $field['config']['key'];
                } else {
                    $key = $field['config']['key'];
                }
                $this->_allFields[] = $field['config']['key'];
            }

            if (isset($field['config']['defaultValue'])) {
                $this->_defaultValues = $this->builderHelper->getArrayManager()->set($key, $this->_defaultValues, $field['config']['defaultValue']);
            }

            $children = $element->getElements();
            if ($children && $children->count()) {
                if ($element->getType() == 'dynamicRows' || $element->getType() == 'tab') {
                    $key = '';
                    $field['config']['templateOptions']['children'] = $this->getElementFields($children, $key);
                } else {
                    $field['children'] = $this->getElementFields($children, $key);
                }
            }
            $field['config']['templateOptions']['builderType'] = $type;
            $field['config']['templateOptions']['elementId']   = $id;

            if (!isset($field['config']['className'])) $field['config']['className'] = '';

            if (isset($field['config']['templateOptions']['controlInline']) && $field['config']['templateOptions']['controlInline']) {
                $field['config']['className'] .= ' mgz__field-control-inline';
            }

            if (isset($field['config']['templateOptions']['controlAutoWidth']) && $field['config']['templateOptions']['controlAutoWidth']) {
                $field['config']['className'] .= ' mgz__field-control-auto-width';
            }

            $fields[$id] = $field;

            usort($fields, function($a, $b) {
                return ($a['config']['sortOrder'] > $b['config']['sortOrder']);
            });
            $key = $orgiKey;
        }
        foreach ($fields as $_field) {
            foreach ($excludeFields as $key) {
                unset($_field['config'][$key]);
            }
            $result[] = $_field;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getFormFields()
    {
        if ($this->_formFields == NULL) {
            $this->_formFields = $this->getElementFields($this->getForm()->getElements()->usort([$this, "compare"]));
        }
        return $this->_formFields;
    }

    /**
     * @param $firstLink
     * @param $secondLink
     * @return int
     */
    private function compare($firstLink, $secondLink)
    {
        return ($firstLink['sortOrder'] > $secondLink['sortOrder']);
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function getFormTab()
    {
        if ($this->_formTab == NULL) {
            $this->_formTab = $this->getForm()->addTab('tab', ['className' => 'mgz-modal-tab']);
        }
        return $this->_formTab;
    }

    /**
     * Add tab
     *
     * @param string $name
     * @param array $options
     * @return \Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function addTab($name, $options = [])
    {
        return $this->getFormTab()->addFieldset($name, $options);
    }

    /**
     * Remove tab by name
     */
    public function removeTab($tab)
    {
        $elements = $this->_formTab->getElements();
        $elements->remove($tab);
        return $this;
    }

    /**
     * Prepare modal components
     */
    public function prepareForm()
    {
        $this->prepareGeneralTab();
        $this->prepareDesignTab();
        if ($this->getData('resizable')) {
            $this->prepareResponsiveTab();
        }
        return $this;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
        $general = $this->addTab(
            self::TAB_GENERAL,
            [
                'sortOrder'       => 0,
                'templateOptions' => [
                    'label' => __('General')
                ]
            ]
        );

            $general->addChildren(
                'animation_in',
                'select',
                [
                    'sortOrder'       => 500,
                    'key'             => 'animation_in',
                    'className'       => 'mgz-inner-widthauto',
                    'templateOptions' => [
                        'templateUrl' => 'Magezon_Builder/js/templates/form/element/animation-style.html',
                        'element'     => 'Magezon_Builder/js/form/element/animation-in',
                        'label'       => __('CSS Animation'),
                        'note'        => __('Select type of animation for element to be animated when it "enters" the browsers viewport (Note: works only in modern browsers).')
                    ]
                ]
            );

            $common1 = $general->addContainerGroup(
                'common1',
                [
                    'sortOrder'      => 510,
                    'hideExpression' => '!model.animation_in'
                ]
            );

                $common1->addChildren(
                    'animation_duration',
                    'number',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'animation_duration',
                        'templateOptions' => [
                            'label'       => __('Animation Duration (s)'),
                            'placeholder' => '0.5'
                        ]
                    ]
                );

                $common1->addChildren(
                    'animation_delay',
                    'number',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'animation_delay',
                        'templateOptions' => [
                            'label' => __('Animation Delay(s)')
                        ]
                    ]
                );

                $common1->addChildren(
                    'animation_infinite',
                    'toggle',
                    [
                        'sortOrder'       => 30,
                        'key'             => 'animation_infinite',
                        'templateOptions' => [
                            'label' => __('Animation Infinite')
                        ]
                    ]
                );

            $common2 = $general->addContainerGroup(
                'common2',
                [
                    'sortOrder' => 520
                ]
            );

                $common2->addChildren(
                    'disable_element',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'disable_element',
                        'className'       => 'mgz-width80',
                        'templateOptions' => [
                            'label' => __('Disable Element')
                        ]
                    ]
                );

                $common2->addChildren(
                    'hidden_default',
                    'toggle',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'hidden_default',
                        'className'       => 'mgz-width80',
                        'templateOptions' => [
                            'label' => __('Hide on Page Load')
                        ],
                        'hideExpression' => 'model.disable_element'
                    ]
                );

                $common2->addChildren(
                    'hide_element',
                    'text',
                    [
                        'sortOrder' => 30,
                        'templateOptions' => [
                            'label'       => __('Hide on'),
                            'element'     => 'Magezon_Builder/js/form/element/hide_on',
                            'templateUrl' => 'Magezon_Builder/js/templates/form/element/hide_on.html',
                        ],
                        'hideExpression' => 'model.disable_element'
                    ]
                );

            $common4 = $general->addContainerGroup(
                'common4',
                [
                    'sortOrder' => 530
                ]
            );

                $common4->addChildren(
                    'el_id',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'el_id',
                        'templateOptions' => [
                            'label' => __('Element ID'),
                            'note'  => __('Enter element ID (Note: make sure it is unique and valid according to <a href="http://www.w3schools.com/tags/att_global_id.asp" target="_blank">w3c specification</a>)')
                        ]
                    ]
                );

                $common4->addChildren(
                    'z_index',
                    'number',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'z_index',
                        'templateOptions' => [
                            'label' => __('Z-Index'),
                            'min'   => 0
                        ]
                    ]
                );

            $common5 = $general->addContainerGroup(
                'common5',
                [
                    'sortOrder' => 540
                ]
            );

                $common5->addChildren(
                    'el_class',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'el_class',
                        'templateOptions' => [
                            'label' => __('Element Class Attribute'),
                            'note'  => __('Style particular content element differently - add a class name and refer to it in custom CSS.')
                        ]
                    ]
                );

                $common5->addChildren(
                    'el_inner_class',
                    'text',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'el_inner_class',
                        'templateOptions' => [
                            'label' => __('Element Inner Class Attribute'),
                            'note'  => __('Style particular content element differently - add a class name and refer to it in custom CSS.')
                        ]
                    ]
                );

        return $general;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareDesignTab()
    {
        $design = $this->addTab(
            self::TAB_DESIGN,
            [
                'sortOrder'       => 100,
                'templateOptions' => [
                    'label' => __('Design Options')
                ]
            ]
        );

        $container1 = $design->addContainerGroup(
            'container1',
            [
                'sortOrder' => 10,
                'className' => 'mgz-design-wrapper',
            ]
        );

            $container2 = $container1->addContainer(
                'container2',
                [
                    'sortOrder' => 5,
                    'className' => 'mgz-design-layout-wrapper'
                ]
            );

                $container2->addChildren(
                    'device_type',
                    'select',
                    [
                        'key'             => 'device_type',
                        'sortOrder'       => 5,
                        'defaultValue'    => 'all',
                        'templateOptions' => [
                            'label'            => __('Device type'),
                            'controlInline'    => true,
                            'controlAutoWidth' => true,
                            'options'          => $this->getDeviceType()
                        ]
                    ]
                );

                $responsives = $container2->addTab(
                    'responsives_wrapper',
                    [
                        'sortOrder' => 10,
                        'className' => 'modal-tabs-style1'
                    ]
                );

                    $xl = $responsives->addContainer(
                        'xl',
                        [
                            'sortOrder'       => 10,
                            'templateOptions' => [
                                'label' => '<i title="Desktop" class="mgz-icon mgz-icon-desktop"></i>'
                            ]
                        ]
                    );

                        $this->prepareModeCssBox($xl, '');

                    $lg = $responsives->addContainer(
                        'lg',
                        [
                            'sortOrder'       => 20,
                            'templateOptions' => [
                                'label' => '<i title="Tablet Landscape" class="mgz-icon mgz-icon-tablet-landscape"></i>'
                            ],
                            'hideExpression' => 'model.device_type=="all"'
                        ]
                    );

                        $this->prepareModeCssBox($lg, 'lg_');

                    $md = $responsives->addContainer(
                        'md',
                        [
                            'sortOrder'       => 30,
                            'templateOptions' => [
                                'label' => '<i title="Tablet Portrait" class="mgz-icon mgz-icon-tablet-portrait"></i>'
                            ],
                            'hideExpression' => 'model.device_type=="all"'
                        ]
                    );

                        $this->prepareModeCssBox($md, 'md_');

                    $sm = $responsives->addContainer(
                        'sm',
                        [
                            'sortOrder'       => 40,
                            'templateOptions' => [
                                'label' => '<i title="Mobile Landscape" class="mgz-icon mgz-icon-mobile-landscape"></i>'
                            ],
                            'hideExpression' => 'model.device_type=="all"'
                        ]
                    );

                        $this->prepareModeCssBox($sm, 'sm_');

                    $xs = $responsives->addContainer(
                        'xs',
                        [
                            'sortOrder'       => 50,
                            'templateOptions' => [
                                'label' => '<i title="Mobile Portrait" class="mgz-icon mgz-icon-mobile-portrait"></i>'
                            ],
                            'hideExpression' => 'model.device_type=="all"'
                        ]
                    );

                        $this->prepareModeCssBox($xs, 'xs_');

        return $design;
    }

    public function prepareModeCssBox($parent, $prefix = '')
    {
        $parent->addChildren(
            'simply',
            'checkbox',
            [
                'key'             => $prefix . 'simply',
                'className'       => 'mgz-design-simply',
                'sortOrder'       => 0,
                'templateOptions' => [
                    'element'       => 'Magezon_Builder/js/form/element/simply',
                    'checkboxLabel' => __('Simplify Controls'),
                    'prefix'        => $prefix
                ]
            ]
        );

            $borderRadius = $parent->addFieldset(
                'radius',
                [
                    'sortOrder'       => 10,
                    'className'       => 'mgz-design-layout',
                    'templateOptions' => [
                        'label' => __('Radius'),
                        'focus' => true
                    ]
                ]
            );

                $borderRadius->addChildren(
                    'border_top_left_radius',
                    'text',
                    [
                        'key'             => $prefix . 'border_top_left_radius',
                        'className'       => 'mgz-design-top',
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'label'       => __('Border Radius Top Left'),
                            'placeholder' => '-'
                        ],
                        'expressionProperties' => [
                            'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                        ]
                    ]
                );

                $borderRadius->addChildren(
                    'border_top_right_radius',
                    'text',
                    [
                        'key'             => $prefix . 'border_top_right_radius',
                        'className'       => 'mgz-design-right',
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label'       => __('Border Top Radius Right'),
                            'placeholder' => '-'
                        ]
                    ]
                );

                $borderRadius->addChildren(
                    'border_bottom_right_radius',
                    'text',
                    [
                        'key'             => $prefix . 'border_bottom_right_radius',
                        'className'       => 'mgz-design-bottom',
                        'sortOrder'       => 30,
                        'templateOptions' => [
                            'label'       => __('Border Radius Bottom Right'),
                            'placeholder' => '-'
                        ],
                        'expressionProperties' => [
                            'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                        ]
                    ]
                );

                $borderRadius->addChildren(
                    'border_bottom_left_radius',
                    'text',
                    [
                        'key'             => $prefix . 'border_bottom_left_radius',
                        'className'       => 'mgz-design-left',
                        'sortOrder'       => 40,
                        'templateOptions' => [
                            'label'       => __('Border Radius Bottom Left'),
                            'placeholder' => '-'
                        ],
                        'expressionProperties' => [
                            'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                        ]
                    ]
                );

                    $margin = $borderRadius->addFieldset(
                        'margin',
                        [
                            'sortOrder'       => 100,
                            'templateOptions' => [
                                'label' => __('Margin')
                            ]
                        ]
                    );

                        $margin->addChildren(
                            'margin_top',
                            'text',
                            [
                                'key'             => $prefix . 'margin_top',
                                'className'       => 'mgz-design-top',
                                'sortOrder'       => 10,
                                'templateOptions' => [
                                    'label'       => __('Margin Top'),
                                    'placeholder' => '-'
                                ]
                            ]
                        );

                        $margin->addChildren(
                            'margin_right',
                            'text',
                            [
                                'key'             => $prefix . 'margin_right',
                                'className'       => 'mgz-design-right',
                                'sortOrder'       => 20,
                                'templateOptions' => [
                                    'label'       => __('Margin Right'),
                                    'placeholder' => '-'
                                ],
                                'expressionProperties' => [
                                    'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                ]
                            ]
                        );

                        $margin->addChildren(
                            'margin_bottom',
                            'text',
                            [
                                'key'             => $prefix . 'margin_bottom',
                                'className'       => 'mgz-design-bottom',
                                'sortOrder'       => 30,
                                'templateOptions' => [
                                    'label' => __('Margin Bottom'),
                                    'placeholder' => '-'
                                ],
                                'expressionProperties' => [
                                    'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                ]
                            ]
                        );

                        $margin->addChildren(
                            'margin_left',
                            'text',
                            [
                                'key'             => $prefix . 'margin_left',
                                'className'       => 'mgz-design-left',
                                'sortOrder'       => 40,
                                'templateOptions' => [
                                    'label'       => __('Margin Left'),
                                    'placeholder' => '-'
                                ],
                                'expressionProperties' => [
                                    'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                ]
                            ]
                        );

                        $border = $margin->addFieldset(
                            'border',
                            [
                                'sortOrder'       => 50,
                                'className'       => 'mgz-design-border',
                                'templateOptions' => [
                                    'label' => __('Border')
                                ]
                            ]
                        );

                            $border->addChildren(
                                'border_top_width',
                                'text',
                                [
                                    'key'             => $prefix . 'border_top_width',
                                    'className'       => 'mgz-design-top',
                                    'sortOrder'       => 10,
                                    'templateOptions' => [
                                        'label'       => __('Border Top Width'),
                                        'placeholder' => '-'
                                    ]
                                ]
                            );

                            $border->addChildren(
                                'border_right_width',
                                'text',
                                [
                                    'key'             => $prefix . 'border_right_width',
                                    'className'       => 'mgz-design-right',
                                    'sortOrder'       => 20,
                                    'templateOptions' => [
                                        'label'       => __('Border Right Width'),
                                        'placeholder' => '-'
                                    ],
                                    'expressionProperties' => [
                                        'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                    ]
                                ]
                            );

                            $border->addChildren(
                                'border_bottom_width',
                                'text',
                                [
                                    'key'             => $prefix . 'border_bottom_width',
                                    'className'       => 'mgz-design-bottom',
                                    'sortOrder'       => 30,
                                    'templateOptions' => [
                                        'label'       => __('Border Bottom Width'),
                                        'placeholder' => '-'
                                    ],
                                    'expressionProperties' => [
                                        'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                    ]
                                ]
                            );

                            $border->addChildren(
                                'border_left_width',
                                'text',
                                [
                                    'key'             => $prefix . 'border_left_width',
                                    'className'       => 'mgz-design-left',
                                    'sortOrder'       => 40,
                                    'templateOptions' => [
                                        'label'       => __('Border Left Width'),
                                        'placeholder' => '-'
                                    ],
                                    'expressionProperties' => [
                                        'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                    ]
                                ]
                            );

                            $padding = $border->addFieldset(
                                'padding',
                                [
                                    'sortOrder'       => 50,
                                    'templateOptions' => [
                                        'label' => __('Padding')
                                    ]
                                ]
                            );

                                $padding->addChildren(
                                    'padding_top',
                                    'text',
                                    [
                                        'key'             => $prefix . 'padding_top',
                                        'className'       => 'mgz-design-top',
                                        'sortOrder'       => 10,
                                        'templateOptions' => [
                                            'label'       => __('Padding Top'),
                                            'placeholder' => '-'
                                        ]
                                    ]
                                );

                                $padding->addChildren(
                                    'padding_right',
                                    'text',
                                    [
                                        'key'             => $prefix . 'padding_right',
                                        'className'       => 'mgz-design-right',
                                        'sortOrder'       => 20,
                                        'templateOptions' => [
                                            'label'       => __('Padding Right'),
                                            'placeholder' => '-'
                                        ],
                                        'expressionProperties' => [
                                            'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                        ]
                                    ]
                                );

                                $padding->addChildren(
                                    'padding_bottom',
                                    'text',
                                    [
                                        'key'             => $prefix . 'padding_bottom',
                                        'className'       => 'mgz-design-bottom',
                                        'sortOrder'       => 30,
                                        'templateOptions' => [
                                            'label'       => __('Padidng Bottom'),
                                            'placeholder' => '-'
                                        ],
                                        'expressionProperties' => [
                                            'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                        ]
                                    ]
                                );

                                $padding->addChildren(
                                    'padding_left',
                                    'text',
                                    [
                                        'key'             => $prefix . 'padding_left',
                                        'className'       => 'mgz-design-left',
                                        'sortOrder'       => 40,
                                        'templateOptions' => [
                                            'label'       => __('Padding Left'),
                                            'placeholder' => '-',
                                            'disabled'    => 'model.' . $prefix . 'simply'
                                        ],
                                        'expressionProperties' => [
                                            'templateOptions.disabled' => 'model.' . $prefix . 'simply'
                                        ]
                                    ]
                                );

                                $padding->addChildren(
                                    'padding_unit',
                                    'html',
                                    [
                                        'sortOrder'       => 50,
                                        'templateOptions' => [
                                            'content' => '<div class="mgz-design-logo"></div>'
                                        ]
                                    ]
                                );

        $container2 = $parent->addContainer(
            'container2',
            [
                'className' => 'mgz-design-styling',
                'sortOrder' => 20
            ]
        );

            if (!$this->getData('resizable')) {
                $container2->addChildren(
                    'align',
                    'select',
                    [
                        'key'             => $prefix . 'align',
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'label'   => __('Alignment'),
                            'options' => $this->getAlignOptions()
                        ]
                    ]
                );
            }

            $container2->addChildren(
                'border_color',
                'color',
                [
                    'key'             => $prefix . 'border_color',
                    'sortOrder'       => 20,
                    'templateOptions' => [
                        'label' => __('Border Color')
                    ]
                ]
            );

            $container2->addChildren(
                'border_style',
                'select',
                [
                    'key'             => $prefix . 'border_style',
                    'sortOrder'       => 30,
                    'templateOptions' => [
                        'label'       => __('Border Style'),
                        'options'     => $this->getBorderStyle(),
                        'placeholder' => __('Theme defaults')
                    ]
                ]
            );

            $container2->addChildren(
                'min_height',
                'text',
                [
                    'key'             => $prefix . 'min_height',
                    'sortOrder'       => 40,
                    'templateOptions' => [
                        'label' => __('Minimum Height')
                    ]
                ]
            );

            $container2->addChildren(
                'el_float',
                'select',
                [
                    'sortOrder'       => 50,
                    'key'             => $prefix . 'el_float',
                    'templateOptions' => [
                        'label'   => __('Float'),
                        'options' => $this->getFloatOptions()
                    ]
                ]
            );

        $container3 = $parent->addContainerGroup(
            'container3',
            [
                'sortOrder' => 40
            ]
        );

            $container3->addChildren(
                'background_type',
                'select',
                [
                    'key'             => $prefix . 'background_type',
                    'sortOrder'       => 10,
                    'defaultValue'    => 'image',
                    'templateOptions' => [
                        'label'   => __('Background Type'),
                        'options' => $this->getBackgroundType()
                    ]
                ]
            );

            $container3->addChildren(
                'background_color',
                'color',
                [
                    'key'             => $prefix . 'background_color',
                    'sortOrder'       => 20,
                    'templateOptions' => [
                        'label' => __('Background Color')
                    ]
                ]
            );

            $container3->addChildren(
                'custom_background_position',
                'text',
                [
                    'key'             => $prefix . 'custom_background_position',
                    'sortOrder'       => 30,
                    'templateOptions' => [
                        'label' => __('Background Position')
                    ],
                    'hideExpression' => '!model.' . $prefix . 'background_image||model.' . $prefix . 'background_position!="custom"'
                ]
            );

        $container4 = $parent->addContainerGroup(
            'container4',
            [
                'sortOrder' => 40
            ]
        );

            $container4->addChildren(
                'background_image',
                'image',
                [
                    'key'             => $prefix . 'background_image',
                    'sortOrder'       => 10,
                    'templateOptions' => [
                        'label' => __('Background Image')
                    ]
                ]
            );

            $container4->addChildren(
                'background_style',
                'select',
                [
                    'key'             => $prefix . 'background_style',
                    'sortOrder'       => 20,
                    'defaultValue'    => 'auto',
                    'templateOptions' => [
                        'label'   => __('Background Style'),
                        'options' => $this->getBackgroundStyle()
                    ],
                    'hideExpression' => '!model.' . $prefix . 'background_image'
                ]
            );

            $container4->addChildren(
                'background_position',
                'select',
                [
                    'key'             => $prefix . 'background_position',
                    'defaultValue'    => 'center-top',
                    'sortOrder'       => 30,
                    'templateOptions' => [
                        'label'   => __('Background Position'),
                        'options' => $this->getBackgroundPosition()
                    ],
                    'hideExpression' => '!model.' . $prefix . 'background_image'
                ]
            );

        $container5 = $parent->addContainerGroup(
            'container5',
            [
                'sortOrder'      => 50,
                'hideExpression' => 'model.' . $prefix . 'background_type!=="yt_vm_video"'
            ]
        );

            $container5->addChildren(
                'background_video',
                'text',
                [
                    'key'             => $prefix . 'background_video',
                    'sortOrder'       => 10,
                    'templateOptions' => [
                        'label' => __('YouTube / Vimeo'),
                        'note'  => __('Example: https://www.youtube.com/watch?v=lMJXxhRFO1k')
                    ]
                ]
            );

            $container5->addChildren(
                'video_volume',
                'number',
                [
                    'key'             => $prefix . 'video_volume',
                    'sortOrder'       => 20,
                    'className'       => 'mgz-width20',
                    'templateOptions' => [
                        'label' => __('Volume'),
                        'note'  => __('0 to 100'),
                        'min'   => 0,
                        'max'   => 100
                    ]
                ]
            );

        $container6 = $parent->addContainerGroup(
            'container6',
            [
                'sortOrder'      => 60,
                'hideExpression' => 'model.' . $prefix . 'background_type!=="yt_vm_video"'
            ]
        );

            $container6->addChildren(
                'video_start_time',
                'text',
                [
                    'key'             => $prefix . 'video_start_time',
                    'sortOrder'       => 10,
                    'templateOptions' => [
                        'label'   => __('Start Time'),
                        'tooltip' => __('Start time in seconds when video will be started (this value will be applied also after loop).')
                    ]
                ]
            );

            $container6->addChildren(
                'video_end_time',
                'text',
                [
                    'key'             => $prefix . 'video_end_time',
                    'sortOrder'       => 20,
                    'templateOptions' => [
                        'label'   => __('End Time'),
                        'tooltip' => __('End time in seconds when video will be ended.')
                    ]
                ]
            );

            $container6->addChildren(
                'video_mobile',
                'toggle',
                [
                    'key'             => $prefix . 'video_mobile',
                    'sortOrder'       => 30,
                    'templateOptions' => [
                        'label' => __('Enable on Mobile Devices')
                    ]
                ]
            );

        $container7 = $parent->addContainerGroup(
            'container7',
            [
                'sortOrder' => 80
            ]
        );

            $container7->addChildren(
                'parallax_type',
                'select',
                [
                    'key'             => 'parallax_type',
                    'sortOrder'       => 10,
                    'templateOptions' => [
                        'label'       => __('Parallax Type'),
                        'options'     => $this->getParallaxType(),
                        'placeholder' => __('None')
                    ]
                ]
            );

            $container7->addChildren(
                'parallax_speed',
                'number',
                [
                    'key'             => 'parallax_speed',
                    'sortOrder'       => 20,
                    'defaultValue'    => 0.5,
                    'templateOptions' => [
                        'label' => __('Parallax Speed'),
                        'max'   => '2',
                        'min'   => '-1',
                        'note'  => __('Number from -1.0 to 2.0')
                    ],
                    'hideExpression' => '!model.parallax_type'
                ]
            );

            $container7->addChildren(
                'parallax_mobile',
                'toggle',
                [
                    'key'             => 'parallax_mobile',
                    'sortOrder'       => 30,
                    'templateOptions' => [
                        'label' => __('Enable on Mobile Devices')
                    ],
                    'hideExpression' => '!model.parallax_type'
                ]
            );

        $container8 = $parent->addContainerGroup(
            'container8',
            [
                'sortOrder'      => 90,
                'hideExpression' => '!model.parallax_type'
            ]
        );

            $container8->addChildren(
                'mouse_parallax',
                'toggle',
                [
                    'key'             => 'mouse_parallax',
                    'sortOrder'       => 10,
                    'templateOptions' => [
                        'label' => __('Mouse Parallax')
                    ]
                ]
            );

            $container8->addChildren(
                'mouse_parallax_size',
                'number',
                [
                    'key'             => 'mouse_parallax_size',
                    'sortOrder'       => 20,
                    'defaultValue'    => 30,
                    'templateOptions' => [
                        'label' => __('Size(px)')
                    ],
                    'hideExpression' => '!model.mouse_parallax'
                ]
            );

            $container8->addChildren(
                'mouse_parallax_speed',
                'number',
                [
                    'key'             => 'mouse_parallax_speed',
                    'sortOrder'       => 30,
                    'defaultValue'    => 10000,
                    'templateOptions' => [
                        'label' => __('Speed(milliseconds)')
                    ],
                    'hideExpression' => '!model.mouse_parallax'
                ]
            );
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareResponsiveTab()
    {
        $sizes = $this->builderHelper->getResizableSizes();
        array_unshift($sizes, ['label' => 'Inherit from smaller', 'value' => '']);

        $responsive = $this->addTab(
            self::TAB_RESPONSIVE,
            [
                'sortOrder'       => 20,
                'className'       => 'mgz-column-responsive',
                'templateOptions' => [
                    'label' => __('Responsive Options')
                ]
            ]
        );

            $responsive->addChildren(
                'message',
                'html',
                [
                    'sortOrder'       => 0,
                    'templateOptions' => [
                        'content' => __('Find out more details about Responsiveness Control <a href="%1" target="_blank">here</a>', 'https://www.magezon.com/magezon-page-builder-responsiveness-control?utm_source=column_settings&utm_medium=element&utm_campaign=builder_extension')
                    ]
                ]
            );

            $responsive->addChildren(
                'md_size',
                'select',
                [
                    'key'             => 'md_size',
                    'sortOrder'       => 10,
                    'defaultValue'    => '',
                    'templateOptions' => [
                        'label'   => 'Width',
                        'options' => $sizes,
                        'note'    => __('Select column width.')
                    ]
                ]
            );

            $container1 = $responsive->addContainerGroup(
                'container1',
                [
                    'className'       => 'mgz-response-head',
                    'sortOrder'       => 20,
                    'templateOptions' => [
                        'label' => __('Responsiveness')
                    ]
                ]
            );

                $container1->addChildren(
                    'device',
                    'html',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'content' => __('Device')
                        ]
                    ]
                );

                $container1->addChildren(
                    'offset',
                    'html',
                    [
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'content' => __('Offset')
                        ]
                    ]
                );

                $container1->addChildren(
                    'width',
                    'html',
                    [
                        'sortOrder'       => 30,
                        'templateOptions' => [
                            'content' => __('Width')
                        ]
                    ]
                );

            $container2 = $responsive->addContainerGroup(
                'container2',
                [
                    'className' => 'mgz-response-xl',
                    'sortOrder' => 30
                ]
            );

                $container2->addChildren(
                    'xl',
                    'html',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'content' => '<i title="Desktop" class="mgz-icon mgz-icon-desktop"></i>'
                        ]
                    ]
                );

                $container2->addChildren(
                    'xl_offset_size',
                    'select',
                    [
                        'key'             => 'xl_offset_size',
                        'sortOrder'       => 20,
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'placeholder' => __('No offset'),
                            'options'     => $sizes
                        ]
                    ]
                );

                $container2->addChildren(
                    'xl_size',
                    'select',
                    [
                        'key'             => 'xl_size',
                        'sortOrder'       => 30,
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'options' => $sizes
                        ]
                    ]
                );

            $container3 = $responsive->addContainerGroup(
                'container3',
                [
                    'className' => 'mgz-response-lg',
                    'sortOrder' => 40
                ]
            );

                $container3->addChildren(
                    'lg',
                    'html',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'content' => '<i title="Tablet Landscape" class="mgz-icon mgz-icon-tablet-landscape"></i>'
                        ]
                    ]
                );

                $container3->addChildren(
                    'lg_offset_size',
                    'select',
                    [
                        'key'             => 'lg_offset_size',
                        'sortOrder'       => 20,
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'placeholder' => __('No offset'),
                            'options'     => $sizes
                        ]
                    ]
                );

                $container3->addChildren(
                    'lg_size',
                    'select',
                    [
                        'key'             => 'lg_size',
                        'sortOrder'       => 30,
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'options' => $sizes
                        ]
                    ]
                );

            $container4 = $responsive->addContainerGroup(
                'container4',
                [
                    'className' => 'mgz-response-md',
                    'sortOrder' => 50
                ]
            );

                $container4->addChildren(
                    'md',
                    'html',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'content' => '<i title="Tablet Portrait" class="mgz-icon mgz-icon-tablet-portrait"></i>'
                        ]
                    ]
                );

                $container4->addChildren(
                    'md_offset_size',
                    'select',
                    [
                        'key'             => 'md_offset_size',
                        'sortOrder'       => 20,
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'placeholder' => __('No offset'),
                            'options'     => $sizes
                        ]
                    ]
                );

                $container4->addChildren(
                    'html1',
                    'html',
                    [
                        'sortOrder'       => 30,
                        'templateOptions' => [
                            'content' => __('Default value from width attribute')
                        ]
                    ]
                );

            $container5 = $responsive->addContainerGroup(
                'container5',
                [
                    'className' => 'mgz-response-sm',
                    'sortOrder' => 60
                ]
            );

                $container5->addChildren(
                    'sm',
                    'html',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'content' => '<i title="Mobile Landscape" class="mgz-icon mgz-icon-mobile-landscape"></i>'
                        ]
                    ]
                );

                $container5->addChildren(
                    'sm_offset_size',
                    'select',
                    [
                        'key'             => 'sm_offset_size',
                        'sortOrder'       => 20,
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'placeholder' => __('No offset'),
                            'options'     => $sizes
                        ]
                    ]
                );

                $container5->addChildren(
                    'sm_size',
                    'select',
                    [
                        'key'             => 'sm_size',
                        'sortOrder'       => 30,
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'options' => $sizes
                        ]
                    ]
                );

            $container6 = $responsive->addContainerGroup(
                'container6',
                [
                    'className' => 'mgz-response-xs',
                    'sortOrder' => 70
                ]
            );

                $container6->addChildren(
                    'xs',
                    'html',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'content' => '<i title="Mobile Portrait" class="mgz-icon mgz-icon-mobile-portrait"></i>'
                        ]
                    ]
                );

                $container6->addChildren(
                    'xs_offset_size',
                    'select',
                    [
                        'key'             => 'xs_offset_size',
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'placeholder' => __('No offset'),
                            'options'     => $this->builderHelper->getResizableSizes()
                        ]
                    ]
                );

                $sizes1 = $this->builderHelper->getResizableSizes();
                array_unshift($sizes1, ['label' => ' ', 'value' => '']);

                $container6->addChildren(
                    'xs_size',
                    'select',
                    [
                        'key'             => 'xs_size',
                        'sortOrder'       => 30,
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'options' => $sizes1
                        ]
                    ]
                );

        $container1 = $responsive->addChildren(
            'html',
            'html',
            [
                'sortOrder'       => 80,
                'templateOptions' => [
                    'content' => '<p class="mgz__field-note">' . __('Adjust column for different screen sizes. Control width, offset and visibility settings.') . '</p>'
                ]
            ]
        );

        return $responsive;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareCarouselTab($sortOrder = 80)
    {
        $carousel = $this->addTab(
            'tab_carousel',
            [
                'sortOrder'       => $sortOrder,
                'templateOptions' => [
                    'label' => __('Carousel Options')
                ]
            ]
        );

            $colors = $carousel->addTab(
                'colors',
                [
                    'sortOrder'       => 10,
                    'templateOptions' => [
                        'label' => __('Colors')
                    ]
                ]
            );

                $normal = $colors->addContainerGroup(
                    'normal',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'label' => __('Normal')
                        ]
                    ]
                );

                    $color1 = $normal->addContainerGroup(
                        'color1',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color1->addChildren(
                            'color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'owl_color',
                                'templateOptions' => [
                                    'label' => __('Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'owl_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );

                $hover = $colors->addContainerGroup(
                    'hover',
                    [
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label' => __('Hover')
                        ]
                    ]
                );

                    $color1 = $hover->addContainerGroup(
                        'color1',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color1->addChildren(
                            'hover_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'owl_hover_color',
                                'templateOptions' => [
                                    'label' => __('Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'hover_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'owl_hover_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );

                $active = $colors->addContainerGroup(
                    'active',
                    [
                        'sortOrder'       => 30,
                        'templateOptions' => [
                            'label' => __('Active')
                        ]
                    ]
                );

                    $color1 = $active->addContainerGroup(
                        'color1',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color1->addChildren(
                            'active_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'owl_active_color',
                                'templateOptions' => [
                                    'label' => __('Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'active_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'owl_active_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );


            $container1 = $carousel->addContainerGroup(
                'container1',
                [
                    'sortOrder' => 20
                ]
            );

                $container1->addChildren(
                    'item_xl',
                    'number',
                    [
                        'key'             => 'owl_item_xl',
                        'sortOrder'       => 10,
                        'defaultValue'    => 5,
                        'templateOptions' => [
                            'label' => __('Desktop'),
                            'note'  => '> 1200px'
                        ]
                    ]
                );

                $container1->addChildren(
                    'item_lg',
                    'number',
                    [
                        'key'             => 'owl_item_lg',
                        'sortOrder'       => 20,
                        'defaultValue'    => 4,
                            'templateOptions' => [
                            'label' => __('Tablet Landscape'),
                            'note'  => '992px - 1199px'
                        ]
                    ]
                );

                $container1->addChildren(
                    'item_md',
                    'number',
                    [
                        'key'             => 'owl_item_md',
                        'sortOrder'       => 30,
                        'defaultValue'    => 3,
                        'templateOptions' => [
                            'label' => __('Tablet Portrait'),
                            'note'  => '768px - 991px'
                        ]
                    ]
                );

            $container2 = $carousel->addContainerGroup(
                'container2',
                [
                    'sortOrder' => 30
                ]
            );

                $container2->addChildren(
                    'item_sm',
                    'number',
                    [
                        'key'             => 'owl_item_sm',
                        'sortOrder'       => 10,
                        'defaultValue'    => 2,
                        'templateOptions' => [
                            'label' => __('Mobile Landscape'),
                            'note'  => '576px - 767px'
                        ]
                    ]
                );

                $container2->addChildren(
                    'item_xs',
                    'number',
                    [
                        'key'             => 'owl_item_xs',
                        'sortOrder'       => 20,
                        'defaultValue'    => 1,
                        'templateOptions' => [
                            'label' => __('Mobile Portrait'),
                            'note'  => '< 576px'
                        ]
                    ]
                );

                $container2->addChildren(
                    'margin',
                    'number',
                    [
                        'key'             => 'owl_margin',
                        'sortOrder'       => 30,
                        'defaultValue'    => 0,
                        'templateOptions' => [
                            'label' => __('Margin'),
                            'note'  => __('margin-right(px) on item.')
                        ]
                    ]
                );

            $container3 = $carousel->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 40
                ]
            );

                $container3->addChildren(
                    'nav',
                    'toggle',
                    [
                        'key'             => 'owl_nav',
                        'sortOrder'       => 10,
                        'defaultValue'    => false,
                        'templateOptions' => [
                            'label' => __('Navigation Buttons')
                        ]
                    ]
                );

                $container3->addChildren(
                    'nav_position',
                    'select',
                    [
                        'key'             => 'owl_nav_position',
                        'sortOrder'       => 20,
                        'defaultValue'    => 'center_split',
                        'templateOptions' => [
                            'label'   => __('Navigation Position'),
                            'options' => $this->getNavigationPosition()
                        ]
                    ]
                );

                $container3->addChildren(
                    'nav_size',
                    'select',
                    [
                        'key'             => 'owl_nav_size',
                        'sortOrder'       => 30,
                        'defaultValue'    => 'normal',
                        'templateOptions' => [
                            'label'   => __('Navigation Size'),
                            'options' => $this->getNavigationSize()
                        ]
                    ]
                );

            $container4 = $carousel->addContainerGroup(
                'container4',
                [
                    'sortOrder' => 50
                ]
            );

                $container4->addChildren(
                    'dots',
                    'toggle',
                    [
                        'key'             => 'owl_dots',
                        'sortOrder'       => 10,
                        'defaultValue'    => true,
                        'templateOptions' => [
                            'label' => __('Dots Navigation')
                        ]
                    ]
                );

                $container4->addChildren(
                    'lazyload',
                    'toggle',
                    [
                        'key'             => 'owl_lazyload',
                        'sortOrder'       => 20,
                        'defaultValue'    => true,
                        'templateOptions' => [
                            'label' => __('Lazyload')
                        ]
                    ]
                );

                $container4->addChildren(
                    'auto_height',
                    'toggle',
                    [
                        'key'             => 'owl_auto_height',
                        'sortOrder'       => 30,
                        'defaultValue'    => true,
                        'templateOptions' => [
                            'label' => __('Auto Height')
                        ]
                    ]
                );

            $container5 = $carousel->addContainerGroup(
                'container5',
                [
                    'sortOrder' => 60
                ]
            );

                $container5->addChildren(
                    'loop',
                    'toggle',
                    [
                        'key'             => 'owl_loop',
                        'sortOrder'       => 10,
                        'defaultValue'    => false,
                        'templateOptions' => [
                            'label' => __('Loop')
                        ]
                    ]
                );

                $container5->addChildren(
                    'center',
                    'toggle',
                    [
                        'key'             => 'owl_center',
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label' => __('Center')
                        ]
                    ]
                );

                $container5->addChildren(
                    'rtl',
                    'toggle',
                    [
                        'key'             => 'owl_rtl',
                        'sortOrder'       => 30,
                        'templateOptions' => [
                            'label' => __('Right To Left')
                        ]
                    ]
                );

            $container6 = $carousel->addContainerGroup(
                'container6',
                [
                    'sortOrder' => 70
                ]
            );

                $container6->addChildren(
                    'autoplay',
                    'toggle',
                    [
                        'key'             => 'owl_autoplay',
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'label' => __('Auto Play')
                        ]
                    ]
                );

                $container6->addChildren(
                    'autoplay_hover_pause',
                    'toggle',
                    [
                        'key'             => 'owl_autoplay_hover_pause',
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label' => __('Pause on Mouse Hover')
                        ]
                    ]
                );

                $container6->addChildren(
                    'autoplay_timeout',
                    'text',
                    [
                        'key'             => 'owl_autoplay_timeout',
                        'defaultValue'    => '5000',
                        'sortOrder'       => 30,
                        'templateOptions' => [
                            'label' => __('Auto Play Timeout')
                        ]
                    ]
                );

            $container7 = $carousel->addContainerGroup(
                'container7',
                [
                    'sortOrder' => 80
                ]
            );

                $container7->addChildren(
                    'owl_autoplay_speed',
                    'number',
                    [
                        'key'             => 'owl_autoplay_speed',
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'label' => __('Auto Play Speed')
                        ]
                    ]
                );

                $container7->addChildren(
                    'dots_speed',
                    'number',
                    [
                        'key'             => 'owl_dots_speed',
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label' => __('Dots Speed')
                        ]
                    ]
                );

                $container7->addChildren(
                    'stage_padding',
                    'number',
                    [
                        'key'             => 'owl_stage_padding',
                        'sortOrder'       => 30,
                        'defaultValue'    => 0,
                        'templateOptions' => [
                            'label' => __('Stage Padding')
                        ]
                    ]
                );

                $container7->addChildren(
                    'slide_by',
                    'number',
                    [
                        'key'             => 'owl_slide_by',
                        'sortOrder'       => 40,
                        'defaultValue'    => 1,
                        'templateOptions' => [
                            'label' => __('SlideBy')
                        ]
                    ]
                );

            // $carousel->addChildren(
            //     'animate_in',
            //     'select',
            //     [
            //         'sortOrder'       => 90,
            //         'key'             => 'owl_animate_in',
            //         'className'       => 'mgz-inner-widthauto',
            //         'templateOptions' => [
            //             'templateUrl' => 'Magezon_Builder/js/templates/form/element/animation-style.html',
            //             'element'     => 'Magezon_Builder/js/form/element/animation-in',
            //             'label'       => __('Animation In')
            //         ]
            //     ]
            // );

            // $carousel->addChildren(
            //     'animate_out',
            //     'select',
            //     [
            //         'sortOrder'       => 90,
            //         'key'             => 'owl_animate_out',
            //         'className'       => 'mgz-inner-widthauto',
            //         'templateOptions' => [
            //             'templateUrl' => 'Magezon_Builder/js/templates/form/element/animation-style.html',
            //             'element'     => 'Magezon_Builder/js/form/element/animation-out',
            //             'label'       => __('Animation Out')
            //         ]
            //     ]
            // );

        return $carousel;
    }


    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGridTab($sortOrder = 80)
    {
        $grid = $this->addTab(
            'tab_grid',
            [
                'sortOrder'       => $sortOrder,
                'templateOptions' => [
                    'label' => __('Grid Options')
                ]
            ]
        );

            $container1 = $grid->addContainerGroup(
                'container1',
                [
                    'sortOrder' => 10
                ]
            );

                $container1->addChildren(
                    'item_xl',
                    'select',
                    [
                        'key'             => 'item_xl',
                        'sortOrder'       => 10,
                        'defaultValue'    => 5,
                        'templateOptions' => [
                            'label'   => __('Desktop'),
                            'note'    => '> 1200px',
                            'options' => $this->getGridColumn()
                        ]
                    ]
                );

                $container1->addChildren(
                    'item_lg',
                    'select',
                    [
                        'key'             => 'item_lg',
                        'sortOrder'       => 20,
                        'defaultValue'    => 4,
                            'templateOptions' => [
                            'label'   => __('Tablet Landscape'),
                            'note'    => '992px - 1199px',
                            'options' => $this->getGridColumn()
                        ]
                    ]
                );

            $container2 = $grid->addContainerGroup(
                'container2',
                [
                    'sortOrder' => 20
                ]
            );

                $container2->addChildren(
                    'item_md',
                    'select',
                    [
                        'key'             => 'item_md',
                        'sortOrder'       => 10,
                        'defaultValue'    => 3,
                        'templateOptions' => [
                            'label'   => __('Tablet Portrait'),
                            'note'    => '768px - 991px',
                            'options' => $this->getGridColumn()
                        ]
                    ]
                );

                $container2->addChildren(
                    'item_sm',
                    'select',
                    [
                        'key'             => 'item_sm',
                        'sortOrder'       => 20,
                        'defaultValue'    => 2,
                        'templateOptions' => [
                            'label'   => __('Mobile Landscape'),
                            'note'    => '576px - 767px',
                            'options' => $this->getGridColumn()
                        ]
                    ]
                );

            $container3 = $grid->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 30
                ]
            );

                $container3->addChildren(
                    'item_xs',
                    'select',
                    [
                        'key'             => 'item_xs',
                        'sortOrder'       => 10,
                        'defaultValue'    => 2,
                        'className'       => 'mgz-width50',
                        'templateOptions' => [
                            'label'   => __('Mobile Portrait'),
                            'note'    => '< 576px',
                            'options' => $this->getGridColumn()
                        ]
                    ]
                );


        return $grid;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareButtonTab()
    {
        $button = $this->addTab(
            'tab_button',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Button')
                ]
            ]
        );

            $button->addChildren(
                'enable_button',
                'toggle',
                [
                    'sortOrder'       => 10,
                    'key'             => 'enable_button',
                    'templateOptions' => [
                        'label' => __('Enable Button')
                    ]
                ]
            );

            $button->addChildren(
                'button_title',
                'text',
                [
                    'sortOrder'       => 20,
                    'key'             => 'button_title',
                    'defaultValue'    => 'Text on the button',
                    'templateOptions' => [
                        'label' => __('Text')
                    ]
                ]
            );

            $button->addChildren(
                'button_link',
                'link',
                [
                    'sortOrder'       => 30,
                    'key'             => 'button_link',
                    'templateOptions' => [
                        'label' => __('Link')
                    ]
                ]
            );

            $container1 = $button->addContainerGroup(
                'container1',
                [
                    'sortOrder' => 40
                ]
            );

                $container1->addChildren(
                    'button_style',
                    'select',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'button_style',
                        'defaultValue'    => 'flat',
                        'templateOptions' => [
                            'label'   => __('Button Style'),
                            'options' => $this->getButtonStyle()
                        ]
                    ]
                );

                $container1->addChildren(
                    'button_size',
                    'select',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'button_size',
                        'defaultValue'    => 'md',
                        'templateOptions' => [
                            'label'   => __('Button Size'),
                            'options' => $this->getSizeList()
                        ]
                    ]
                );

                $container1->addChildren(
                    'full_width',
                    'toggle',
                    [
                        'sortOrder'       => 30,
                        'key'             => 'full_width',
                        'templateOptions' => [
                            'label' => __('Set Full Width Button')
                        ]
                    ]
                );


            $container2 = $button->addContainerGroup(
                'container2',
                [
                    'sortOrder'      => 50,
                    'hideExpression' => 'model.button_style!="gradient"'
                ]
            );

                $container2->addChildren(
                    'gradient_color_1',
                    'color',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'gradient_color_1',
                        'defaultValue'    => '#dd3333',
                        'templateOptions' => [
                            'label'       => __('Gradient Color 1')
                        ]
                    ]
                );

                $container2->addChildren(
                    'gradient_color_2',
                    'color',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'gradient_color_2',
                        'defaultValue'    => '#eeee22',
                        'templateOptions' => [
                            'label'       => __('Gradient Color 2')
                        ]
                    ]
                );


            $border1 = $button->addContainerGroup(
                'border1',
                [
                    'sortOrder' => 60
                ]
            );

                $border1->addChildren(
                    'button_border_width',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'button_border_width',
                        'templateOptions' => [
                            'label' => __('Border Width')
                        ]
                    ]
                );

                $border1->addChildren(
                    'button_border_radius',
                    'text',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'button_border_radius',
                        'templateOptions' => [
                            'label' => __('Border Radius')
                        ]
                    ]
                );

                $border1->addChildren(
                    'button_border_style',
                    'select',
                    [
                        'key'             => 'button_border_style',
                        'sortOrder'       => 30,
                        'defaultValue'    => 'solid',
                        'templateOptions' => [
                            'label'   => __('Border Style'),
                            'options' => $this->getBorderStyle()
                        ]
                    ]
                );

            $colors = $button->addTab(
                'colors',
                [
                    'sortOrder'       => 70,
                    'templateOptions' => [
                        'label' => __('Colors')
                    ]
                ]
            );

                $normal = $colors->addContainerGroup(
                    'normal',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'label' => __('Normal')
                        ]
                    ]
                );

                    $color1 = $normal->addContainerGroup(
                        'color1',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color1->addChildren(
                            'button_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'button_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'button_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'button_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'button_border_color',
                            'color',
                            [
                                'sortOrder'       => 30,
                                'key'             => 'button_border_color',
                                'templateOptions' => [
                                    'label' => __('Border Color')
                                ]
                            ]
                        );

                $hover = $colors->addContainerGroup(
                    'hover',
                    [
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label' => __('Hover')
                        ]
                    ]
                );

                    $color2 = $hover->addContainerGroup(
                        'color2',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color2->addChildren(
                            'button_hover_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'button_hover_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color2->addChildren(
                            'button_hover_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'button_hover_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );

                        $color2->addChildren(
                            'button_hover_border_color',
                            'color',
                            [
                                'sortOrder'       => 30,
                                'key'             => 'button_hover_border_color',
                                'templateOptions' => [
                                    'label' => __('Border Color')
                                ]
                            ]
                        );

            $button->addChildren(
                'button_css',
                'code',
                [
                    'sortOrder'       => 80,
                    'key'             => 'button_css',
                    'templateOptions' => [
                        'label' => __('Inline CSS')
                    ]
                ]
            );

        return $button;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareButtonDesignTab()
    {
        $design = $this->addTab(
            'button_design',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Button Design')
                ]
            ]
        );

            $container1 = $design->addContainerGroup(
                'container1',
                [
                    'sortOrder' => 10
                ]
            );

                $container1->addChildren(
                    'button_style',
                    'select',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'button_style',
                        'defaultValue'    => 'flat',
                        'templateOptions' => [
                            'label'   => __('Button Style'),
                            'options' => $this->getButtonStyle()
                        ]
                    ]
                );

                $container1->addChildren(
                    'button_size',
                    'select',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'button_size',
                        'defaultValue'    => 'md',
                        'templateOptions' => [
                            'label'   => __('Button Size'),
                            'options' => $this->getSizeList()
                        ]
                    ]
                );

                $container1->addChildren(
                    'full_width',
                    'toggle',
                    [
                        'sortOrder'       => 30,
                        'key'             => 'full_width',
                        'templateOptions' => [
                            'label' => __('Set Full Width Button')
                        ]
                    ]
                );

            $container2 = $design->addContainerGroup(
                'container2',
                [
                    'sortOrder'      => 20,
                    'hideExpression' => 'model.button_style!="gradient"'
                ]
            );

                $container2->addChildren(
                    'gradient_color_1',
                    'color',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'gradient_color_1',
                        'defaultValue'    => '#dd3333',
                        'templateOptions' => [
                            'label'       => __('Gradient Color 1')
                        ]
                    ]
                );

                $container2->addChildren(
                    'gradient_color_2',
                    'color',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'gradient_color_2',
                        'defaultValue'    => '#eeee22',
                        'templateOptions' => [
                            'label'       => __('Gradient Color 2')
                        ]
                    ]
                );

            $container3 = $design->addContainerGroup(
                'container3',
                [
                    'sortOrder'      => 20,
                    'hideExpression' => 'model.button_style!="3d"'
                ]
            );

                $container3->addChildren(
                    'box_shadow_color',
                    'color',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'box_shadow_color',
                        'defaultValue'    => '#cccccc',
                        'templateOptions' => [
                            'label'       => __('BoxShadow Color')
                        ]
                    ]
                );


            $border1 = $design->addContainerGroup(
                'border1',
                [
                    'sortOrder' => 30
                ]
            );

                $border1->addChildren(
                    'button_border_width',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'button_border_width',
                        'templateOptions' => [
                            'label' => __('Border Width')
                        ]
                    ]
                );

                $border1->addChildren(
                    'button_border_radius',
                    'text',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'button_border_radius',
                        'templateOptions' => [
                            'label' => __('Border Radius')
                        ]
                    ]
                );

                $border1->addChildren(
                    'button_border_style',
                    'select',
                    [
                        'key'             => 'button_border_style',
                        'sortOrder'       => 30,
                        'defaultValue'    => 'solid',
                        'templateOptions' => [
                            'label'   => __('Border Style'),
                            'options' => $this->getBorderStyle()
                        ]
                    ]
                );

            $colors = $design->addTab(
                'colors',
                [
                    'sortOrder'       => 40,
                    'templateOptions' => [
                        'label' => __('Colors')
                    ]
                ]
            );

                $normal = $colors->addContainerGroup(
                    'normal',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'label' => __('Normal')
                        ]
                    ]
                );

                    $color1 = $normal->addContainerGroup(
                        'color1',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color1->addChildren(
                            'button_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'button_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'button_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'button_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'button_border_color',
                            'color',
                            [
                                'sortOrder'       => 30,
                                'key'             => 'button_border_color',
                                'templateOptions' => [
                                    'label' => __('Border Color')
                                ]
                            ]
                        );

                $hover = $colors->addContainerGroup(
                    'hover',
                    [
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label' => __('Hover')
                        ]
                    ]
                );

                    $color2 = $hover->addContainerGroup(
                        'color2',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color2->addChildren(
                            'button_hover_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'button_hover_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color2->addChildren(
                            'button_hover_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'button_hover_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );

                        $color2->addChildren(
                            'button_hover_border_color',
                            'color',
                            [
                                'sortOrder'       => 30,
                                'key'             => 'button_hover_border_color',
                                'templateOptions' => [
                                    'label' => __('Border Color')
                                ]
                            ]
                        );

            $design->addChildren(
                'button_css',
                'code',
                [
                    'sortOrder'       => 50,
                    'key'             => 'button_css',
                    'templateOptions' => [
                        'label' => __('Inline CSS')
                    ]
                ]
            );

        return $design;
    }

    /**
     * @return array
     */
    public function getBorderStyle()
    {
        return [
            [
                'label' => __('Solid'),
                'value' => 'solid'
            ],
            [
                'label' => __('Dotted'),
                'value' => 'dotted'
            ],
            [
                'label' => __('Dashed'),
                'value' => 'dashed'
            ],
            [
                'label' => __('None'),
                'value' => 'none'
            ],
            [
                'label' => __('Hidden'),
                'value' => 'hidden'
            ],
            [
                'label' => __('Double'),
                'value' => 'double'
            ],
            [
                'label' => __('Groove'),
                'value' => 'groove'
            ],
            [
                'label' => __('Ridge'),
                'value' => 'ridge'
            ],
            [
                'label' => __('Inset'),
                'value' => 'inset'
            ],
            [
                'label' => __('Outset'),
                'value' => 'outset'
            ],
            [
                'label' => __('Initial'),
                'value' => 'initial'
            ],
            [
                'label' => __('Inherit'),
                'value' => 'inherit'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getBackgroundStyle()
    {
        return [
            [
                'label' => __('Auto'),
                'value' => 'auto'
            ],
            [
                'label' => __('Cover'),
                'value' => 'cover'
            ],
            [
                'label' => __('Contain'),
                'value' => 'contain'
            ],
            [
                'label' => __('Full width'),
                'value' => 'full-width'
            ],
            [
                'label' => __('Full Height'),
                'value' => 'full-height'
            ],
            [
                'label' => __('Repeat'),
                'value' => 'repeat'
            ],
            [
                'label' => __('Repeat horizontal'),
                'value' => 'repeat-x'
            ],
            [
                'label' => __('Repeat vertical'),
                'value' => 'repeat-y'
            ],
            [
                'label' => __('No Repeat'),
                'value' => 'no-repeat'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getAlignOptions()
    {
        return [
            [
                'label' => __('Left'),
                'value' => 'left'
            ],
            [
                'label' => __('Center'),
                'value' => 'center'
            ],
            [
                'label' => __('Right'),
                'value' => 'right'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getBackgroundType()
    {
        return [
            [
                'label' => __('Image'),
                'value' => 'image'
            ],
            // [
            //     'label' => __('Background Zoom'),
            //     'value' => 'background_zoom'
            // ],
            [
                'label' => __('YouTube / Vimeo'),
                'value' => 'yt_vm_video'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getBackgroundPosition()
    {
        return [
            [
                'label' => __('Left Top'),
                'value' => 'left-top'
            ],
            [
                'label' => __('Center Top'),
                'value' => 'center-top'
            ],
            [
                'label' => __('Right Top'),
                'value' => 'right-top'
            ],
            [
                'label' => __('Left Center'),
                'value' => 'left-center'
            ],
            [
                'label' => __('Center Center'),
                'value' => 'center-center'
            ],
            [
                'label' => __('Right Center'),
                'value' => 'right-center'
            ],
            [
                'label' => __('Left Bottom'),
                'value' => 'left-bottom'
            ],
            [
                'label' => __('Center Bottom'),
                'value' => 'center-bottom'
            ],
            [
                'label' => __('Right Bottom'),
                'value' => 'right-bottom'
            ],
            [
                'label' => __('Custom'),
                'value' => 'custom'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getParallaxType()
    {
        return [
            [
                'label' => __('Scroll'),
                'value' => 'scroll'
            ],
            [
                'label' => __('Scale'),
                'value' => 'scale'
            ],
            [
                'label' => __('Opacity'),
                'value' => 'opacity'
            ],
            [
                'label' => __('Opacity + Scroll'),
                'value' => 'scroll-opacity'
            ],
            [
                'label' => __('Opacity + Scale'),
                'value' => 'scale-opacity'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getHeadingType()
    {
        return [
            [
                'label' => 'Heading 1',
                'value' => 'h1'
            ],
            [
                'label' => 'Heading 2',
                'value' => 'h2'
            ],
            [
                'label' => 'Heading 3',
                'value' => 'h3'
            ],
            [
                'label' => 'Heading 4',
                'value' => 'h4'
            ],
            [
                'label' => 'Heading 5',
                'value' => 'h5'
            ],
            [
                'label' => 'Heading 6',
                'value' => 'h6'
            ]
        ];
    }

    public function getSizeList()
    {
        return [
            [
                'label' => __('Mini'),
                'value' => 'xs'
            ],
            [
                'label' => __('Small'),
                'value' => 'sm'
            ],
            [
                'label' => __('Normal'),
                'value' => 'md'
            ],
            [
                'label' => __('Large'),
                'value' => 'lg'
            ],
            [
                'label' => __('Extra Large'),
                'value' => 'xl'
            ]
        ];
    }

    /**
     * @param  int  $from   
     * @param  int  $to     
     * @param  boolean $prefix 
     * @return array          
     */
    public function getRange($from, $to, $step = 1, $prefix = false, $suffix = '')
    {
        $options = [];
        for ($i = $from; $i <= $to; $i) { 
            $label = $value = $i;
            if ($i < 10 && $prefix) {
                $label = '0' . $label;
            }
            $options[] = [
                'label' => $label . $suffix,
                'value' => $value
            ];
            $i+=$step;
        }
        return $options;
    }

    public function getGridColumn()
    {
        $options = [];
        $columns = [1, 2, 3, 4, 5, 6, 12];
        foreach ($columns as $i) {
            $value = $i;
            if ($i == 1) {
                $label = '1 column';
            } else {
                $label =  $i . ' columns';
            }
            $options[] = [
                'label' => $label,
                'value' => $value
            ];
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getResponsiveColumn()
    {
        return [
            [
                'label' => 12,
                'value' => 12
            ],
            [
                'label' => 6,
                'value' => 6
            ],
            [
                'label' => 5,
                'value' => 5
            ],
            [
                'label' => 4,
                'value' => 4
            ],
            [
                'label' => 3,
                'value' => 3
            ],
            [
                'label' => 2,
                'value' => 2
            ],
            [
                'label' => 1,
                'value' => 1
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPositionOptions()
    {
        return [
            [
                'label' => __('Top'),
                'value' => 'top'
            ],
            [
                'label' => __('Bottom'),
                'value' => 'bottom'
            ],
            [
                'label' => __('Left'),
                'value' => 'left'
            ],
            [
                'label' => __('Right'),
                'value' => 'right'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getLinkTarget()
    {
        return [
            [
                'label' => __('Same window'),
                'value' => '_self'
            ],
            [
                'label' => __('New window'),
                'value' => '_blank'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getShape()
    {
        return [
            [
                'label' => __('Rounded'),
                'value' => 'rounded'
            ],
            [
                'label' => __('Square'),
                'value' => 'square'
            ],
            [
                'label' => __('Round'),
                'value' => 'round'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getIconPosition()
    {
        return [
            [
                'label' => __('Left'),
                'value' => 'left'
            ],
            [
                'label' => __('Right'),
                'value' => 'right'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getDeviceType()
    {
        return [
            [
                'label' => __('All'),
                'value' => 'all'
            ],
            [
                'label' => __('Custom'),
                'value' => 'custom'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getNavigationPosition()
    {
        return [
            [
                'label' => __('Top Left'),
                'value' => 'top_left'
            ],
            [
                'label' => __('Top Right'),
                'value' => 'top_right'
            ],
            [
                'label' => __('Bottom Left'),
                'value' => 'bottom_left'
            ],
            [
                'label' => __('Bottom Right'),
                'value' => 'bottom_right'
            ],
            [
                'label' => __('Bottom Center'),
                'value' => 'bottom_center'
            ],
            [
                'label' => __('Center Split'),
                'value' => 'center_split'
            ],
            [
                'label' => __('Top Split'),
                'value' => 'top_split'
            ],
            [
                'label' => __('Bottom Split'),
                'value' => 'bottom_split'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getNavigationSize()
    {
        return [
            [
                'label' => __('Mini'),
                'value' => 'mini'
            ],
            [
                'label' => __('Small'),
                'value' => 'small'
            ],
            [
                'label' => __('Normal'),
                'value' => 'normal'
            ],
            [
                'label' => __('Large'),
                'value' => 'large'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getLinePosition()
    {
        return [
            [
                'label' => __('Center'),
                'value' => 'center'
            ],
            [
                'label' => __('Bottom'),
                'value' => 'bottom'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getHoverEffect()
    {
        return [
            [
                'label' => __('None'),
                'value' => ''
            ],
            [
                'label' => __('Zoom In'),
                'value' => 'zoomin'
            ],
            [
                'label' => __('Zoom Out'),
                'value' => 'zoomout'
            ]
        ];
    }

    public function getButtonStyle()
    {
        return [
            [
                'label' => __('Modern'),
                'value' => 'modern'
            ],
            [
                'label' => __('Flat'),
                'value' => 'flat'
            ],
            [
                'label' => __('Gradient'),
                'value' => 'gradient'
            ]
        ];
    }

    public function getContentPosition()
    {
        return [
            [
                'label' => __('Top Left'),
                'value' => 'top-left'
            ],
            [
                'label' => __('Top Center'),
                'value' => 'top-center'
            ],
            [
                'label' => __('Top Right'),
                'value' => 'top-right'
            ],
            [
                'label' => __('Middle Left'),
                'value' => 'middle-left'
            ],
            [
                'label' => __('Middle Center'),
                'value' => 'middle-center'
            ],
            [
                'label' => __('Middle Right'),
                'value' => 'middle-right'
            ],
            [
                'label' => __('Bottom Left'),
                'value' => 'bottom-left'
            ],
            [
                'label' => __('Bottom Center'),
                'value' => 'bottom-center'
            ],
            [
                'label' => __('Bottom Right'),
                'value' => 'bottom-right'
            ]
        ];
    }

    public function getFloatOptions()
    {
        return [
            [
                'label' => __('None'),
                'value' => 'none'
            ],
            [
                'label' => __('Left'),
                'value' => 'left'
            ],
            [
                'label' => __('Right'),
                'value' => 'right'
            ]
        ];
    }

    public function getOrientationOptions()
    {
        return [
            [
                'label' => __('Vertical'),
                'value' => 'vertical'
            ],
            [
                'label' => __('Horizontal'),
                'value' => 'horizontal'
            ]
        ];
    }
}