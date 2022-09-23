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
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Data\Element;

class Countdown extends \Magezon\Builder\Data\Element\AbstractElement
{
    /**
     * Prepare modal components
     */
    public function prepareForm()
    {
    	parent::prepareForm();
    	$this->prepareStyleTab();
    	return $this;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

	    	$general->addChildren(
	            'heading_text',
	            'text',
	            [
					'sortOrder'       => 10,
					'key'             => 'heading_text',
					'templateOptions' => [
						'label'   => __('Heading ')
	                ]
	            ]
	        );

	    	$general->addChildren(
	            'sub_heading_text',
	            'text',
	            [
					'sortOrder'       => 20,
					'key'             => 'sub_heading_text',
					'templateOptions' => [
						'label'   => __('Sub Heading ')
	                ]
	            ]
	        );

	        $container1 = $general->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 30
	            ]
		    );

		    	$container1->addChildren(
		            'day',
		            'number',
		            [
						'sortOrder'       => 10,
						'key'             => 'day',
						'defaultValue'    => 20,
						'templateOptions' => [
							'label'   => __('Day')
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'month',
		            'number',
		            [
						'sortOrder'       => 20,
						'key'             => 'month',
						'defaultValue'    => 3,
						'templateOptions' => [
							'label'   => __('Month')
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'year',
		            'number',
		            [
						'sortOrder'       => 30,
						'key'             => 'year',
						'defaultValue'    => 2020,
						'templateOptions' => [
							'label'   => __('Year')
		                ]
		            ]
		        );

	        $container2 = $general->addContainerGroup(
	            'container2',
	            [
					'sortOrder' => 40
	            ]
		    );

		    	$container2->addChildren(
		            'hours',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'hours',
						'defaultValue'    => 0,
						'templateOptions' => [
							'label'   => __('Hours'),
							'options' => $this->getRange(0, 23, 1, true)
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'minutes',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'minutes',
						'defaultValue'    => 0,
						'templateOptions' => [
							'label'   => __('Minutes'),
							'options' => $this->getRange(0, 59, 1, true)
		                ]
		            ]
		        );

		        $container2->addChildren(
		            'time_zone',
		            'select',
		            [
						'sortOrder'       => 30,
						'key'             => 'time_zone',
						'defaultValue'    => 'UTC',
						'templateOptions' => [
							'label'         => __('Time Zone'),
							'builderConfig' => 'timezone'
		                ]
		            ]
			    );

	        $general->addChildren(
	            'link_text',
	            'text',
	            [
					'sortOrder'       => 50,
					'key'             => 'link_text',
					'templateOptions' => [
						'label' => __('Link Text')
	                ]
	            ]
		    );

	        $general->addChildren(
	            'link_url',
	            'link',
	            [
					'sortOrder'       => 60,
					'key'             => 'link_url',
					'templateOptions' => [
						'label' => __('Link Url')
	                ]
	            ]
		    );

    	return $general;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareStyleTab()
    {
    	$style = $this->addTab(
            'style',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Style')
                ]
            ]
        );

	    	$style->addChildren(
	            'layout',
	            'select',
	            [
					'sortOrder'       => 10,
					'key'             => 'layout',
					'defaultValue'    => 'circle',
					'templateOptions' => [
						'label'   => __('Layout'),
						'options' => $this->getLayoutOptions()
	                ]
	            ]
	        );

	        $container1 = $style->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 20
	            ]
		    );

		    	$container1->addChildren(
		            'number_color',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'number_color',
						'templateOptions' => [
							'label' => __('Number Color')
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'number_size',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'number_size',
						'templateOptions' => [
							'label' => __('Number Size')
		                ]
		            ]
		        );

	        $container4 = $style->addContainerGroup(
	            'container4',
	            [
					'sortOrder' => 30
	            ]
		    );

		    	$container4->addChildren(
		            'number_background_color',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'number_background_color',
						'templateOptions' => [
							'label' => __('Number Background Color')
		                ]
		            ]
		        );

		    	$container4->addChildren(
		            'number_border_radius',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'number_border_radius',
						'templateOptions' => [
							'label' => __('Number Border Radius')
		                ]
		            ]
		        );

	        $container2 = $style->addContainerGroup(
	            'container2',
	            [
					'sortOrder' => 40
	            ]
		    );

		    	$container2->addChildren(
		            'number_padding',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'number_padding',
						'defaultValue'    => '10px 20px',
						'templateOptions' => [
							'label' => __('Number Padding')
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'number_spacing',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'number_spacing',
						'defaultValue'    => '10',
						'templateOptions' => [
							'label' => __('Number Spacing')
		                ]
		            ]
		        );

	        $container3 = $style->addContainerGroup(
	            'container3',
	            [
					'sortOrder' => 50
	            ]
		    );

		    	$container3->addChildren(
		            'text_color',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'text_color',
						'templateOptions' => [
							'label' => __('Text Color')
		                ]
		            ]
		        );

		    	$container3->addChildren(
		            'text_size',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'text_size',
						'templateOptions' => [
							'label' => __('Text Size')
		                ]
		            ]
		        );

	        $container5 = $style->addContainerGroup(
	            'container5',
	            [
					'sortOrder' => 60
	            ]
		    );

		    	$container5->addChildren(
		            'text_inline',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'text_inline',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Text Inline')
		                ]
		            ]
		        );

		    	$container5->addChildren(
		            'show_separator',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'show_separator',
						'templateOptions' => [
							'label' => __('Show Time Separators')
		                ]
		            ]
		        );

		    	$container5->addChildren(
		            'separator_type',
		            'select',
		            [
						'sortOrder'       => 30,
						'key'             => 'separator_type',
						'defaultValue'    => 'colon',
						'templateOptions' => [
							'label'   => __('Separator Type'),
							'options' => $this->getSeparatorType()
		                ],
						'hideExpression' => '!model.show_separator'
		            ]
		        );

	        $container6 = $style->addContainerGroup(
	            'container6',
	            [
					'sortOrder'      => 70,
					'hideExpression' => '!model.show_separator'
	            ]
		    );

		    	$container6->addChildren(
		            'separator_color',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'separator_color',
						'defaultValue'    => '#ff9900',
						'templateOptions' => [
							'label' => __('Separator Color')
		                ]
		            ]
		        );

		    	$container6->addChildren(
		            'separator_size',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'separator_size',
						'templateOptions' => [
							'label' => __('Separator Size')
		                ]
		            ]
		        );

			$container7 = $style->addContainerGroup(
	            'container7',
	            [
					'sortOrder'      => 80,
					'hideExpression' => 'model.layout!="circle"'
	            ]
		    );

		    	$container7->addChildren(
		            'circle_size',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'circle_size',
						'defaultValue'    => '200px',
						'templateOptions' => [
							'label' => __('Circle Size')
		                ]
		            ]
		        );

		    	$container7->addChildren(
		            'circle_dash_width',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'circle_dash_width',
						'defaultValue'    => '10px',
						'templateOptions' => [
							'label' => __('Circle Stroke Size')
		                ]
		            ]
		        );

	        $container8 = $style->addContainerGroup(
	            'container8',
	            [
					'sortOrder'      => 90,
					'hideExpression' => 'model.layout!="circle"'
	            ]
		    );

		    	$container8->addChildren(
		            'circle_color1',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'circle_color1',
						'defaultValue'    => '#ff9900',
						'templateOptions' => [
							'label' => __('Circle Color1')
		                ]
		            ]
		        );

		    	$container8->addChildren(
		            'circle_color2',
		            'color',
		            [
						'sortOrder'       => 20,
						'key'             => 'circle_color2',
						'defaultValue'    => '#eaeaea',
						'templateOptions' => [
							'label' => __('Circle Color2')
		                ]
		            ]
		        );

		    	$container8->addChildren(
		            'circle_background_color',
		            'color',
		            [
						'sortOrder'       => 30,
						'key'             => 'circle_background_color',
						'defaultValue'    => '#ffffff',
						'templateOptions' => [
							'label' => __('Circle Background Color')
		                ]
		            ]
		        );

	        $container9 = $style->addContainerGroup(
	            'container9',
	            [
					'sortOrder' => 100
	            ]
		    );

		    	$container9->addChildren(
		            'heading_color',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'heading_color',
						'templateOptions' => [
							'label' => __('Heading Color')
		                ]
		            ]
		        );

		    	$container9->addChildren(
		            'heading_font_size',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'heading_font_size',
						'templateOptions' => [
							'label' => __('Heading Font Size')
		                ]
		            ]
		        );

	        $container10 = $style->addContainerGroup(
	            'container10',
	            [
					'sortOrder' => 110
	            ]
		    );

		    	$container10->addChildren(
		            'sub_heading_color',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'sub_heading_color',
						'templateOptions' => [
							'label' => __('Sub Heading Color')
		                ]
		            ]
		        );

		    	$container10->addChildren(
		            'sub_heading_font_size',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'sub_heading_font_size',
						'templateOptions' => [
							'label' => __('Sub Heading Font Size')
		                ]
		            ]
		        );

	        $container11 = $style->addContainerGroup(
	            'container11',
	            [
					'sortOrder' => 120
	            ]
		    );

		    	$container11->addChildren(
		            'link_color',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'link_color',
						'templateOptions' => [
							'label' => __('Link Color')
		                ]
		            ]
		        );

		    	$container11->addChildren(
		            'link_font_size',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'link_font_size',
						'templateOptions' => [
							'label' => __('Link Font Size')
		                ]
		            ]
		        );

        return $style;
    }

    public function getLayoutOptions()
    {
        return [
            [
                'label' => __('Numbers'),
				'value' => 'numbers'
            ],
            [
                'label' => __('Numbers & Circles'),
                'value' => 'circle'
            ]
        ];
    }

    public function getSeparatorType()
    {
        return [
            [
                'label' => __('Colon'),
				'value' => 'colon'
            ],
            [
                'label' => __('Line'),
                'value' => 'line'
            ]
        ];
    }

    public function getDefaultValues()
    {
    	return [
    		'align' => 'center'
    	];
    }
}