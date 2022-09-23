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

class Link extends \Magezon\Builder\Data\Element\AbstractElement
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
                'type',
                'select',
                [
                    'sortOrder'       => 10,
                    'key'             => 'type',
                    'defaultValue'    => 'custom',
                    'templateOptions' => [
                        'label'   => __('Type'),
                        'options' => $this->getTypeOptions()
                    ]
                ]
            );

            $general->addChildren(
                'url',
                'textarea',
                [
                    'sortOrder'       => 20,
                    'key'             => 'url',
                    'templateOptions' => [
                        'label' => __('Url'),
                        'rows'  => 2
                    ],
                    'hideExpression' => 'model.type!="custom"'
                ]
            );

            $general->addChildren(
                'id',
                'uiSelect',
                [
                    'sortOrder'       => 30,
                    'key'             => 'id',
                    'templateOptions' => [
                        'label'       => __('Id'),
                        'showValue'   => true,
                        'element'     => 'Magezon_Builder/js/form/element/link_entity',
                        'placeholder' => __('Search item by name or id')
                    ],
                    'hideExpression' => 'model.type=="custom"'
                ]
            );

            $general->addChildren(
                'extra',
                'textarea',
                [
                    'sortOrder'       => 40,
                    'key'             => 'extra',
                    'templateOptions' => [
                        'label' => __('Extra Params'),
                        'note'  => __('Add parameters to link. Eg: utm_source=news4&utm_medium=email&utm_campaign=spring-summer'),
                        'rows'  => 2
                    ],
                    'hideExpression' => 'model.type=="custom"'
                ]
            );

            $general->addChildren(
                'title',
                'text',
                [
                    'sortOrder'       => 50,
                    'key'             => 'title',
                    'templateOptions' => [
                        'label' => __('Title')
                    ]
                ]
            );

            $container1 = $general->addContainerGroup(
                'container1',
                [
                    'sortOrder' => 60
                ]
            );

                $container1->addChildren(
                    'blank',
                    'toggle',
                    [
                        'sortOrder'       => 10,
                        'key'             => 'blank',
                        'templateOptions' => [
                            'label' => __('Open link in a new tab')
                        ]
                    ]
                );

                $container1->addChildren(
                    'nofollow',
                    'toggle',
                    [
                        'sortOrder'       => 20,
                        'key'             => 'nofollow',
                        'templateOptions' => [
                            'label' => __('Add nofollow option to link')
                        ]
                    ]
                );

        return $this;
    }

    public function getTypeOptions()
    {
        $options[] = [
            'label' => __('Category'),
            'value' => 'category'
        ];
        $options[] = [
            'label' => __('Product'),
            'value' => 'product'
        ];
        $options[] = [
            'label' => __('Page'),
            'value' => 'page'
        ];
        $options[] = [
            'label' => __('Custom'),
            'value' => 'custom'
        ];
        return $options;
    }
}