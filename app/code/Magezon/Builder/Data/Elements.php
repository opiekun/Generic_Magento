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

namespace Magezon\Builder\Data;

use \Magento\Framework\App\ObjectManager;

class Elements
{
    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magezon\Builder\Model\ElementFactory
     */
    protected $elementFactory;

    /**
     * @var array
     */
    protected $sortableElements;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager  
     * @param \Magezon\Builder\Model\ElementFactory     $elementFactory 
     * @param array                                     $elements       
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magezon\Builder\Model\ElementFactory $elementFactory,
        array $elements = []
    ) {
        $this->elements       = array_merge($this->elements, $elements);
        $this->objectManager  = $objectManager;
        $this->elementFactory = $elementFactory;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        if ($this->sortableElements == null && $this->elements) {
            $elements = $this->elements;
            $sortableElements = [];
            foreach ($elements as $type => $data) {
                if (!isset($data['class'])) $data['class'] = 'Magezon\Builder\Data\Element\Element';
                $element = $this->objectManager->create(
                    $data['class']
                )->setType(
                    $type
                )->addData(
                    $data
                );
                $sortableElements[] = $element;
            }
            usort($sortableElements, function($a, $b) {
                return ($a['sortOrder'] > $b['sortOrder']);
            });
            $this->sortableElements = $sortableElements;
        }
        return $this->sortableElements;
    }

    /**
     * @param  string $type
     * @return Magezon\Builder\Data\Element|null
     */
    public function getElement($type)
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element['type'] == $type) {
                return $element;
            }
        }
    }

    /**
     * @param  array|object $data
     * @return \Magezon\Builder\Model\Element
     */
    public function getElementModel($data)
    {
        if (is_array($data)) {
            $element = $this->elementFactory->create();
            $element->setData($data);
        } else {
            $element = $data;
        }
        return $element;
    }
}
