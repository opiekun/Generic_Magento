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

class ContainerGroup extends AbstractElement
{
    public function _construct()
    {
        $this->setType('container');
    }

    public function getElementConfig()
    {
        $config = array_replace_recursive([
            'componentType'     => \Magento\Ui\Component\Container::NAME,
            'formElement'       => \Magento\Ui\Component\Container::NAME,
            'component'         => 'Magento_Ui/js/form/components/group',
            'template'          => 'Magezon_UiBuilder/group/group',
            'fieldTemplate'     => 'Magezon_UiBuilder/form/field',
            'breakLine'         => false,
            'showLabel'         => false,
            'additionalClasses' => ''
        ], (array) $this->getData('config'));
        $config['additionalClasses'] .= ' admin__field-group-columns admin__control-group-equal';
        return [
            'arguments' => [
                'data' => [
                    'config' => $config
                ]
            ]
        ];
    }
}
