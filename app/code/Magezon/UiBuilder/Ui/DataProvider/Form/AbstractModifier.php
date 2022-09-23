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

namespace Magezon\UiBuilder\Ui\DataProvider\Form;

use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magezon\UiBuilder\Data\Form\Element\Factory;
use Magezon\UiBuilder\Data\Form\Element\CollectionFactory;

class AbstractModifier implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    const GROUP_CONDITIONAL_SCOPE = 'data';

    /**
     * @var Factory
     */
    protected $_factoryElement;

    /**
     * @var CollectionFactory
     */
    protected $_factoryCollection;

    /**
     * @var BlueFormBuilder\Core\Data\Form\Element\Data\Form\Element\Collection
     */
    protected $_elements;

    protected $group;

    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * @param Factory           $factoryElement    
     * @param CollectionFactory $factoryCollection 
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection
    ) {
        $this->_factoryElement    = $factoryElement;
        $this->_factoryCollection = $factoryCollection;
    }

    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get elements collection
     *
     * @return Collection
     */
    public function getElements()
    {
        if (empty($this->_elements)) {
            $this->_elements = $this->_factoryCollection->create();
        }

        return $this->_elements;
    }

    public function addChildren($elementId, $type, $config = [])
    {
        if (isset($this->_types[$type])) {
            $type = $this->_types[$type];
        }
        $element = $this->_factoryElement->create($type, ['data' => ['config' => $config]]);
        $element->setId($elementId);
        $this->addElement($element);
        return $element;
    }

    public function addFieldset($elementId, $config = [])
    {
        $element = $this->_factoryElement->create('fieldset', ['data' => ['config' => $config]]);
        $element->setId($elementId);
        $this->addElement($element);
        return $element;
    }

    public function addContainer($elementId, $config = [])
    {
        $element = $this->_factoryElement->create('container', ['data' => ['config' => $config]]);
        $element->setId($elementId);
        $this->addElement($element);
        return $element;
    }

    public function addContainerGroup($elementId, $config = [])
    {
        $element = $this->_factoryElement->create('containerGroup', ['data' => ['config' => $config]]);
        $element->setId($elementId);
        $this->addElement($element);
        return $element;
    }

    public function addElement($element)
    {
        $element->setForm($this);
        $this->getElements()->add($element);
        return $this;
    }

    /**
     * Retrieve scope overridden value
     *
     * @return ScopeOverriddenValue
     * @deprecated 101.1.0
     */
    private function getScopeOverriddenValue()
    {
        if (null === $this->scopeOverriddenValue) {
            $this->scopeOverriddenValue = \Magento\Framework\App\ObjectManager::getInstance()->get(
                ScopeOverriddenValue::class
            );
        }

        return $this->scopeOverriddenValue;
    }

    public function getAttribute($attributeCode) {
        $attributes = $this->getCurrentForm()->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getAttributeCode() == $attributeCode) {
                return $attribute;
            }
        }
        return false;
    }

    /**
     * @param  Get all modal children
     * @return array
     */
    public function getChildren($elements = null)
    {
        if (!$elements) {
            $elements = $this->getElements();
        }
        $children = [];
        foreach ($elements as $_element) {
            $id            = $_element->getId();
            $children[$id] = $_element->getElementConfig();
            if ($_element->getElements()->count()) {
                $children[$id]['children'] = $this->getChildren($_element->getElements());
            }
        }
        return $children;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
