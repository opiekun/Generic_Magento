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

class SearchForm extends \Magezon\Builder\Data\Element\AbstractElement
{
    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
        $general = parent::prepareGeneralTab();

            $general->addChildren(
                'placeholder',
                'text',
                [
                    'sortOrder'       => 10,
                    'key'             => 'placeholder',
                    'defaultValue'    => 'Search entire store here...',
                    'templateOptions' => [
                        'label'   => __('Placeholder')
                    ]
                ]
            );

            $general->addChildren(
                'form_width',
                'text',
                [
                    'sortOrder'       => 20,
                    'key'             => 'form_width',
                    'defaultValue'    => 250,
                    'templateOptions' => [
                        'label'   => __('Width')
                    ]
                ]
            );

            $general->addChildren(
                'input_background_color',
                'color',
                [
                    'sortOrder'       => 30,
                    'key'             => 'input_background_color',
                    'templateOptions' => [
                        'label' => __('Input Background Color')
                    ]
                ]
            );

            $general->addChildren(
                'input_text_color',
                'color',
                [
                    'sortOrder'       => 40,
                    'key'             => 'input_text_color',
                    'templateOptions' => [
                        'label' => __('Input Text Color')
                    ]
                ]
            );

        return $general;
    }
}
