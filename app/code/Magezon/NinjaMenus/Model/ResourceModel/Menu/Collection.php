<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Model\ResourceModel\Menu;

use Magezon\NinjaMenus\Api\Data\MenuInterface;

class Collection extends \Magezon\NinjaMenus\Model\ResourceModel\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'menu_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'ninjamenus_menu_collection';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = 'menu_collection';

    /**
     * Init collection and determine table names
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magezon\NinjaMenus\Model\Menu::class, \Magezon\NinjaMenus\Model\ResourceModel\Menu::class);
        $this->_map['fields']['store']   = 'store_table.store_id';
        $this->_map['fields']['menu_id'] = 'main_table.menu_id';
    }

    /**
     * Before collection load
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_before', [$this->_eventObject => $this]);
        return parent::_beforeLoad();
    }

    /**
     * After collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', [$this->_eventObject => $this]);

        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
        $this->performAfterLoad('mgz_ninjamenus_menu_store', $entityMetadata->getLinkField());

        return parent::_afterLoad();
    }
    
    /**
     * Add active category filter
     *
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', 1);
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_add_is_active_filter',
            [$this->_eventObject => $this]
        );
        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
        $this->joinStoreRelationTable('mgz_ninjamenus_menu_store', $entityMetadata->getLinkField());
    }
}
