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

class Sources
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $types = [];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager 
     * @param array                                     $types         
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $types = []
    ) {
        $this->objectManager = $objectManager;
        $this->types         = $types;
    }

    /**
     * @return array
     */
    public function getSource($type)
    {
        if (isset($this->types[$type])) {
            $source = $this->objectManager->create(
                $this->types[$type]
            );
            if (!$source instanceof \Magezon\Builder\Model\Source\ListInterface) {
                throw new \InvalidArgumentException(
                    $this->types[$type] . ' does not implement interface ' . \Magezon\Builder\Model\Source\ListInterface::class
                );
            }
            return $source;
        }
    }
}
