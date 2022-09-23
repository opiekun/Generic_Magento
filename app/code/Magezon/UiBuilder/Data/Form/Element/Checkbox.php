<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_UiBuilder
 * @copyright Copyright (C) 2018 Magezon (https://www.magezon.com)
 */

namespace Magezon\UiBuilder\Data\Form\Element;

class Checkbox extends AbstractElement
{
    public function _construct()
    {
        $this->setType('checkbox');
    }

    public function getElementConfig()
    {
        $config = array_replace_recursive([
            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
            'formElement'   => \Magento\Ui\Component\Form\Element\Checkbox::NAME,
            'component'     => 'Magezon_UiBuilder/js/form/element/single-checkbox',
            'prefer'        => 'checkbox',
            'valueMap'      => [
                'false' => 0,
                'true'  => 1
            ]
        ], (array) $this->getData('config'));

        return [
            'arguments' => [
                'data' => [
                    'config' => $config
                ]
            ]
        ];
    }
}
