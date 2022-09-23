<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Data\Element;

class Item extends \Magezon\Builder\Data\Element\AbstractElement
{
    /**
     * Prepare modal components
     */
    public function prepareForm()
    {
    	parent::prepareForm();
    	$this->prepareItemDesignTab();
        $this->prepareIconTab();
    	$this->prepareSubmenuTab();
    	return $this;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

    		$container1 = $general->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
		    );

		    	$container1->addChildren(
		            'title',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'title',
						'defaultValue'    => 'Item',
						'templateOptions' => [
							'label' => __('Title')
		                ],
                        'expressionProperties' => [
                            'templateOptions.disabled' => 'model.item_type=="category"&&model.cat_name'
                        ]
		            ]
		        );

		    	$container1->addChildren(
		            'sub_title',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'sub_title',
						'templateOptions' => [
							'label' => __('Sub Title')
		                ]
		            ]
		        );

            $container2 = $general->addContainerGroup(
                'container2',
                [
                    'sortOrder' => 20
                ]
            );

    	    	$container2->addChildren(
    	            'item_type',
    	            'select',
    	            [
    					'sortOrder'       => 10,
    					'key'             => 'item_type',
    					'defaultValue'    => 'custom',
    					'templateOptions' => [
    						'label'   => __('Type'),
    						'options' => $this->getItemType()
    	                ]
    	            ]
    	        );

                $container2->addChildren(
                    'cat_name',
                    'toggle',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'cat_name',
                        'templateOptions' => [
                            'label' => __('Use Category Name')
                        ],
                        'hideExpression' => 'model.item_type!="category"'
                    ]
                );

	    	$general->addChildren(
	            'category_id',
	            'uiSelect',
	            [
					'sortOrder'       => 30,
					'key'             => 'category_id',
					'templateOptions' => [
                        'label'       => __('Category'),
                        'source'      => 'category',
                        'showValue'   => true,
                        'placeholder' => __('Search category by name')
	                ],
	                'hideExpression' => 'model.item_type!="category"'
	            ]
	        );

	    	$general->addChildren(
	            'page_id',
	            'uiSelect',
	            [
					'sortOrder'       => 30,
					'key'             => 'page_id',
					'templateOptions' => [
                        'label'       => __('Page'),
                        'source'      => 'page',
                        'showValue'   => true,
                        'placeholder' => __('Search page by name')
	                ],
	                'hideExpression' => 'model.item_type!="page"'
	            ]
	        );

	    	$general->addChildren(
	            'product_id',
	            'uiSelect',
	            [
					'sortOrder'       => 30,
					'key'             => 'product_id',
					'templateOptions' => [
                        'label'       => __('Product'),
                        'source'      => 'product',
                        'showValue'   => true,
                        'placeholder' => __('Search product by name')
	                ],
	                'hideExpression' => 'model.item_type!="product"'
	            ]
	        );

	    	$general->addChildren(
	            'custom_link',
	            'text',
	            [
					'sortOrder'       => 30,
					'key'             => 'custom_link',
					'templateOptions' => [
						'label' => __('Custom Link')
	                ],
	                'hideExpression' => 'model.item_type!="custom"'
	            ]
	        );

            $container3 = $general->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 40
                ]
            );

                $container3->addChildren(
                    'label',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'label',
                        'templateOptions' => [
                            'label' => __('Label')
                        ]
                    ]
                );

                $container3->addChildren(
                    'label_position',
                    'select',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'label_position',
                        'defaultValue'    => 'top_right',
                        'templateOptions' => [
                            'label'   => __('Label Position'),
                            'options' => $this->getLabelPosition()
                        ]
                    ]
                );

            $container4 = $general->addContainerGroup(
                'container4',
                [
                    'sortOrder' => 50
                ]
            );

                $container4->addChildren(
                    'caret',
                    'icon',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'caret',
                        'defaultValue'    => 'fas mgz-fa-angle-down',
                        'templateOptions' => [
                            'label' => __('Caret')
                        ]
                    ]
                );

                $container4->addChildren(
                    'caret_hover',
                    'icon',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'caret_hover',
                        'defaultValue'    => 'fas mgz-fa-angle-up',
                        'templateOptions' => [
                            'label' => __('Caret on Hover')
                        ]
                    ]
                );

            $container5 = $general->addContainerGroup(
                'container5',
                [
                    'sortOrder' => 60
                ]
            );

                $container5->addChildren(
                    'hide_on_mobile',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'hide_on_mobile',
                        'templateOptions' => [
                            'label' => __('Hide below breakpoint')
                        ]
                    ]
                );

                $container5->addChildren(
                    'hide_on_desktop',
                    'toggle',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'hide_on_desktop',
                        'templateOptions' => [
                            'label' => __('Hide above breakpoint')
                        ]
                    ]
                );

            $container6 = $general->addContainerGroup(
                'container6',
                [
                    'sortOrder' => 70
                ]
            );

                $container6->addChildren(
                    'nofollow',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'nofollow',
                        'templateOptions' => [
                            'label' => __('Add nofollow option to link')
                        ]
                    ]
                );

                $container6->addChildren(
                    'new_tab',
                    'toggle',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'new_tab',
                        'templateOptions' => [
                            'label' => __('Open New Tab')
                        ]
                    ]
                );

            $general->addChildren(
                'hide_on_sticky',
                'toggle',
                [
                    'sortOrder'       => 80,
                    'key'             => 'hide_on_sticky',
                    'templateOptions' => [
                        'label' => __('Hide on Sticky')
                    ]
                ]
            );

            $container7 = $general->addContainerGroup(
                'container7',
                [
                    'sortOrder' => 90
                ]
            );

                $container7->addChildren(
                    'scrollto',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'scrollto',
                        'templateOptions' => [
                            'label' => __('Scroll To'),
                            'note'  => __('The selector for an item to scroll to when clicked, if present. Example: #section-1')
                        ]
                    ]
                );

                $container7->addChildren(
                    'item_align',
                    'select',
                    [
                        'key'             => 'item_align',
                        'sortOrder'       => 20,
                        'defaultValue'    => 'left',
                        'templateOptions' => [
                            'label'   => __('Alignment'),
                            'options' => $this->getAlignOptions()
                        ]
                    ]
                );

    	return $general;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareItemDesignTab()
    {
    	$design = $this->addTab(
            'item_design',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Style')
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
                    'item_padding',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'item_padding',
                        'templateOptions' => [
                            'label'       => __('Item Padding'),
                            'placeholder' => '0 15px'
                        ]
                    ]
                );

                $container1->addChildren(
                    'item_font_size',
                    'text',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'item_font_size',
                        'templateOptions' => [
                            'label' => __('Item Font Size')
                        ]
                    ]
                );

                $container1->addChildren(
                    'item_font_weight',
                    'text',
                    [
                        'sortOrder'       => 30,
                        'key'             => 'item_font_weight',
                        'templateOptions' => [
                            'label' => __('Item Font Weight')
                        ]
                    ]
                );

            $colors = $design->addTab(
                'title_colors',
                [
                    'sortOrder'       => 20,
                    'templateOptions' => [
                        'label' => __('Item Colors')
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
                            'title_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'title_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'title_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'title_background_color',
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

                    $color2 = $hover->addContainerGroup(
                        'color2',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color2->addChildren(
                            'section_hover_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'title_hover_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color2->addChildren(
                            'title_hover_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'title_hover_background_color',
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

                    $color3 = $active->addContainerGroup(
                        'color3',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color3->addChildren(
                            'title_active_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'title_active_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color3->addChildren(
                            'title_active_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'title_active_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );

            $colors = $design->addTab(
                'label_colors',
                [
                    'sortOrder'       => 30,
                    'templateOptions' => [
                        'label' => __('Label Colors')
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
                            'label_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'label_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color1->addChildren(
                            'label_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'label_background_color',
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

                    $color2 = $hover->addContainerGroup(
                        'color2',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color2->addChildren(
                            'label_hover_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'label_hover_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color2->addChildren(
                            'label_hover_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'label_hover_background_color',
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

                    $color3 = $active->addContainerGroup(
                        'color3',
                        [
                            'sortOrder' => 10
                        ]
                    );

                        $color3->addChildren(
                            'label_active_color',
                            'color',
                            [
                                'sortOrder'       => 10,
                                'key'             => 'label_active_color',
                                'templateOptions' => [
                                    'label' => __('Text Color')
                                ]
                            ]
                        );

                        $color3->addChildren(
                            'label_active_background_color',
                            'color',
                            [
                                'sortOrder'       => 20,
                                'key'             => 'label_active_background_color',
                                'templateOptions' => [
                                    'label' => __('Background Color')
                                ]
                            ]
                        );

            $design->addChildren(
                'item_inline_css',
                'code',
                [
                    'sortOrder'       => 40,
                    'key'             => 'item_inline_css',
                    'templateOptions' => [
                        'label' => __('Item Inline CSS')
                    ]
                ]
            );

    	return $design;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareIconTab()
    {
        $icon = $this->addTab(
            'item_icon',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Icon')
                ]
            ]
        );
            $container1 = $icon->addContainerGroup(
                'container1',
                [
                    'sortOrder' => 10
                ]
            );

                $container1->addChildren(
                    'show_icon',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'show_icon',
                        'templateOptions' => [
                            'label' => __('Show Icon')
                        ]
                    ]
                );

                $container1->addChildren(
                    'icon_position',
                    'select',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'icon_position',
                        'defaultValue'    => 'left',
                        'templateOptions' => [
                            'label'   => __('Position'),
                            'options' => $this->getIconPosition()
                        ],
                        'hideExpression' => '!model.show_icon'
                    ]
                );

            $container2 = $icon->addContainerGroup(
                'container2',
                [
                    'sortOrder'      => 20,
                    'hideExpression' => '!model.show_icon'
                ]
            );

                $container2->addChildren(
                    'icon',
                    'icon',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'icon',
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'label' => __('Icon')
                        ]
                    ]
                );

                $container2->addChildren(
                    'icon_hover',
                    'icon',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'icon_hover',
                        'defaultValue'    => '',
                        'templateOptions' => [
                            'label' => __('Icon on Hover')
                        ]
                    ]
                );

            $container3 = $icon->addContainerGroup(
                'container3',
                [
                    'sortOrder'      => 30,
                    'hideExpression' => '!model.show_icon'
                ]
            );

                $container3->addChildren(
                    'icon_color',
                    'color',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'icon_color',
                        'templateOptions' => [
                            'label' => __('Icon Color')
                        ]
                    ]
                );

                $container3->addChildren(
                    'icon_classes',
                    'text',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'icon_classes',
                        'templateOptions' => [
                            'label' => __('Custom Classes')
                        ]
                    ]
                );

        return $icon;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareSubmenuTab()
    {
    	$submenu = $this->addTab(
            'item_submenu',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Submenu')
                ]
            ]
        );

    		$container1 = $submenu->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
		    );

		    	$container1->addChildren(
		            'submenu_type',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'submenu_type',
						'defaultValue'    => 'mega',
						'templateOptions' => [
							'label'   => __('Type'),
							'options' => $this->getSubmenuType()
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'submenu_position',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'submenu_position',
						'defaultValue'    => 'left_edge_parent_item',
						'templateOptions' => [
							'label'   => __('Position'),
							'options' => $this->getMegaSubmenuPosition()
		                ],
                        'hideExpression' => 'model.submenu_type!="mega"'
		            ]
		        );

		    	$container1->addChildren(
		            'submenu_width',
		            'text',
		            [
                        'sortOrder'       => 30,
                        'key'             => 'submenu_width',
                        'templateOptions' => [
							'label' => __('Width')
		                ]
		            ]
		        );

            $container2 = $submenu->addContainerGroup(
                'container2',
                [
                    'sortOrder' => 20
                ]
            );

                $container2->addChildren(
                    'parent_id',
                    'uiSelect',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'parent_id',
                        'templateOptions' => [
                            'label'       => __('Auto List Sub Categories'),
                            'source'      => 'category',
                            'showValue'   => true,
                            'placeholder' => __('Search parent by name')
                        ]
                    ]
                );

                $container2->addChildren(
                    'submenu_desktop_padding',
                    'text',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'submenu_desktop_padding',
                        'templateOptions' => [
                            'label' => __('Submenu Desktop Padding')
                        ]
                    ]
                );

                $container2->addChildren(
                    'submenu_mobile_padding',
                    'text',
                    [
                        'sortOrder'       => 30,
                        'key'             => 'submenu_mobile_padding',
                        'templateOptions' => [
                            'label' => __('Submenu Mobile Padding')
                        ]
                    ]
                );

	    	$submenu->addChildren(
                'subcategories_col',
                'select',
                [
                    'sortOrder'       => 30,
                    'key'             => 'subcategories_col',
                    'defaultValue'    => 3,
                    'templateOptions' => [
                        'label'   => __('Sub Categories Column'),
                        'options' => [
                            [
                                'label' => 1,
                                'value' => 1
                            ],
                            [
                                'label' => 2,
                                'value' => 2
                            ],
                            [
                                'label' => 3,
                                'value' => 3
                            ],
                            [
                                'label' => 4,
                                'value' => 4
                            ],
                            [
                                'label' => 5,
                                'value' => 5
                            ],
                            [
                                'label' => 6,
                                'value' => 6
                            ]
                        ]
                    ],
                    'hideExpression' => '!model.parent_id'
                ]
            );

            $container3 = $submenu->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 40
                ]
            );

                $container3->addChildren(
                    'submenu_fullwidth',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'submenu_fullwidth',
                        'defaultValue'    => 0,
                        'templateOptions' => [
                            'label' => __('Full width Submenu'),
                            'note'  => __('This option is only applied on the Top Navigation')
                        ]
                    ]
                );

            $submenu->addChildren(
                'submenu_animate_in',
                'select',
                [
                    'sortOrder'       => 50,
                    'key'             => 'submenu_animate_in',
                    'className'       => 'mgz-inner-widthauto',
                    'templateOptions' => [
                        'templateUrl' => 'Magezon_Builder/js/templates/form/element/animation-style.html',
                        'element'     => 'Magezon_Builder/js/form/element/animation-in',
                        'label'       => __('Animation In')
                    ]
                ]
            );

            $submenu->addChildren(
                'submenu_animate_out',
                'select',
                [
                    'sortOrder'       => 60,
                    'key'             => 'submenu_animate_out',
                    'className'       => 'mgz-inner-widthauto',
                    'templateOptions' => [
                        'templateUrl' => 'Magezon_Builder/js/templates/form/element/animation-style.html',
                        'element'     => 'Magezon_Builder/js/form/element/animation-out',
                        'label'       => __('Animation Out')
                    ]
                ]
            );

            $submenu->addChildren(
                'submenu_animation_duration',
                'number',
                [
                    'sortOrder'       => 70,
                    'className'       => 'mgz-width50',
                    'key'             => 'submenu_animation_duration',
                    'templateOptions' => [
                        'label'       => __('Animation Duration(s)'),
                        'placeholder' => '.2'
                    ]
                ]
            );

            $submenu->addChildren(
                'submenu_inline_css',
                'code',
                [
                    'sortOrder'       => 80,
                    'key'             => 'submenu_inline_css',
                    'templateOptions' => [
                        'label' => __('Inline CSS')
                    ]
                ]
            );

    	return $submenu;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareDesignTab()
    {
    	$tab = parent::prepareDesignTab();
    	$config = $tab->getData('config');
    	$config['templateOptions']['label'] = __('Submenu Design');
    	$tab->setData('config', $config);
    	return $tab;
    }

    public function getItemType()
    {
        return [
            [
                'label' => __('Custom Link'),
                'value' => 'custom'
            ],
            [
                'label' => __('Category Link'),
                'value' => 'category'
            ],
            [
                'label' => __('Product Link'),
                'value' => 'product'
            ],
            [
                'label' => __('CMS Page Link'),
                'value' => 'page'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getSubmenuType()
    {
        return [
            [
                'label' => __('Mega Submenu'),
                'value' => 'mega'
            ],
            [
                'label' => __('Stack Submenu'),
                'value' => 'stack'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getMegaSubmenuPosition()
    {
        return [
            [
                'label' => __('Center'),
                'value' => 'center'
            ],
            [
                'label' => __('Left Edge of Menu Bar'),
                'value' => 'left_edge_menu_bar'
            ],
            [
                'label' => __('Right Edge of Menu Bar'),
                'value' => 'right_edge_menu_bar'
            ],
            [
                'label' => __('Left Edge of Parent Item'),
                'value' => 'left_edge_parent_item'
            ],
            [
                'label' => __('Right Edge of Parent Item'),
                'value' => 'right_edge_parent_item'
            ],
            [
                'label' => __('Left - Vertical - Full Height'),
                'value' => 'left_vertical_full_height'
            ],
            [
                'label' => __('Right - Vertical - Full Height'),
                'value' => 'right_vertical_full_height'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getLabelPosition()
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
                'label' => __('Left'),
                'value' => 'left'
            ],
            [
                'label' => __('Right'),
                'value' => 'right'
            ]
        ];
    }
}
