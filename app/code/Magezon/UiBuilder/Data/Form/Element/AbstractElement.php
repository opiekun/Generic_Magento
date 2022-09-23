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

use Magezon\UiBuilder\Data\Form\AbstractForm;
use Magento\Framework\UrlInterface;

class AbstractElement extends AbstractForm
{
    /**
     * @var Form
     */
    protected $_form;

    /**
     * @var array
     */
    protected $_elementsIndex = [];

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magezon\UiBuilder\Helper\Data
     */
    protected $builderHelper;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @param Factory                             $factoryElement    
     * @param CollectionFactory                   $factoryCollection 
     * @param \Magento\Backend\Model\UrlInterface $backendUrl        
     * @param \Magezon\UiBuilder\Helper\Data      $builderHelper     
     * @param \Magezon\Core\Helper\Data           $coreHelper        
     * @param array                               $data              
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magezon\UiBuilder\Helper\Data $builderHelper,
        \Magezon\Core\Helper\Data $coreHelper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $data);
        $this->_backendUrl   = $backendUrl;
        $this->builderHelper = $builderHelper;
        $this->coreHelper    = $coreHelper;
        $this->_construct();
    }

    /**
     * Internal constructor, that is called from real constructor
     *
     * Please override this one instead of overriding real __construct constructor
     *
     * @return void
     */
    public function _construct()
    {
    }

    /**
     * Add form element
     *
     * @param AbstractElement $element
     * @param bool $after
     * @return Form
     */
    public function addElement(AbstractElement $element, $after = false)
    {
        $this->checkElementId($element->getId());
        parent::addElement($element, $after);
        return $this;
    }

    /**
     * @param string $elementId
     * @return bool
     * @throws \Exception
     */
    public function checkElementId($elementId)
    {
        $elements = $this->getElements();
        foreach ($elements as $_elm) {
            if ($_elm->getId() == $elementId) {
                throw new \InvalidArgumentException('Element with id "' . $elementId . '" already exists');
            }
        }
    }

    /**
     * Check existing element
     *
     * @param   string $elementId
     * @return  bool
     */
    protected function _elementIdExists($elementId)
    {
        return isset($this->_elementsIndex[$elementId]);
    }

    /**
     * @param AbstractElement $element
     * @return $this
     */
    public function addElementToCollection($element)
    {
        $this->_elementsIndex[$element->getId()] = $element;
        return $this;
    }

    /**
     * @param AbstractForm $form
     * @return $this
     */
    public function setForm($form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Get form
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Set the Id.
     *
     * @param string|int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setData('field_id', $id);
        return $this;
    }

    /**
     * Get id.
     *
     * @return string|int
     */
    public function getId()
    {
        return $this->getData('field_id');
    }

    /**
     * Set the type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->setData('type', $type);
        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData('type');
    }
}
