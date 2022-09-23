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
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Ui\DataProvider\Form;

use Magento\Framework\App\ObjectManager;
use Magezon\Core\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;

class AbstractModifier extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * Container fieldset prefix
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Filesystem
     */
    private $fileInfo;

    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * Retrieve array manager
     *
     * @return ArrayManager
     */
    protected function getArrayManager()
    {
        if (null === $this->arrayManager) {
            $this->arrayManager = ObjectManager::getInstance()->get(
                ArrayManager::class
            );
        }
        return $this->arrayManager;
    }

    /**
     * Get FileInfo instance
     *
     * @return FileInfo
     */
    protected function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(\Magezon\Core\Model\FileInfo::class);
        }
        return $this->fileInfo;
    }

    /**
     * Retrieve scope overridden value
     *
     * @return ScopeOverriddenValue
     */
    protected function getScopeOverriddenValue()
    {
        if (null === $this->scopeOverriddenValue) {
            $this->scopeOverriddenValue = ObjectManager::getInstance()->get(
                ScopeOverriddenValue::class
            );
        }

        return $this->scopeOverriddenValue;
    }
}
