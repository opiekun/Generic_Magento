<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more inmenuation.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Block;

use Magento\Catalog\Model\Category as CategoryModel;

class Menu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magezon_NinjaMenus::menu.phtml';

    /**
     * @var Magezon\NinjaMenus\Model\Menu
     */
    protected $_menu;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Magezon\Builder\Helper\Data
     */
    protected $builderHelper;

    /**
     * @var \Magezon\NinjaMenus\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magezon\NinjaMenus\Helper\Menu
     */
    protected $menuHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                $context                   
     * @param \Magento\Framework\App\Http\Context                             $httpContext               
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory         $pageCollectionFactory     
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory 
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory  
     * @param \Magezon\Core\Helper\Data                                       $coreHelper                
     * @param \Magezon\Builder\Helper\Data                                    $builderHelper             
     * @param \Magezon\NinjaMenus\Helper\Data                                 $dataHelper                
     * @param \Magezon\NinjaMenus\Helper\Menu                                 $menuHelper                
     * @param array                                                           $data                      
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magezon\Core\Helper\Data $coreHelper,
        \Magezon\Builder\Helper\Data $builderHelper,
        \Magezon\NinjaMenus\Helper\Data $dataHelper,
        \Magezon\NinjaMenus\Helper\Menu $menuHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext               = $httpContext;
        $this->pageCollectionFactory     = $pageCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->coreHelper                = $coreHelper;
        $this->builderHelper             = $builderHelper;
        $this->dataHelper                = $dataHelper;
        $this->menuHelper                = $menuHelper;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->getData('template')) {
            $this->setTemplate($this->getData('template'));
        }

        $this->addData(
            [
                'cache_lifetime' => 86400,
                'cache_tags'     => [\Magezon\NinjaMenus\Model\Menu::CACHE_TAG]
            ]
        );
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'NINJAMENUS_MENU',
            $this->_storeManager->getStore()->getId(),
            (int)$this->_storeManager->getStore()->isCurrentlySecure(),
            $this->_design->getDesignTheme()->getId(),
            $this->getCustomerGroupId(),
            $this->coreHelper->serialize($this->getData()),
            'template' => $this->getTemplate()
        ];
    }

    public function getCustomerGroupId()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
    }

    /**
     * Render menu HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->dataHelper->isEnabled()) return;

        $menu   = $this->getCurrentMenu();
        $menuId = $this->getData('menu_id');

        if (!$menu) {
            if ($menuId) {
                $menu = $this->menuHelper->loadMenu($menuId, 'menu_id');
            } else if ($identifier = $this->getData('identifier')) {
                $menu = $this->menuHelper->loadMenu($identifier);
            }
            $this->setCurrentMenu($menu);
        }

        if ($menu && $menu->getId()) {
            return parent::_toHtml();
        }

        return;
    }

    /**
     * @param \Magezon\NinjaMenus\Model\Menu $menu
     */
    public function setCurrentMenu($menu)
    {
        $this->_menu = $menu;
        return $this;
    }

    /**
     * Get current menu
     *
     * @return \Magezon\NinjaMenus\Model\Menu
     */
    public function getCurrentMenu()
    {
        return $this->_menu;
    }

    /**
     * @return string
     */
    public function getProfileHtml()
    {
        $menu = $this->getCurrentMenu();
        $block = $this->builderHelper->prepareProfileBlock(\Magezon\Builder\Block\Profile::class, $menu->getProfile());
        $flatElements = $block->getFlatElements();
        $pageIds = $productIds = $categoryIds = $parents = [];
        foreach ($flatElements as $_element) {
            if ($_element->getType() == 'menu_item') {
                if ($parentId = $_element->getData('parent_id')) {
                    $parents[] = $parentId;
                    $categoryIds[] = $parentId;
                }
                switch ($_element->getData('item_type')) {
                    case 'category':
                        $categoryIds[] = $_element->getData('category_id');
                        break;

                    case 'product':
                        $productIds[] = $_element->getData('product_id');
                        break;

                    case 'page':
                        $pageIds[] = $_element->getData('page_id');
                        break;
                }
            }
        }

        if (!empty($categoryIds)) {
            $storeId = $this->_storeManager->getStore()->getId();
            $categories = $this->categoryCollectionFactory->create();
            $categories->addAttributeToSelect(['name', 'is_active', 'parent_id']);
            $categories->setStoreId($storeId);
            $categories->addIsActiveFilter();
            $categories->addUrlRewriteToResult();
            $categories->setOrder('position', 'ASC');
            $attributes[] = [
                'attribute' => 'entity_id', 'in' => $categoryIds
            ];
            foreach ($parents as $k => $v) {
                $attributes[] = [
                    'attribute' => 'path', 'like' => '%/' . $v . '/%'
                ];
            }
            $categories->addAttributeToFilter($attributes);
            $list = [];
            foreach ($categories as $category) {
                $list[$category->getId()]['category'] = $category;
                $list[$category->getParentId()]['children'][] = &$list[$category->getId()];
            }
            $result = [];
            foreach ($list as $id => $item) {
                if ((in_array($id, $categoryIds) || in_array($id, $parents)) && isset($item['children'])) {
                    $category = $categories->getItemById($id);
                    if ($category) {
                        $category->setSubCategories($item['children']);
                    }
                }
            }
            $block->addGlobalData('category_collection', $categories);
        }

        if (!empty($productIds)) {
            $products = $this->productCollectionFactory->create();
            $products->addFieldToFilter('entity_id', ['in' => $productIds]);
            $products->addUrlRewrite();
            $block->addGlobalData('product_collection', $products);
        }

        if (!empty($pageIds)) {
            $pages = $this->pageCollectionFactory->create();
            $pages->addFieldToFilter('page_id', ['in' => $pageIds]);
            $block->addGlobalData('page_collection', $pages);
        }

        $block->addGlobalData('menu', $menu);
        return $this->dataHelper->filter($block->toHtml());
    }
}
