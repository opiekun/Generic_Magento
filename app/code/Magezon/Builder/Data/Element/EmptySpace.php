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

class EmptySpace extends AbstractElement
{
    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

	    	$general->addChildren(
	            'height',
	            'text',
	            [
					'sortOrder'       => 10,
					'key'             => 'height',
					'defaultValue'    => '32',
					'templateOptions' => [
						'label' => __('Height')
	                ]
	            ]
	        );

	        $container1 = $general->addContainerGroup(
	            'container1',
	            [
	                'sortOrder' => 20
	            ]
	        );

		    	$container1->addChildren(
		            'lg_height',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'lg_height',
						'templateOptions' => [
							'label' => __('Height on Tablet Landscape(< 1200px)')
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'md_height',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'md_height',
						'templateOptions' => [
							'label' => __('Height on Tablet Portrait(< 992px)')
		                ]
		            ]
		        );

	        $container2 = $general->addContainerGroup(
	            'container2',
	            [
	                'sortOrder' => 30
	            ]
	        );

		    	$container2->addChildren(
		            'sm_height',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'sm_height',
						'templateOptions' => [
							'label' => __('Height on Mobile Landscape(< 768px)')
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'xs_height',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'xs_height',
						'templateOptions' => [
							'label' => __('Height on Mobile Portrait(< 576px)')
		                ]
		            ]
		        );

    	return $general;
    }
}