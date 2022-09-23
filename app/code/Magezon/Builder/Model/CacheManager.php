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

namespace Magezon\Builder\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;

class CacheManager
{
    /**
     * Cache group Tag
     */
    const CACHE_GROUP = \Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER;

    /**
     * Cache tag used to distinguish the cache type from all other cache
     */
    const CACHE_TAG   = \Magento\Framework\App\Cache\Type\Config::CACHE_TAG;

    /**
     * Prefix for cache key of block
     */
    const CACHE_KEY_PREFIX = 'MAGEZON_BUILDER_';

    /**
     * @var CacheInterface
     */
    private $_cacheManager;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $cacheState;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState 
     * @param \Magezon\Core\Helper\Data                   $coreHelper 
     */
	public function __construct(
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magezon\Core\Helper\Data $coreHelper
	) {
        $this->cacheState = $cacheState;
        $this->coreHelper = $coreHelper;
	}

    /**
     * @param  string $key 
     * @return string      
     */
    public function getCacheKey($key)
    {
        return self::CACHE_KEY_PREFIX . $key;
    }

	public function getFromCache($key)
	{
        if ($this->cacheState->isEnabled(self::CACHE_GROUP)) {
            $key = $this->getCacheKey($key);
    		$config = $this->getCacheManager()->load($key);
            if ($config) {
                return $this->coreHelper->unserialize($config);
            }
        }
	}

	public function saveToCache($key, $value)
	{
        if ($this->cacheState->isEnabled(self::CACHE_GROUP)) {
            $key = $this->getCacheKey($key);
    		$this->getCacheManager()->save(
                $this->coreHelper->serialize($value),
                $key,
                [
                    self::CACHE_TAG
                ]
            );
        }
	}

    /**
     * Retrieve cache interface
     *
     * @return CacheInterface
     * @deprecated 101.0.3
     */
    private function getCacheManager()
    {
        if (!$this->_cacheManager) {
            $this->_cacheManager = ObjectManager::getInstance()
                ->get(CacheInterface::class);
        }
        return $this->_cacheManager;
    }
}