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

class Gmaps extends \Magezon\Builder\Data\Element\AbstractElement
{
    /**
     * Prepare modal components
     */
    public function prepareForm()
    {
    	parent::prepareForm();
    	$this->prepareMarkersTab();
    	return $this;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

		    $key = $this->builderHelper->getGoogleMapApi();
	        if (!$key) {
	        	$general->addChildren(
		            'message',
		            'html',
		            [
						'sortOrder'       => 0,
						'templateOptions' => [
							'content' => __('<p>Configure Google Map API key at <a href="%1" target="_blank">here</a></p>', $this->builderHelper->getUrl('adminhtml/system_config/edit/section/mgzbuilder'))
		                ]
		            ]
		        );
	        }

	    	$container1 = $general->addContainerGroup(
	            'container1',
	            [
	                'sortOrder' => 10
	            ]
	        );

	        	$container1->addChildren(
		            'map_width',
		            'text',
		            [
						'key'             => 'map_width',
						'sortOrder'       => 10,
						'defaultValue'    => '100%',
						'templateOptions' => [
							'label' => __('Width')
		                ]
		            ]
		        );

	        	$container1->addChildren(
		            'map_height',
		            'text',
		            [
						'key'             => 'map_height',
						'sortOrder'       => 20,
						'defaultValue'    => 400,
						'templateOptions' => [
							'label' => __('Height')
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
		            'map_zoom',
		            'select',
		            [
						'key'             => 'map_zoom',
						'sortOrder'       => 10,
						'defaultValue'    => 12,
						'templateOptions' => [
							'label'   => __('Zoom'),
							'options' => $this->getRange(1, 16)
		                ]
		            ]
		        );

	        	$container2->addChildren(
		            'map_type',
		            'select',
		            [
						'key'             => 'map_type',
						'sortOrder'       => 20,
						'defaultValue'    => 'roadmap',
						'templateOptions' => [
							'label'   => __('Type'),
							'options' => $this->getMapType()
		                ]
		            ]
		        );

	    	$container3 = $general->addContainerGroup(
	            'container3',
	            [
	                'sortOrder' => 30
	            ]
	        );

	        	$container3->addChildren(
		            'map_ui',
		            'toggle',
		            [
		                'key'             => 'map_ui',
		                'sortOrder'       => 10,
						'defaultValue'    => true,
		                'templateOptions' => [
							'label' => __('Disable UI'),
							'note'  => __('Disable Google Map user interface elements')
		                ]
		            ]
		        );

	        	$container3->addChildren(
		            'map_scrollwheel',
		            'toggle',
		            [
		                'key'             => 'map_scrollwheel',
		                'sortOrder'       => 20,
						'defaultValue'    => true,
		                'templateOptions' => [
							'label' => __('Scrollwheel'),
							'note'  => __('If false, disable scrollwheel zooming on the map')
		                ]
		            ]
		        );

	        	$container3->addChildren(
		            'map_draggable',
		            'toggle',
		            [
						'key'             => 'map_draggable',
						'sortOrder'       => 30,
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Draggable'),
							'note'  => __('If false, prevent the map from being dragged.')
		                ]
		            ]
		        );

	    	$container4 = $general->addContainerGroup(
	            'container4',
	            [
	                'sortOrder' => 40
	            ]
	        );

	        	$container4->addChildren(
		            'infobox_opened',
		            'toggle',
		            [
						'key'             => 'infobox_opened',
						'sortOrder'       => 10,
						'templateOptions' => [
							'label' => __('InforBox Opened by Default')
		                ]
		            ]
		        );

	        	$container4->addChildren(
		            'infobox_width',
		            'text',
		            [
						'key'             => 'infobox_width',
						'sortOrder'       => 10,
						'templateOptions' => [
							'label' => __('InforBox Width')
		                ]
		            ]
		        );

	    	$container5 = $general->addContainerGroup(
	            'container5',
	            [
	                'sortOrder' => 50
	            ]
	        );

	        	$container5->addChildren(
		            'infobox_text_color',
		            'color',
		            [
						'key'             => 'infobox_text_color',
						'sortOrder'       => 20,
						'templateOptions' => [
							'label' => __('InforBox Text Color')
		                ]
		            ]
		        );

	        	$container5->addChildren(
		            'infobox_background_color',
		            'color',
		            [
						'key'             => 'infobox_background_color',
						'sortOrder'       => 30,
						'templateOptions' => [
							'label' => __('InforBox Background Color')
		                ]
		            ]
		        );

    	return $general;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareMarkersTab()
    {
    	$markers = $this->addTab(
            'tab_markers',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Markers')
                ]
            ]
        );

    		$markers->addChildren(
                'html',
                'html',
                [
					'sortOrder'       => 10,
					'templateOptions' => [
						'content' => __('Find latitude & longitude <a href="%1">here</a>', 'https://support.google.com/maps/answer/18539')
					]
                ]
            );

            $items = $markers->addChildren(
                'items',
                'dynamicRows',
                [
					'key'       => 'items',
					'className' => 'mgz-image-carousel-items mgz-editor-simple',
					'sortOrder' => 20
                ]
            );

            	$container1 = $items->addContainerGroup(
	                'container1',
	                [
	                    'sortOrder' => 10
	                ]
	            );

	            	$container1->addChildren(
			            'center',
			            'radio',
			            [
							'key'             => 'center',
							'className'       => 'mgz-width20 mgz_center',
							'sortOrder'       => 0,
							'templateOptions' => [
								'label'   => __('Center'),
								'element' => 'Magezon_Builder/js/form/element/dynamic-rows/radio'
			                ]
			            ]
			        );

	            	$container1->addChildren(
			            'image',
			            'image',
			            [
							'key'             => 'image',
							'sortOrder'       => 10,
							'templateOptions' => [
								'label' => __('Icon')
			                ]
			            ]
			        );

			        $container2 = $container1->addContainer(
		                'container1',
		                [
		                	'sortOrder' => 20
		                ]
		            );

		            	$container2->addChildren(
				            'lat',
				            'text',
				            [
				                'key'             => 'lat',
				                'sortOrder'       => 10,
				                'templateOptions' => [
									'label' => __('Latitude')
				                ]
				            ]
				        );

		            	$container2->addChildren(
				            'lng',
				            'text',
				            [
								'key'             => 'lng',
								'sortOrder'       => 20,
								'templateOptions' => [
									'label' => __('Longitude')
				                ]
				            ]
				        );

	            	$container1->addChildren(
			            'info',
			            'textarea',
			            [
			                'key'             => 'info',
			                'sortOrder'       => 30,
			                'templateOptions' => [
								'label' => __('Infowindow'),
								'rows'  => 6
			                ]
			            ]
			        );

	            	$container1->addChildren(
			            'delete',
			            'actionDelete',
			            [
							'sortOrder' => 40,
							'className' => 'mgz-dynamicrows-actions'
			            ]
			        );

        return $markers;
    }

    /**
     * @return array
     */
    protected function getMapType()
    {
    	return [
    		[
    			'label' => __('Roadmap'),
    			'value' => 'roadmap'
    		],
    		[
    			'label' => __('Satellite'),
    			'value' => 'satellite'
    		],
    		[
    			'label' => __('Hybrid'),
    			'value' => 'hybrid'
    		],
    		[
    			'label' => __('Terrain'),
    			'value' => 'terrain'
    		],
    	];
    }

    /**
     * @return array
     */
    public function getDefaultValues() {
    	return [
    		'items' => [
    			[
					'center' => '1',
					'lat'    => 1.289226,
					'lng'    => 103.862888,
					'info'   => 'Singapore Tower'
    			],
    			[
					'lat'   => 40.759538,
					'lng'   => -73.985028,
					'info'  => 'Times Square'
    			],
    			[
					'lat'   => 51.503531,
					'lng'   => -0.119522,
					'info'  => 'London Eye'
    			],
    			[
					'lat'   => 35.659888,
					'lng'   => 139.745411,
					'info'  => 'Tokyo Tower'
    			]
    		]
    	];
    }
}