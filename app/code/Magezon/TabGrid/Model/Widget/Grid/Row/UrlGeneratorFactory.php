<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_TabGrid
 * @copyright Copyright (C) 2017 Magezon (https://magezon.com)
 */

namespace Magezon\TabGrid\Model\Widget\Grid\Row;

class UrlGeneratorFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new url generator instance
     *
     * @param string $generatorClassName
     * @param array $arguments
     * @return \Magezon\TabGrid\Model\Widget\Grid\Row\UrlGenerator
     * @throws \InvalidArgumentException
     */
    public function createUrlGenerator($generatorClassName, array $arguments = [])
    {
        $rowUrlGenerator = $this->_objectManager->create($generatorClassName, $arguments);
        if (false === $rowUrlGenerator instanceof \Magezon\TabGrid\Model\Widget\Grid\Row\GeneratorInterface) {
            throw new \InvalidArgumentException('Passed wrong parameters');
        }

        return $rowUrlGenerator;
    }
}
