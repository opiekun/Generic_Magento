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

namespace Magezon\Builder\Data\Form\Element;

use Magento\Framework\ObjectManagerInterface;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Standard library element types
     *
     * @var array
     */
    protected $_standardTypes = [
        'fieldset',
        'element',
        'container',
        'containerGroup',
        'text',
        'select',
        'boolean',
        'editor',
        'code',
        'textarea',
        'checkbox',
        'radio',
        'image',
        'dynamicRows',
        'date',
        'actionDelete',
        'color',
        'tab',
        'html',
        'hidden',
        'multiselect',
        'multicheckbox',
        'toggle',
        'uiSelect',
        'number',
        'emptyElement',
        'icon',
        'actionDelete',
        'condition',
        'link'
    ];

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    public function create($elementType, array $config = [])
    {
        if (in_array($elementType, $this->_standardTypes)) {
            $className = 'Magezon\Builder\Data\Form\Element\\' . ucfirst($elementType);
        } else {
            $className = $elementType;
        }
        if (isset($config['default'])) {
            $config['default'] = '';
        }
        $element = $this->_objectManager->create($className, $config);
        if (!$element instanceof \Magezon\Builder\Data\Form\Element\AbstractElement) {
            $className . ' doessn\'n not extend \Magezon\Builder\Data\Form\Element\AbstractElement';
        }
        return $element;
    }
}
