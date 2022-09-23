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

class Profile extends \Magezon\Builder\Data\Element\AbstractElement
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
                'custom_classes',
                'text',
                [
                    'sortOrder'       => 10,
                    'key'             => 'custom_classes',
                    'templateOptions' => [
                        'label' => __('Custom Classes'),
                        'note'  => __('Style particular content element differently - add a class name and refer to it in custom CSS.')
                    ]
                ]
            );

            $general->addChildren(
                'custom_css',
                'code',
                [
                    'sortOrder'       => 20,
                    'key'             => 'custom_css',
                    'templateOptions' => [
                        'label' => __('Custom CSS'),
                        'note'  => __('Enter custom CSS (Note: it will be outputted only on this particular page). <a href="%1" target="_blank">How to add custom css</a>', 'https://blog.magezon.com/how-to-add-custom-css-in-magezon-page-builder?utm_campaign=builder&utm_source=userguide&utm_medium=backend')
                    ]
                ]
            );

        return $this;
    }
}