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

class Button extends \Magezon\Builder\Data\Element\AbstractElement
{
    /**
     * Prepare modal components
     */
    public function prepareForm()
    {
    	parent::prepareForm();
    	$this->prepareButtonDesignTab();
    	return $this;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

	    	$general->addChildren(
	            'title',
	            'text',
	            [
					'sortOrder'       => 10,
					'key'             => 'title',
					'defaultValue'    => 'Text on the button',
					'templateOptions' => [
						'label' => __('Text')
	                ]
	            ]
	        );

	    	$general->addChildren(
	            'link',
	            'link',
	            [
					'sortOrder'       => 20,
					'key'             => 'link',
					'templateOptions' => [
						'label' => __('Link')
	                ]
	            ]
	        );

	    	$general->addChildren(
	            'onclick_code',
	            'text',
	            [
					'sortOrder'       => 30,
					'key'             => 'onclick_code',
					'templateOptions' => [
						'label' => __('On Click Code')
	                ]
	            ]
	        );

	        $container1 = $general->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 40,
	            ]
	        );

		    	$container1->addChildren(
		            'add_icon',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'add_icon',
						'templateOptions' => [
							'label' => __('Add Icon')
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'auto_width',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'auto_width',
						'templateOptions' => [
							'label' => __('Element Auto Width'),
							'note'  => __('Display multiple buttons in same row')
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'display_as_link',
		            'toggle',
		            [
						'sortOrder'       => 30,
						'key'             => 'display_as_link',
						'templateOptions' => [
							'label' => __('Display as link')
		                ]
		            ]
		        );

	        $container2 = $general->addContainerGroup(
	            'container2',
	            [
					'sortOrder'      => 50,
					'hideExpression' => '!model.add_icon'
	            ]
	        );

		        $container2->addChildren(
		            'icon',
		            'icon',
		            [
						'sortOrder'       => 10,
						'key'             => 'icon',
						'templateOptions' => [
							'label' => __('Icon')
		                ]
		            ]
		        );

		        $container2->addChildren(
		            'icon_position',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'icon_position',
						'defaultValue'    => 'left',
						'templateOptions' => [
							'label'   => __('Icon Position'),
							'options' => $this->getIconPosition()
		                ]
		            ]
		        );

    	return $general;
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
                'label' => __('3D'),
                'value' => '3d'
            ],
            [
                'label' => __('Gradient'),
                'value' => 'gradient'
            ]
        ];
    }
}