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

class ProductList extends \Magezon\Builder\Data\Element\AbstractElement
{

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareProductOptionsTab()
    {
    	$product = $this->addTab(
            'tab_product',
            [
                'sortOrder'       => 90,
                'templateOptions' => [
                    'label' => __('Product Options')
                ]
            ]
        );

	        $container1 = $product->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
		    );

		        $container1->addChildren(
		            'product_name',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'product_name',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Name')
		                ]
		            ]
		        );

		        $container1->addChildren(
		            'product_price',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'product_price',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Price')
		                ]
		            ]
		        );

	        $container2 = $product->addContainerGroup(
	            'container2',
	            [
					'sortOrder' => 20
	            ]
		    );

		        $container2->addChildren(
		            'product_image',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'product_image',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Image')
		                ]
		            ]
		        );

		        $container2->addChildren(
		            'product_review',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'product_review',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Review')
		                ]
		            ]
		        );

	        $container3 = $product->addContainerGroup(
	            'container3',
	            [
					'sortOrder' => 30
	            ]
		    );

		        $container3->addChildren(
		            'product_addtocart',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'product_addtocart',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Add To Cart')
		                ]
		            ]
		        );

		        $container3->addChildren(
		            'product_shortdescription',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'product_shortdescription',
						'templateOptions' => [
							'label' => __('Short Description')
		                ]
		            ]
		        );

	        $container4 = $product->addContainerGroup(
	            'container4',
	            [
					'sortOrder' => 40
	            ]
		    );

		        $container4->addChildren(
		            'product_wishlist',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'product_wishlist',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Wishlist Link')
		                ]
		            ]
		        );

		        $container4->addChildren(
		            'product_compare',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'product_compare',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Compare Link')
		                ]
		            ]
		        );

	        $container5 = $product->addContainerGroup(
	            'container5',
	            [
					'sortOrder' => 50
	            ]
		    );

		        $container5->addChildren(
		            'product_swatches',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'product_swatches',
						'templateOptions' => [
							'label' => __('Swatches')
		                ]
		            ]
		        );

		        $container5->addChildren(
		            'product_equalheight',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'product_equalheight',
						'templateOptions' => [
							'label' => __('Equal Height')
		                ]
		            ]
		        );

	        $container6 = $product->addContainerGroup(
	            'container6',
	            [
					'sortOrder' => 60
	            ]
		    );

		        $container6->addChildren(
		            'product_padding',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'product_padding',
						'templateOptions' => [
							'label' => __('Padding')
		                ]
		            ]
		        );

		        $container6->addChildren(
		            'product_background',
		            'color',
		            [
						'sortOrder'       => 20,
						'key'             => 'product_background',
						'templateOptions' => [
							'label' => __('Background Color')
		                ]
		            ]
		        );

    	return $product;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareConditionTab()
    {
    	$condition = $this->addTab(
            'tab_condition',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Condition')
                ]
            ]
        );

	        $container1 = $condition->addContainerGroup(
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
						'defaultValue'    => 'latest',
						'templateOptions' => [
							'label'   => __('Data Source'),
							'options' => $this->getSourceOptions()
		                ]
		            ]
		        );

		        $container1->addChildren(
		            'max_items',
		            'number',
		            [
						'sortOrder'       => 20,
						'key'             => 'max_items',
						'defaultValue'    => 10,
						'templateOptions' => [
							'label'   => __('Total Items')
		                ]
		            ]
		        );

	        $container2 = $condition->addContainerGroup(
	            'container2',
	            [
					'sortOrder' => 20
	            ]
		    );

		    	$container2->addChildren(
		            'orer_by',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'orer_by',
						'defaultValue'    => 'default',
						'templateOptions' => [
							'label'   => __('Order By'),
							'options' => $this->getOrderByOptions()
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'show_out_of_stock',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'show_out_of_stock',
						'templateOptions' => [
							'label' => __('Display Out of Stock Products')
		                ]
		            ]
		        );

	    	$condition->addChildren(
	            'condition',
	            'condition',
	            [
					'sortOrder'       => 30,
					'key'             => 'condition',
					'templateOptions' => [
						'label' => __('Conditions')
	                ]
	            ]
	        );

    	return $condition;
    }

    public function getSourceOptions()
    {
        return [
            [
                'label' => __('Latest'),
                'value' => 'latest'
            ],
            [
                'label' => __('New Arrival'),
                'value' => 'new'
            ],
            [
                'label' => __('Best Sellers'),
                'value' => 'bestseller'
            ],
            [
                'label' => __('On Sale'),
                'value' => 'onsale'
            ],
            [
                'label' => __('Most Viewed'),
                'value' => 'mostviewed'
            ],
            [
                'label' => __('Wishlist Top'),
                'value' => 'wishlisttop'
            ],
            [
                'label' => __('Top Rated'),
                'value' => 'toprated'
            ],
            [
                'label' => __('Featured'),
                'value' => 'featured'
            ],
            [
                'label' => __('Free'),
                'value' => 'free'
            ],
            [
                'label' => __('Random'),
                'value' => 'random'
            ]
        ];
    }

    public function getOrderByOptions()
    {
        return [
            [
                'label' => __('Default'),
                'value' => 'default'
            ],
            [
                'label' => __('Alphabetically'),
                'value' => 'alphabetically'
            ],
            [
                'label' => __('Price: Low to High'),
                'value' => 'price_low_to_high'
            ],
            [
                'label' => __('Price: High to Low'),
                'value' => 'price_high_to_low'
            ],
            [
                'label' => __('Random'),
                'value' => 'random'
            ],
            [
                'label' => __('Newest First'),
                'value' => 'newestfirst'
            ],
            [
                'label' => __('Oldest First'),
                'value' => 'oldestfirst'
            ]
        ];
    }
}