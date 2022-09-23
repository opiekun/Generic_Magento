<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_UiBuilder
 * @copyright Copyright (C) 2018 Magezon (https://www.magezon.com)
 */

namespace Magezon\UiBuilder\Ui\DataProvider\Form\Modifier;

use Magento\Ui\Component\Form\Fieldset;

class Styling extends \Magezon\UiBuilder\Ui\DataProvider\Form\AbstractModifier
{
    const GROUP_STYLING_NAME               = 'styling';
    const GROUP_STYLING_DEFAULT_SORT_ORDER = 300;
    const FIELD_CUSTOM_CLASS               = 'custom_class';
    const FIELD_CUSTOM_CSS                 = 'custom_css';
    const FIELD_SIMPLY                     = 'simply';
    const FIELD_MARGIN_TOP                 = 'margin_top';
    const FIELD_MARGIN_RIGHT               = 'margin_right';
    const FIELD_MARGIN_BOTTOM              = 'margin_bottom';
    const FIELD_MARGIN_LEFT                = 'margin_left';
    const FIELD_MARGIN_UNIT                = 'margin_unit';
    const FIELD_BORDER_TOP                 = 'border_top_width';
    const FIELD_BORDER_RIGHT               = 'border_right_width';
    const FIELD_BORDER_BOTTOM              = 'border_bottom_width';
    const FIELD_BORDER_LEFT                = 'border_left_width';
    const FIELD_BORDER_UNIT                = 'border_unit';
    const FIELD_PADDING_TOP                = 'padding_top';
    const FIELD_PADDING_RIGHT              = 'padding_right';
    const FIELD_PADDING_BOTTOM             = 'padding_bottom';
    const FIELD_PADDING_LEFT               = 'padding_left';
    const FIELD_PADDING_UNIT               = 'padding_unit';
    const FIELD_BORDER_COLOR               = 'border_color';
    const FIELD_BORDER_STYLE               = 'border_style';
    const FIELD_BACKGROUND_COLOR           = 'background_color';
    const FIELD_BACKGROUND_IMAGE           = 'background_image';
    const FIELD_BACKGROUND_POSITION        = 'background_position';
    const FIELD_CUSTOM_BACKGROUND_POSITION = 'custom_background_position';
    const FIELD_BACKGROUND_REPEAT          = 'background_repeat';
    const FIELD_BACKGROUND_SIZE            = 'background_size';
    const FIELD_CUSTOM_BACKGROUND_SIZE     = 'custom_background_size';
    const FIELD_BORDER_RADIUS_TOP_LEFT     = 'border_top_left_radius';
    const FIELD_BORDER_RADIUS_TOP_RIGHT    = 'border_top_right_radius';
    const FIELD_BORDER_RADIUS_BOTTOM_RIGHT = 'border_bottom_right_radius';
    const FIELD_BORDER_RADIUS_BOTTOM_LEFT  = 'border_bottom_left_radius';
    const FIELD_BORDER_RADIUS_UNIT         = 'border_radius_unit';

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->prepareChildren();

        $this->createStylingPanel();

        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if (isset($data['simply'])) {
            $data['simply'] = (int)$data['simply'];
        }
        return $data;
    }

    /**
     * Create Editor panel
     *
     * @return $this
     * @since 101.0.0
     */
    protected function createStylingPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_STYLING_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'                           => __('Styling'),
                                'componentType'                   => Fieldset::NAME,
                                'collapsible'                     => true,
                                'initializeFieldsetDataByDefault' => false,
                                'sortOrder'                       => static::GROUP_STYLING_DEFAULT_SORT_ORDER,
                                'additionalClasses'               => 'uibuilder-styling',
                                'template'                        => 'Magezon_UiBuilder/form/edit/styling',
                                'dataScope'                       => 'data'
                            ]
                        ]
                    ],
                    'children' => $this->getChildren()
                ]
            ]
        );
        return $this;
    }

    /**
     * @return \Magezon\UiBuilder\Data\Form\Element\Fieldset
     */
    public function prepareChildren()
    {
        $this->addChildren(
            self::FIELD_SIMPLY,
            'checkbox',
            [
                'sortOrder'   => 0,
                'displayArea' => 'simply',
                'component'   => 'Magezon_UiBuilder/js/element/design-simply',
                'links'       => [
                    self::FIELD_MARGIN_LEFT  => '${ $.provider }:${ $.parentScope }.' . self::FIELD_MARGIN_LEFT . ':changed',
                    self::FIELD_BORDER_LEFT  => '${ $.provider }:${ $.parentScope }.' . self::FIELD_BORDER_LEFT . ':changed',
                    self::FIELD_PADDING_LEFT => '${ $.provider }:${ $.parentScope }.' . self::FIELD_PADDING_LEFT . ':changed'
                ],
                'listens' => [
                    self::FIELD_MARGIN_LEFT  => 'onMarginChanged',
                    self::FIELD_BORDER_LEFT  => 'onBorderChanged',
                    self::FIELD_PADDING_LEFT => 'onPaddingChanged'
                ]
            ]
        );

        // MARGIN
        $this->addChildren(
            self::FIELD_MARGIN_TOP,
            'text',
            [
                'sortOrder'         => 10,
                'displayArea'       => 'margin',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-top',
                'imports' => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_MARGIN_RIGHT,
            'text',
            [
                'sortOrder'         => 20,
                'displayArea'       => 'margin',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-right',
                'imports'           => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_MARGIN_BOTTOM,
            'text',
            [
                'sortOrder'         => 30,
                'displayArea'       => 'margin',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-bottom',
                'imports' => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_MARGIN_LEFT,
            'text',
            [
                'sortOrder'         => 40,
                'displayArea'       => 'margin',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-left'
            ]
        );

        // $this->addChildren(
        //     self::FIELD_MARGIN_UNIT,
        //     'select',
        //     [
        //         'sortOrder'            => 50,
        //         'displayArea'          => 'margin-unit',
        //         'additionalClasses'    => 'uibuilder-design-margin-unit',
        //         'options'              => $this->getUnit(),
        //         'selectedPlaceholders' => false,
        //         'value'              => 'px'
        //     ]
        // );

        // BORDER
        $this->addChildren(
            self::FIELD_BORDER_TOP,
            'text',
            [
                'sortOrder'         => 10,
                'displayArea'       => 'border',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-top',
                'imports' => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_BORDER_RIGHT,
            'text',
            [
                'sortOrder'         => 20,
                'displayArea'       => 'border',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-right',
                'imports' => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_BORDER_BOTTOM,
            'text',
            [
                'sortOrder'         => 30,
                'displayArea'       => 'border',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-bottom',
                'imports' => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_BORDER_LEFT,
            'text',
            [
                'sortOrder'         => 40,
                'displayArea'       => 'border',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-left'
            ]
        );

        // $this->addChildren(
        //     self::FIELD_BORDER_UNIT,
        //     'select',
        //     [
        //         'sortOrder'            => 50,
        //         'displayArea'          => 'border-unit',
        //         'additionalClasses'    => 'uibuilder-design-border-unit',
        //         'options'              => $this->getBorderUnit(),
        //         'selectedPlaceholders' => false,
        //         'value'              => 'px'
        //     ]
        // );

        // PADDING
        $this->addChildren(
            self::FIELD_PADDING_TOP,
            'text',
            [
                'sortOrder'         => 10,
                'displayArea'       => 'padding',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-top',
                'imports' => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_PADDING_RIGHT,
            'text',
            [
                'sortOrder'         => 20,
                'displayArea'       => 'padding',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-right',
                'imports' => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_PADDING_BOTTOM,
            'text',
            [
                'sortOrder'         => 30,
                'displayArea'       => 'padding',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-bottom',
                'imports' => [
                    'visible' => '!${ $.provider }:${ $.parentScope }.' . self::FIELD_SIMPLY
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_PADDING_LEFT,
            'text',
            [
                'sortOrder'         => 40,
                'displayArea'       => 'padding',
                'placeholder'       => '-',
                'additionalClasses' => 'uibuilder-design-left'
            ]
        );

        $this->addChildren(
            self::FIELD_PADDING_UNIT,
            'select',
            [
                'sortOrder'            => 50,
                'displayArea'          => 'padding-unit',
                'additionalClasses'    => 'uibuilder-design-padding-unit',
                'options'              => $this->getUnit(),
                'selectedPlaceholders' => false,
                'value'                => 'px'
            ]
        );

        $this->addChildren(
            self::FIELD_BORDER_COLOR,
            'color',
            [
                'label'       => __('Border Color'),
                'sortOrder'   => 20,
                'displayArea' => 'right'
            ]
        );

        $this->addChildren(
            self::FIELD_BORDER_STYLE,
            'select',
            [
                'label'       => __('Border Style'),
                'sortOrder'   => 30,
                'displayArea' => 'right',
                'options'     => $this->getBorderStyle(),
                'placeholder' => __('Theme Default')
            ]
        );

        $this->addChildren(
            self::FIELD_BACKGROUND_COLOR,
            'color',
            [
                'label'       => __('Background Color'),
                'sortOrder'   => 40,
                'displayArea' => 'right',
                'rgb'         => true
            ]
        );

        $this->addChildren(
            self::FIELD_BACKGROUND_IMAGE,
            'image',
            [
                'label'        => __('Background Image'),
                'sortOrder'    => 50,
                'displayArea'  => 'right',
                'labelVisible' => false
            ]
        );

        $this->addChildren(
            self::FIELD_BACKGROUND_POSITION,
            'select',
            [
                'label'        => __('Background Position'),
                'sortOrder'    => 60,
                'displayArea'  => 'right',
                'options'      => $this->getBackgroundPositionOptions(),
                'placeholder'  => __('Default'),
                'groupsConfig' => [
                    'custom' => [
                        self::FIELD_CUSTOM_BACKGROUND_POSITION
                    ]
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_CUSTOM_BACKGROUND_POSITION,
            'text',
            [
                'label'       => __('Custom Background Position'),
                'sortOrder'   => 70,
                'displayArea' => 'right'
            ]
        );

        $this->addChildren(
            self::FIELD_BACKGROUND_REPEAT,
            'select',
            [
                'label'       => __('Background Repeat'),
                'sortOrder'   => 80,
                'options'     => $this->getBackgroundRepeatOptions(),
                'displayArea' => 'right',
                'placeholder' => __('Default')
            ]
        );

        $this->addChildren(
            self::FIELD_BACKGROUND_SIZE,
            'select',
            [
                'label'        => __('Background Size'),
                'sortOrder'    => 90,
                'displayArea'  => 'right',
                'options'      => $this->getBackgroundSizeOptions(),
                'placeholder'  => __('Default'),
                'groupsConfig' => [
                    'custom' => [
                        self::FIELD_CUSTOM_BACKGROUND_SIZE
                    ]
                ]
            ]
        );

        $this->addChildren(
            self::FIELD_CUSTOM_BACKGROUND_SIZE,
            'text',
            [
                'label'       => __('Custom Background Size'),
                'sortOrder'   => 100,
                'displayArea' => 'right'
            ]
        );

        // BORDER RADIUS
        $this->addChildren(
            self::FIELD_BORDER_RADIUS_TOP_LEFT,
            'text',
            [
                'sortOrder'         => 10,
                'displayArea'       => 'borderRadius',
                'additionalClasses' => 'uibuilder-design-top uibuilder-design-border-radius-top',
                'placeholder'       => '-',
                'notice'            => __('Border Radius')
            ]
        );

        $this->addChildren(
            self::FIELD_BORDER_RADIUS_TOP_RIGHT,
            'text',
            [
                'sortOrder'         => 20,
                'displayArea'       => 'borderRadius',
                'additionalClasses' => 'uibuilder-design-right',
                'placeholder'       => '-'
            ]
        );

        $this->addChildren(
            self::FIELD_BORDER_RADIUS_BOTTOM_RIGHT,
            'text',
            [
                'sortOrder'         => 30,
                'displayArea'       => 'borderRadius',
                'additionalClasses' => 'uibuilder-design-bottom',
                'placeholder'       => '-'
            ]
        );

        $this->addChildren(
            self::FIELD_BORDER_RADIUS_BOTTOM_LEFT,
            'text',
            [
                'sortOrder'         => 40,
                'displayArea'       => 'borderRadius',
                'additionalClasses' => 'uibuilder-design-left',
                'placeholder'       => '-'
            ]
        );

        // $this->addChildren(
        //     self::FIELD_BORDER_RADIUS_UNIT,
        //     'select',
        //     [
        //         'sortOrder'            => 50,
        //         'displayArea'          => 'borderRadius',
        //         'additionalClasses'    => 'uibuilder-design-border-radius-unit',
        //         'options'              => $this->getUnit(),
        //         'selectedPlaceholders' => false,
        //         'value'                => 'px'
        //     ]
        // );

        $container1 = $this->addContainerGroup(
            'container1',
            [
                'label'             => __('Box Shadow'),
                'sortOrder'         => 10,
                'additionalClasses' => 'uibuilder-styling-boxshadow',
                'displayArea'       => 'footer'
            ]
        );

            $container1->addChildren(
                'boxshadow',
                'boolean',
                [
                    'sortOrder' => 10,
                    'required'  => true
                ]
            );

            $container1->addChildren(
                'boxshadow_color',
                'color',
                [
                    'sortOrder' => 20,
                    'required'  => true,
                    'label'     => __('Color'),
                    'value'     => 'rgba(0, 0, 0, 0.2)',
                    'rgb'       => true,
                    'imports'   => [
                        'visible' => '${ $.provider }:${ $.parentScope }.boxshadow'
                    ]
                ]
            );

            $container1->addChildren(
                'boxshadow_horizontal',
                'number',
                [
                    'sortOrder' => 30,
                    'required'  => true,
                    'label'     => __('Horizontal'),
                    'value'     => 1,
                    'imports'   => [
                        'visible' => '${ $.provider }:${ $.parentScope }.boxshadow'
                    ]
                ]
            );

            $container1->addChildren(
                'boxshadow_vertical',
                'number',
                [
                    'sortOrder' => 40,
                    'required'  => true,
                    'value'     => 8,
                    'label'     => __('Vertical'),
                    'imports'   => [
                        'visible' => '${ $.provider }:${ $.parentScope }.boxshadow'
                    ]
                ]
            );

            $container1->addChildren(
                'boxshadow_blur',
                'number',
                [
                    'sortOrder' => 50,
                    'required'  => true,
                    'value'     => 23,
                    'label'     => __('Blur'),
                    'imports'   => [
                        'visible' => '${ $.provider }:${ $.parentScope }.boxshadow'
                    ]
                ]
            );

            $container1->addChildren(
                'boxshadow_spread',
                'number',
                [
                    'sortOrder'    => 60,
                    'required'     => true,
                    'value'        => 4,
                    'label'        => __('Spread'),
                    'imports'      => [
                        'visible' => '${ $.provider }:${ $.parentScope }.boxshadow'
                    ]
                ]
            );

            $container1->addChildren(
                'boxshadow_position',
                'select',
                [
                    'sortOrder' => 70,
                    'required'  => true,
                    'label'     => __('Position'),
                    'value'     => 'outline',
                    'options'   => $this->getPositionOptions(),
                    'imports'   => [
                        'visible' => '${ $.provider }:${ $.parentScope }.boxshadow'
                    ]
                ]
            );

        $this->addChildren(
            static::FIELD_CUSTOM_CLASS,
            'text',
            [
                'label'             => __('Custom Class'),
                'sortOrder'         => 20,
                'additionalClasses' => 'uibuilder-custom-class',
                'displayArea'       => 'footer'
            ]
        );

        $this->addChildren(
            static::FIELD_CUSTOM_CSS,
            'code',
            [
                'label'             => __('Custom CSS'),
                'sortOrder'         => 30,
                'rows'              => 12,
                'additionalClasses' => 'uibuilder-custom-css',
                'displayArea'       => 'footer'
            ]
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getUnit()
    {
        return [
            [
                'label' => 'px',
                'value' => 'px'
            ],
            [
                'label' => 'em',
                'value' => 'em'
            ],
            [
                'label' => '%',
                'value' => '%'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getBorderUnit()
    {
        return [
            [
                'label' => 'px',
                'value' => 'px'
            ],
            [
                'label' => 'em',
                'value' => 'em'
            ]
        ];
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
    public function getBackgroundPositionOptions()
    {
        return [
            [
                'label' => __('Top Left'),
                'value' => 'top left'
            ],
            [
                'label' => __('Top Center'),
                'value' => 'top center'
            ],
            [
                'label' => __('Top Right'),
                'value' => 'top right'
            ],
            [
                'label' => __('Center Left'),
                'value' => 'center left'
            ],
            [
                'label' => __('Center Center'),
                'value' => 'center center'
            ],
            [
                'label' => __('Center Right'),
                'value' => 'center right'
            ],
            [
                'label' => __('Bottom Left'),
                'value' => 'bottom left'
            ],
            [
                'label' => __('Bottom Center'),
                'value' => 'bottom center'
            ],
            [
                'label' => __('Bottom Right'),
                'value' => 'bottom right'
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
    public function getBackgroundRepeatOptions()
    {
        return [
            [
                'label' => __('No-repeat'),
                'value' => 'no-repeat'
            ],
            [
                'label' => __('Repeat'),
                'value' => 'repeat'
            ],
            [
                'label' => __('Repeat-x'),
                'value' => 'repeat-x'
            ],
            [
                'label' => __('Repeat-y'),
                'value' => 'repeat-y'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getBackgroundSizeOptions()
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
                'label' => __('Custom'),
                'value' => 'custom'
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
                'label' => __('Inset'),
                'value' => 'inset'
            ],
            [
                'label' => __('Outline'),
                'value' => 'outline'
            ]
        ];
    }
}
