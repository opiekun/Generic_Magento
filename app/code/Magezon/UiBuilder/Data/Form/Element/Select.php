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

class Select extends AbstractElement
{
    public function _construct()
    {
        $this->setType('select');
    }

    public function getElementConfig()
    {
        $config = array_replace_recursive([
            'componentType'        => \Magento\Ui\Component\Form\Field::NAME,
            'formElement'          => \Magento\Ui\Component\Form\Element\Select::NAME,
            'component'            => 'Magezon_UiBuilder/js/form/element/ui-select',
            'elementTmpl'          => 'ui/grid/filters/elements/ui-select',
            'disableLabel'         => true,
            'multiple'             => false,
            'selectedPlaceholders' => false
        ], (array) $this->getData('config'));

        if (isset($config['placeholder']) && !isset($config['selectedPlaceholders']['defaultPlaceholder'])) {
            $config['selectedPlaceholders']['defaultPlaceholder'] = $config['placeholder'];
        }

        if (isset($config['additionalClasses'])) {
            $config['additionalClasses'] .= ' uibuilder-select';
        } else {
            $config['additionalClasses'] = ' uibuilder-select';
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
