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

class Separator extends AbstractElement
{
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
						'templateOptions' => [
							'label' => __('Widget Title')
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
		            'title_tag',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'title_tag',
						'defaultValue'    => 'h2',
						'templateOptions' => [
							'label'   => __('Title Tag'),
							'options' => $this->getHeadingType()
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'title_align',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'title_align',
						'defaultValue'    => 'center',
						'templateOptions' => [
							'label'   => __('Title Alignment'),
							'options' => $this->getAlignOptions()
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'title_color',
		            'color',
		            [
						'sortOrder'       => 30,
						'key'             => 'title_color',
						'templateOptions' => [
							'label' => __('Title Color')
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
		            'style',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'style',
						'defaultValue'    => 'solid',
						'templateOptions' => [
							'label'   => __('Line Style'),
							'options' => $this->getBorderStyle()
		                ]
		            ]
		        );

		    	$container3->addChildren(
		            'line_weight',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'line_weight',
						'defaultValue'    => 1,
						'templateOptions' => [
							'label' => __('Line Weight')
		                ]
		            ]
		        );

		    	$container3->addChildren(
		            'color',
		            'color',
		            [
						'sortOrder'       => 30,
						'key'             => 'color',
						'defaultValue'    => '#ebebeb',
						'templateOptions' => [
							'label' => __('Line Color')
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
	                'el_width',
	                'text',
	                [
						'sortOrder'       => 10,
						'key'             => 'el_width',
						'templateOptions' => [
							'label' => __('Element width')
	                    ]
	                ]
	            );

		        $container4->addChildren(
	                'add_icon',
	                'toggle',
	                [
	                    'sortOrder'       => 20,
	                    'key'             => 'add_icon',
	                    'templateOptions' => [
	                        'label' => __('Add Icon')
	                    ]
	                ]
	            );

            $container5 = $general->addContainerGroup(
                'container5',
                [
					'sortOrder'      => 40,
					'hideExpression' => '!model.add_icon'
                ]
            );

                $container5->addChildren(
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

                $container5->addChildren(
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
     * @return array
     */
    public function getDefaultValues()
    {
        return [
			'align' => 'center'
        ];
    }
}