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

class Headding extends AbstractElement
{
    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

	    	$general->addChildren(
	            'text',
	            'textarea',
	            [
					'sortOrder'       => 10,
					'key'             => 'text',
					'defaultValue'    => 'This is heading element',
					'templateOptions' => [
                        'label' => __('Text'),
                        'rows'  => 3
	                ]
	            ]
	        );

            $general->addChildren(
                'heading_type',
                'select',
                [
                    'sortOrder'       => 20,
                    'key'             => 'heading_type',
                    'defaultValue'    => 'h2',
                    'templateOptions' => [
                        'label'   => __('Heading Type'),
                        'options' => $this->getHeadingType()
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
                    'font_size',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'font_size',
                        'templateOptions' => [
                            'label' => __('Font Size')
                        ]
                    ]
                );

                $container1->addChildren(
                    'color',
                    'color',
                    [
                        'key'             => 'color',
                        'sortOrder'       => 20,
                        'templateOptions' => [
                            'label' => __('Text Color')
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
                    'line_height',
                    'text',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'line_height',
                        'templateOptions' => [
                            'label' => __('Line Height')
                        ]
                    ]
                );

    	        $container2->addChildren(
    	            'font_weight',
    	            'text',
    	            [
                        'sortOrder'       => 20,
                        'key'             => 'font_weight',
                        'templateOptions' => [
                            'label' => __('Font Weight')
    	                ]
    	            ]
    	        );

            $container3 = $general->addContainerGroup(
                'container3',
                [
                    'sortOrder' => 50
                ]
            );

                $container3->addChildren(
                    'link',
                    'link',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'link',
                        'className'       => 'mgz-width200',
                        'templateOptions' => [
                            'label' => __('Link')
                        ]
                    ]
                );

    	return $general;
    }
}