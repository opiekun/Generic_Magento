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

namespace Magezon\Builder\Data\Modal;

class Templates extends \Magezon\Builder\Data\Element\AbstractElement
{
    const TAB_TEMPLATES = 'tab_templates';

    /**
     * Prepare modal components
     */
	public function prepareForm()
    {
        $this->prepareTemplatesTab();
    	return $this;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareTemplatesTab()
    {
        $general = $this->addTab(
            self::TAB_TEMPLATES,
            [
                'sortOrder'       => 0,
                'templateOptions' => [
                    'label' => __('Template Library')
                ]
            ]
        );

            $general->addChildren(
                'templates',
                'select',
                [
                    'sortOrder'       => 10,
                    'templateOptions' => [
                        'element'     => 'Magezon_Builder/js/form/element/templates',
                        'templateUrl' => 'Magezon_Builder/js/templates/form/element/templates.html',
                        'url'         => 'mgzbuilder/ajax/libraryTemplate'
                    ]
                ]
            );
    }
}