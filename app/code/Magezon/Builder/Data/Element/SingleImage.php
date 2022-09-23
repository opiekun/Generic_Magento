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

class SingleImage extends AbstractElement
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

	        $container1 = $general->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
		    );

		    	$container1->addChildren(
		            'source',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'source',
						'defaultValue'    => 'media_library',
						'templateOptions' => [
							'label'   => __('Image Source'),
							'options' => $this->getSource()
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'onclick',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'onclick',
						'defaultValue'    => '',
						'templateOptions' => [
							'label'   => __('On click action'),
							'options' => $this->getOnclickAction()
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'responsive_images',
		            'select',
		            [
						'sortOrder'       => 30,
						'key'             => 'responsive_images',
						'defaultValue'    => '',
						'templateOptions' => [
							'label'   => __('Responsive Images'),
							'options' => $this->getResponsiveImages()
		                ],
						'hideExpression' => 'model.source!="media_library"'
		            ]
		        );

	    	$general->addChildren(
	            'custom_src',
	            'text',
	            [
					'sortOrder'       => 20,
					'key'             => 'custom_src',
					'templateOptions' => [
						'label' => __('External link')
	                ],
	                'hideExpression' => 'model.source!="external_link"'
	            ]
	        );

	        $container2 = $general->addContainerGroup(
	            'container2',
	            [
					'sortOrder'      => 30,
					'hideExpression' => 'model.source!="media_library"'
	            ]
		    );

		    	$container2->addChildren(
		            'image',
		            'image',
		            [
						'sortOrder'       => 10,
						'key'             => 'image',
						'defaultValue'    => 'mgzbuilder/no_image.png',
						'templateOptions' => [
							'label' => __('Image')
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'hover_image',
		            'image',
		            [
						'sortOrder'       => 10,
						'key'             => 'hover_image',
						'templateOptions' => [
							'label' => __('Hover Image')
		                ]
		            ]
		        );

	        $container3 = $general->addContainerGroup(
	            'container3',
	            [
					'sortOrder'      => 40,
					'hideExpression' => 'model.responsive_images!="custom"'
	            ]
		    );

		    	$container3->addChildren(
		            'tablet_image',
		            'image',
		            [
						'sortOrder'       => 20,
						'key'             => 'tablet_image',
						'templateOptions' => [
							'label' => __('Tablet(<1024)')
		                ]
		            ]
		        );

		    	$container3->addChildren(
		            'landscape_phone_image',
		            'image',
		            [
						'sortOrder'       => 30,
						'key'             => 'landscape_phone_image',
						'templateOptions' => [
							'label' => __('Landscape Phone(<768px)')
		                ]
		            ]
		        );

		    	$container3->addChildren(
		            'portrait_phone_image',
		            'image',
		            [
						'sortOrder'       => 40,
						'key'             => 'portrait_phone_image',
						'templateOptions' => [
							'label' => __('Portrait Phone(<576px)')
		                ]
		            ]
		        );

	        $container4 = $general->addContainerGroup(
	            'container4',
	            [
					'sortOrder'      => 50,
					'hideExpression' => 'model.onclick!="magnific"'
	            ]
		    );

		    	$container4->addChildren(
		            'popup_image',
		            'image',
		            [
						'sortOrder'       => 10,
						'key'             => 'popup_image',
						'templateOptions' => [
							'label' => __('Popup Image')
		                ]
		            ]
		        );

	            $container4->addChildren(
	                'zoom_effect',
	                'toggle',
	                [
						'key'             => 'zoom_effect',
						'sortOrder'       => 20,
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Enable Zoom Effect')
	                    ],
	                    'hideExpression' => 'model.onclick!="magnific"'
	                ]
	            );

            $general->addChildren(
                'video_map',
                'text',
                [
					'key'             => 'video_map',
					'sortOrder'       => 60,
					'templateOptions' => [
						'label' => __('Video or Map')
                    ],
                    'hideExpression' => 'model.onclick!="video_map"'
                ]
            );

	    	$general->addChildren(
	            'custom_link',
	            'link',
	            [
					'sortOrder'       => 70,
					'key'             => 'custom_link',
					'templateOptions' => [
						'label' => __('Custom link')
	                ],
	                'hideExpression' => 'model.onclick!="custom_link"'
	            ]
	        );

	        $container5 = $general->addContainerGroup(
	            'container5',
	            [
					'sortOrder' => 80
	            ]
		    );

		        $container5->addChildren(
		            'image_style',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'image_style',
						'defaultValue'    => '',
						'templateOptions' => [
							'label'   => __('Image Style'),
							'options' => $this->getImageStyle()
		                ]
		            ]
		        );

		        $container5->addChildren(
		            'image_hover_effect',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'image_hover_effect',
						'defaultValue'    => '',
						'templateOptions' => [
							'label'   => __('Image Hover Effect'),
							'options' => $this->getImageHoverEffect()
		                ]
		            ]
		        );

	        $container6 = $general->addContainerGroup(
	            'container6',
	            [
					'sortOrder' => 90
	            ]
		    );

		    	$container6->addChildren(
		            'image_width',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'image_width',
						'templateOptions' => [
							'label' => __('Image Width')
		                ]
		            ]
		        );

		        $container6->addChildren(
		            'image_height',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'image_height',
						'templateOptions' => [
							'label' => __('Image Height')
		                ]
		            ]
		        );

	        $container7 = $general->addContainerGroup(
	            'container7',
	            [
					'sortOrder' => 100
	            ]
		    );

		        $container7->addChildren(
		            'alt_tag',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'alt_tag',
						'templateOptions' => [
							'label' => __('Alternative Text')
		                ]
		            ]
		        );

		        $container7->addChildren(
		            'img_id',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'img_id',
						'templateOptions' => [
							'label' => __('Image ID')
		                ]
		            ]
		        );

	        $general->addChildren(
	            'title',
	            'text',
	            [
					'sortOrder'       => 110,
					'key'             => 'title',
					'templateOptions' => [
						'label' => __('Title')
	                ]
	            ]
	        );

	        $general->addChildren(
	            'description',
	            'textarea',
	            [
					'sortOrder'       => 120,
					'key'             => 'description',
					'templateOptions' => [
						'label' => __('Description')
	                ]
	            ]
	        );

	        $container8 = $general->addContainerGroup(
	            'container8',
	            [
					'sortOrder' => 130
	            ]
		    );

	        	$positions = $this->getContentPosition();
	        	array_unshift($positions, ['label' => 'Below Image', 'value' => 'below']);
	        	array_unshift($positions, ['label' => 'None', 'value' => '']);
		    	$container8->addChildren(
		            'content_position',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'content_position',
						'defaultValue'    => '',
						'templateOptions' => [
							'label'   => __('Content Position'),
							'options' => $positions
		                ]
		            ]
		        );

	        	$container8->addChildren(
		            'content_align',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'content_align',
						'defaultValue'    => 'center',
						'templateOptions' => [
							'label'   => __('Content Alignment'),
							'options' => $this->getAlignOptions()
		                ]
		            ]
		        );

		    	$container8->addChildren(
		            'display_on_hover',
		            'toggle',
		            [
						'sortOrder'       => 30,
						'key'             => 'display_on_hover',
						'templateOptions' => [
							'label' => __('Display Content on Hover')
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
    	$tab = $this->addTab(
            'tab_style',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Style')
                ]
            ]
        );

	        $container1 = $tab->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
		    );

		    	$container1->addChildren(
		            'content_padding',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'content_padding',
						'templateOptions' => [
							'label' => __('Content Padding')
		                ]
		            ]
		        );

		        $container1->addChildren(
		            'content_fullwidth',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'content_fullwidth',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Content Fullwidth')
		                ]
		            ]
		        );

		    $content = $tab->addTab(
                'content',
                [
                    'sortOrder'       => 20,
                    'templateOptions' => [
                        'label' => __('Content')
                    ]
                ]
            );

                $normal = $content->addContainerGroup(
                    'normal',
                    [
                        'sortOrder'       => 10,
                        'templateOptions' => [
                            'label' => __('Normal')
                        ]
                    ]
                );

                	$normal->addChildren(
			            'content_color',
			            'color',
			            [
							'sortOrder'       => 10,
							'key'             => 'content_color',
							'templateOptions' => [
								'label' => __('Text Color')
			                ]
			            ]
			        );

			    	$normal->addChildren(
			            'content_background',
			            'color',
			            [
							'sortOrder'       => 20,
							'key'             => 'content_background',
							'templateOptions' => [
								'label' => __('Background Color')
			                ]
			            ]
			        );

			    $hover = $content->addContainerGroup(
                    'hover',
                    [
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label' => __('Hover')
                        ]
                    ]
                );

                	$hover->addChildren(
			            'content_hover_color',
			            'color',
			            [
							'sortOrder'       => 10,
							'key'             => 'content_hover_color',
							'templateOptions' => [
								'label' => __('Text Color')
			                ]
			            ]
			        );

			    	$hover->addChildren(
			            'content_hover_background',
			            'color',
			            [
							'sortOrder'       => 20,
							'key'             => 'content_hover_background',
							'templateOptions' => [
								'label' => __('Background Color')
			                ]
			            ]
			        );

		    $container3 = $tab->addContainerGroup(
	            'container3',
	            [
					'sortOrder' => 30
	            ]
	        );

		    	$container3->addChildren(
		            'title_font_size',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'title_font_size',
						'defaultValue'    => '16px',
						'templateOptions' => [
							'label' => __('Title Font Size')
		                ]
		            ]
		        );

		    	$container3->addChildren(
		            'title_font_weight',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'title_font_weight',
						'templateOptions' => [
							'label' => __('Title Font Weight')
		                ]
		            ]
		        );

        	$container4 = $tab->addContainerGroup(
	            'container4',
	            [
					'sortOrder' => 40
	            ]
	        );

		    	$container4->addChildren(
		            'description_font_size',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'description_font_size',
						'templateOptions' => [
							'label' => __('Description Font Size')
		                ]
		            ]
		        );

		    	$container4->addChildren(
		            'description_font_weight',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'description_font_weight',
						'templateOptions' => [
							'label' => __('Description Font Weight')
		                ]
		            ]
		        );

	        $container5 = $tab->addContainerGroup(
	            'container5',
	            [
					'sortOrder' => 50
	            ]
		    );

		    	$container5->addChildren(
		            'image_border_radius',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'image_border_radius',
						'templateOptions' => [
							'label'       => __('Border Radius'),
							'placeholder' => '0px'
		                ]
		            ]
		        );

		    	$container5->addChildren(
		            'image_border_width',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'image_border_width',
						'templateOptions' => [
							'label'       => __('Border Width'),
							'placeholder' => '1px'
		                ]
		            ]
		        );

	        $container6 = $tab->addContainerGroup(
	            'container6',
	            [
					'sortOrder' => 60
	            ]
		    );

	            $container6->addChildren(
	                'image_border_style',
	                'select',
	                [
						'key'             => 'image_border_style',
						'sortOrder'       => 10,
						'defaultValue'    => 'solid',
						'templateOptions' => [
							'label'   => __('Border Style'),
							'options' => $this->getBorderStyle()
	                    ]
	                ]
	            );

	            $container6->addChildren(
	                'image_border_color',
	                'color',
	                [
						'key'             => 'image_border_color',
						'sortOrder'       => 20,
						'templateOptions' => [
							'label' => __('Border Color')
	                    ]
	                ]
	            );

	        $container7 = $tab->addContainerGroup(
	            'container7',
	            [
					'sortOrder' => 70
	            ]
		    );

	            $container7->addChildren(
	                'overlay_color',
	                'color',
	                [
						'key'             => 'overlay_color',
						'sortOrder'       => 10,
						'templateOptions' => [
							'label' => __('Overlay Color')
	                    ]
	                ]
	            );

	            $container7->addChildren(
	                'hover_overlay_color',
	                'color',
	                [
						'key'             => 'hover_overlay_color',
						'sortOrder'       => 20,
						'templateOptions' => [
							'label' => __('Hover Overlay Color')
	                    ]
	                ]
	            );

        return $tab;
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return [
            [
				'label' => __('Media library'),
				'value' => 'media_library'
            ],
            [
                'label' => __('External link'),
                'value' => 'external_link'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getImageStyle()
    {
        return [
            [
				'label' => __('Default'),
				'value' => ''
            ],
            [
				'label' => __('Outline'),
				'value' => 'mgz-box-outline'
            ],
            [
				'label' => __('Shadow1'),
				'value' => 'mgz-box-shadow'
            ],
            [
				'label' => __('Shadow2'),
				'value' => 'mgz-box-shadow2'
            ],
            [
				'label' => __('3D Shadow'),
				'value' => 'mgz-box-shadow-3d'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getOnclickAction()
    {
        return [
            [
				'label' => __('None'),
				'value' => ''
            ],
            [
                'label' => __('Open Magnific Popup'),
                'value' => 'magnific'
            ],
            [
                'label' => __('Open Custom Link'),
                'value' => 'custom_link'
            ],
            [
                'label' => __('Open Video or Map'),
                'value' => 'video_map'
            ],
            [
                'label' => __('Open Media File'),
                'value' => 'pdf'
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
    public function getImageHoverEffect()
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
                'label' => __('Lift Up'),
                'value' => 'liftup'
            ],
            [
                'label' => __('Zoom Out'),
                'value' => 'zoomout'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getResponsiveImages()
    {
        return [
            [
                'label' => __('Auto'),
                'value' => 'auto'
            ],
            [
                'label' => __('Custom'),
                'value' => 'custom'
            ],
            [
                'label' => __('None'),
                'value' => ''
            ]
        ];
    }
}