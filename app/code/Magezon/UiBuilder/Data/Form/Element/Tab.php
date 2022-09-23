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

class Tab extends AbstractElement
{
    public function _construct()
    {
        $this->setType('tab');
    }

    public function getElementConfig()
    {
        $config = array_replace_recursive([
            'formElement'   => \Magento\Ui\Component\Container::NAME,
            'componentType' => \Magento\Ui\Component\Container::NAME,
            'component'     => 'Magezon_UiBuilder/js/form/element/tab'
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
