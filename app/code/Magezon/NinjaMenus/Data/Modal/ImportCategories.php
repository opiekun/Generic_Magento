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

namespace Magezon\NinjaMenus\Data\Modal;

class ImportCategories extends \Magezon\Builder\Data\Element\AbstractElement
{
	public function prepareForm()
    {
    	$general = $this->addTab(
            self::TAB_GENERAL,
            [
                'sortOrder'       => 0,
                'templateOptions' => [
                    'label' => __('General')
                ]
            ]
        );

            $general->addChildren(
                'category_id',
                'uiSelect',
                [
                    'sortOrder'       => 20,
                    'key'             => 'category_id',
                    'templateOptions' => [
                        'required'    => true,
                        'label'       => __('Category'),
                        'source'      => 'category',
                        'placeholder' => __('Search category by name')
                    ]
                ]
            );

            $general->addChildren(
                'import_children',
                'toggle',
                [
                    'sortOrder'       => 20,
                    'key'             => 'import_children',
                    'templateOptions' => [
                        'label' => __('Import Subcategories')
                    ]
                ]
            );

    	return $this;
    }
}