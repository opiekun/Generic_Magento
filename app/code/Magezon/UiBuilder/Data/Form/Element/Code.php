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

class Code extends AbstractElement
{
    public function _construct()
    {
        $this->setType('code');
    }

    public function getElementConfig()
    {
        $config = array_replace_recursive([
            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
            'component'     => 'Magezon_UiBuilder/js/form/element/code-mirror',
            'formElement'   => \Magento\Ui\Component\Form\Element\Textarea::NAME,
            'rows'          => 5
        ], (array) $this->getData('config'));

        if (isset($config['additionalClasses'])) {
            $config['additionalClasses'] .= ' uibuilder-codemirror';
        } else {
            $config['additionalClasses'] = 'uibuilder-codemirror';
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => $config
                ]
            ]
        ];
    }
}
