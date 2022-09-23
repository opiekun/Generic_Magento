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

class SocialIcons extends \Magezon\Builder\Data\Element\AbstractElement
{
	/**
	 * @var \Magezon\Builder\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magezon\Builder\Data\FormFactory $formFactory   
	 * @param \Magezon\Builder\Helper\Data      $builderHelper 
	 * @param \Magezon\Builder\Helper\Data      $dataHelper    
	 * @param array                             $data          
	 */
    public function __construct(
        \Magezon\Builder\Data\FormFactory $formFactory,
        \Magezon\Builder\Helper\Data $builderHelper,
        \Magezon\Builder\Helper\Data $dataHelper,
        array $data = []
    ) {
    	parent::__construct($formFactory, $builderHelper, $data);
    	$this->dataHelper = $dataHelper;
    }

    /**
     * Prepare modal components
     */
    public function prepareForm()
    {
    	parent::prepareForm();
    	$this->prepareIconsTab();
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
		    		'link_target',
		    		'select',
		    		[
		    			'sortOrder'       => 10,
		    			'key'             => 'link_target',
		    			'defaultValue'    => '_self',
		    			'templateOptions' => [
		    				'label'   => __('Link Target'),
		    				'options' => $this->getLinkTarget()
		    			]
		    		]
		    	);

		    	$container1->addChildren(
		    		'follow_button',
		    		'toggle',
		    		[
		    			'sortOrder'       => 20,
		    			'key'             => 'follow_button',
		    			'templateOptions' => [
							'label' => __('Follow Button')
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
		            'icon_radius',
		            'number',
		            [
						'sortOrder'       => 10,
						'key'             => 'icon_radius',
						'templateOptions' => [
							'label'   => __('Icon Radius')
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'icon_size',
		            'number',
		            [
						'sortOrder'       => 20,
						'key'             => 'icon_size',
						'templateOptions' => [
							'label'   => __('Icon Size')
		                ]
		            ]
		        );

    	return $general;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareIconsTab()
    {
    	$icons = $this->addTab(
            'tab_icons',
            [
                'sortOrder'       => 80,
                'templateOptions' => [
                    'label' => __('Social Icons')
                ]
            ]
        );

        	$items = $icons->addChildren(
                'items',
                'dynamicRows',
                [
					'key'       => 'items',
					'sortOrder' => 10
                ]
            );

            	$container1 = $items->addContainerGroup(
	                'container1',
	                [
	                    'templateOptions' => [
	                        'sortOrder' => 10
	                    ]
	                ]
	            );


	            	$container2 = $container1->addContainer(
		                'container2',
		                [
							'sortOrder' => 10
		                ]
		            );

		            	$container3 = $container2->addContainerGroup(
			                'container3',
			                [
			                    'sortOrder' => 10
			                ]
			            );

					    	$container3->addChildren(
					            'icon',
					            'select',
					            [
									'sortOrder'       => 10,
									'key'             => 'icon',
									'defaultValue'    => 'fab fa-facebook-f',
									'className'       => 'mgz-width60',
									'templateOptions' => [
										'label'   => __('Icon'),
										'options' => $this->dataHelper->getListSocial()
					                ]
					            ]
					        );

					    	$container3->addChildren(
					            'background_color',
					            'color',
					            [
									'sortOrder'       => 20,
									'key'             => 'background_color',
									'templateOptions' => [
										'label' => __('Background Color')
					                ]
					            ]
					        );

					    	$container3->addChildren(
					            'hover_background_color',
					            'color',
					            [
									'sortOrder'       => 30,
									'key'             => 'hover_background_color',
									'templateOptions' => [
										'label' => __('Hover Background Color')
					                ]
					            ]
					        );


				    	$container2->addChildren(
				            'link',
				            'text',
				            [
								'sortOrder'       => 20,
								'key'             => 'link',
								'templateOptions' => [
									'label' => __('Link')
				                ]
				            ]
				        );

			        $container4 = $container1->addContainer(
		                'container4',
		                [
							'className' => 'mgz-dynamicrows-actions',
							'sortOrder' => 20
		                ]
		            );

		            	$container4->addChildren(
				            'delete',
				            'actionDelete',
				            [
								'sortOrder' => 10
				            ]
				        );

		            	$container4->addChildren(
				            'position',
				            'text',
				            [
								'sortOrder'       => 20,
								'key'             => 'position',
								'templateOptions' => [
									'element' => 'Magezon_Builder/js/form/element/dynamic-rows/position'
								]
				            ]
				        );

    	return $icons;
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
    	return [
    		'align' => 'center',
    		'items' => [
    			[
					'icon' => 'fab mgz-fa-facebook-f',
					'link' => 'https://www.facebook.com/magezonvn'
    			],
    			[
					'icon' => 'fab mgz-fa-twitter',
					'link' => 'https://twitter.com/magezonvn'
    			],
    			[
					'icon' => 'fab mgz-fa-linkedin-in',
					'link' => 'https://www.linkedin.com/in/magezon'
    			],
    			[
					'icon' => 'fab mgz-fa-instagram',
					'link' => 'https://www.instagram.com/magezonvn'
    			],
    			[
					'icon' => 'fab mgz-fa-youtube',
					'link' => 'https://www.youtube.com/channel/UC1XkOcmZeAq-b2VNqM6o4rw'
    			]
    		]
    	];
    }
}