<?php

namespace WeltPixel\GoogleTagManager\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CACHE_ID_CATEGORIES = 'weltpixel_gtm_cached_categories';
    /**
     * @var array
     */
    protected $_gtmOptions;

    /**
     * @var array
     */
    protected $_brandOptions;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $storeCategories;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $resourceCategory;

    /**
     * @var \Magento\Framework\Escaper $escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Checkout\Model\Session\SuccessValidator
     */
    protected $checkoutSuccessValidator;

    /**
     * @var \WeltPixel\GoogleTagManager\Model\Storage
     */
    protected $storage;

    /**
     * \Magento\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $configurationHelper;

    /**
     * @var \Magento\Catalog\Api\ProductCustomOptionRepositoryInterface
     */
    protected $productOptionRepository;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \WeltPixel\GoogleTagManager\Model\Dimension
     */
    protected $dimensionModel;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var  \Magento\Framework\App\Cache\StateInterface
     */
    protected $cacheState;

    protected const XML_PATH_DEV_MOVE_JS_TO_BOTTOM = 'dev/js/move_script_to_bottom';

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category $resourceCategory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Checkout\Model\Session\SuccessValidator $checkoutSuccessValidator
     * @param \WeltPixel\GoogleTagManager\Model\Storage $storage
     * @param \Magento\Cookie\Helper\Cookie $cookieHelper
     * @param \Magento\Catalog\Helper\Product\Configuration $configurationHelper
     * @param \Magento\Catalog\Api\ProductCustomOptionRepositoryInterface $productOptionRepository
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \WeltPixel\GoogleTagManager\Model\Dimension $dimensionModel
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category $resourceCategory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Checkout\Model\Session\SuccessValidator $checkoutSuccessValidator,
        \WeltPixel\GoogleTagManager\Model\Storage $storage,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Magento\Catalog\Helper\Product\Configuration $configurationHelper,
        \Magento\Catalog\Api\ProductCustomOptionRepositoryInterface $productOptionRepository,
        \Magento\Framework\View\Page\Config $pageConfig,
        \WeltPixel\GoogleTagManager\Model\Dimension $dimensionModel,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState
    ) {
        parent::__construct($context);
        $this->_gtmOptions = $this->scopeConfig->getValue('weltpixel_googletagmanager', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->blockFactory = $blockFactory;
        $this->registry = $registry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->resourceCategory = $resourceCategory;
        $this->escaper = $escaper;
        $this->storeCategories = [];
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->checkoutSuccessValidator = $checkoutSuccessValidator;
        $this->storage = $storage;
        $this->cookieHelper = $cookieHelper;
        $this->configurationHelper = $configurationHelper;
        $this->productOptionRepository = $productOptionRepository;
        $this->pageConfig = $pageConfig;
        $this->dimensionModel = $dimensionModel;
        $this->cache = $cache;
        $this->cacheState = $cacheState;
    }

    /**
     * Get all categories id, name for the current store view
     */
    private function _populateStoreCategories()
    {
        if (!$this->isEnabled() || !empty($this->storeCategories)) {
            return;
        }

        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        $storeId = $this->storeManager->getStore()->getStoreId();

        $isWpGtmCacheEnabled = $this->cacheState->isEnabled(\WeltPixel\GoogleTagManager\Model\Cache\Type::TYPE_IDENTIFIER);
        $cacheKey = self::CACHE_ID_CATEGORIES . '-' . $rootCategoryId . '-' . $storeId;
        if ($isWpGtmCacheEnabled) {
            $this->_eventManager->dispatch('weltpixel_googletagmanager_cachekey_after', ['cache_key' => $cacheKey]);

            $cachedCategoriesData = $this->cache->load($cacheKey);
            if ($cachedCategoriesData) {
                $this->storeCategories = json_decode($cachedCategoriesData, true);
                return;
            }
        }

        $categories = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToFilter('path', ['like' => "1/{$rootCategoryId}%"])
            ->addAttributeToSelect('name');

        foreach ($categories as $categ) {
            $this->storeCategories[$categ->getData('entity_id')] = [
                'name' => $categ->getData('name'),
                'path' => $categ->getData('path')
            ];
        }

        if ($isWpGtmCacheEnabled) {
            $cachedCategories = json_encode($this->storeCategories);
            $this->cache->save($cachedCategories, $cacheKey, [\WeltPixel\GoogleTagManager\Model\Cache\Type::CACHE_TAG]);
        }
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return !$this->cookieHelper->isUserNotAllowSaveCookie() && $this->_gtmOptions['general']['enable'];
    }

    /**
     * @return boolean
     */
    public function isProductClickTrackingEnabled()
    {
        return ($this->isProductClickTrackingOnlyForGtmEnabled()) ||
            (!$this->cookieHelper->isUserNotAllowSaveCookie() && $this->scopeConfig->getValue('weltpixel_ga4/general/product_click_tracking', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    /**
     * @return boolean
     */
    public function isProductClickTrackingOnlyForGtmEnabled()
    {
        return (!$this->cookieHelper->isUserNotAllowSaveCookie() && $this->_gtmOptions['general']['product_click_tracking']);
    }

    /**
     * @return bool
     */
    public function isCookieRestrictionModeEnabled()
    {
        return $this->scopeConfig->getValue(\Magento\Cookie\Helper\Cookie::XML_PATH_COOKIE_RESTRICTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getBrandAttribute()
    {
        return $this->_gtmOptions['general']['brand_attribute'];
    }

    /**
     * @return boolean
     */
    public function trackPromotions()
    {
        return $this->_gtmOptions['general']['promotion_tracking'];
    }

    /**
    * @return int
    */
    public function getPersistentStorageExpiryTime()
    {
        return $this->_gtmOptions['general']['persistentlayer_expiry'];
    }

    /**
     * @return boolean
     */
    public function excludeTaxFromTransaction()
    {
        return $this->_gtmOptions['general']['exclude_tax_from_transaction'];
    }

    /**
     * @return boolean
     */
    public function excludeShippingFromTransaction()
    {
        return $this->_gtmOptions['general']['exclude_shipping_from_transaction'];
    }

    /**
     * @return boolean
     */
    public function excludeShippingFromTransactionIncludingTax()
    {
        return $this->_gtmOptions['general']['exclude_shipping_from_transaction_including_tax'];
    }

    /**
     * @return boolean
     */
    public function excludeFreeOrderFromPurchaseForGoogleAnalytics()
    {
        return $this->_gtmOptions['general']['exclude_free_purchase'];
    }

    /**
     * @return boolean
     */
    public function excludeFreeOrderFromAdwordsConversionTracking()
    {
        return $this->_gtmOptions['adwords_conversion_tracking']['exclude_free_purchase'];
    }

    /**
     * @return boolean
     */
    public function excludeFreeOrderFromAdwordsRemarketing()
    {
        return $this->_gtmOptions['adwords_remarketing']['exclude_free_purchase'];
    }

    /**
     * return child or parent string
     * @return string
     */
    public function getParentOrChildIdUsage()
    {
        return $this->_gtmOptions['general']['parent_vs_child'];
    }

    /**
     * @return bool
     */
    public function getSecureCookiesFlag()
    {
        return $this->_gtmOptions['general']['secure_cookies'];
    }

    /**
     * @return int
     */
    public function getImpressionChunkSize()
    {
        return $this->_gtmOptions['general']['impression_chunk_size'];
    }

    /**
     * @return string
     */
    public function getGtmCodeSnippet()
    {
        return trim($this->_gtmOptions['general']['gtm_code']);
    }

    /**
     * @return string
     */
    public function getGtmCodeSnippetForHead()
    {
        $gtmCodeSnippet = $this->getGtmCodeSnippet();
        return $gtmCodeSnippet;
    }

    /**
     * @return string
     */
    public function getGtmNonJsCodeSnippet()
    {
        return trim($this->_gtmOptions['general']['gtm_nonjs_code']);
    }

    /**
     * @return string
     */
    public function getCustomSuccessPagePaths()
    {
        return trim($this->_gtmOptions['general']['success_page_paths']);
    }

    /**
     * @return string
     */
    public function getDataLayerScript()
    {
        $script = '';

        if (!($block = $this->createBlock('Core', 'datalayer.phtml'))) {
            return $script;
        }

        $block->setNameInLayout('wp.gtm.datalayer.scripts');

        $this->addDefaultInformation();
        $this->addCategoryPageInformation();
        $this->addSearchResultPageInformation();
        $this->addProductPageInformation();
        $this->addCartPageInformation();
        $this->addCheckoutInformation();
        $this->addOrderInformation();

        $html = $block->toHtml();

        return $html;
    }

    /**
     * @param $blockName
     * @param $template
     * @return bool
     */
    protected function createBlock($blockName, $template)
    {
        if ($block = $this->blockFactory->createBlock('\WeltPixel\GoogleTagManager\Block\\' . $blockName)
            ->setTemplate('WeltPixel_GoogleTagManager::' . $template)
        ) {
            return $block;
        }

        return false;
    }

    /**
     * Set default gtm options based on configuration
     */
    public function addDefaultInformation()
    {
        $actionName = $this->_request->getFullActionName();

        if ($this->isCustomDimensionPageNameEnabled()) {
            if ($this->pageConfig) {
                $pageTitle = $this->pageConfig->getTitle()->get();
                $this->storage->setData('pageName', $pageTitle);
            }
        }

        if ($this->isCustomDimensionPageTypeEnabled()) {
            $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_OTHER;
            switch ($actionName) {
                case 'cms_index_index':
                    $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_HOME;
                    break;
                case 'checkout_index_index':
                case 'firecheckout_index_index':
                    $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_CART;
                    break;
            }
            $this->storage->setData('pageType', $pageType);
        }

        if ($this->isAdWordsRemarketingEnabled()) {
            $remarketingData = [];
            switch ($actionName) {
                case 'cms_index_index':
                    $remarketingData['ecomm_pagetype'] = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_HOME;
                    break;
                case 'checkout_index_index':
                case 'firecheckout_index_index':
                    $remarketingData['ecomm_pagetype'] = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_CART;
                    break;
                default:
                    $remarketingData['ecomm_pagetype'] = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_OTHER;
                    break;
            }
            $this->storage->setData('google_tag_params', $remarketingData);
        }
    }

    /**
     * Set category page product impressions
     */
    public function addCategoryPageInformation()
    {
        $currentCategory = $this->getCurrentCategory();

        if (!empty($currentCategory)) {
            $categoryBlock = $this->createBlock('Category', 'category.phtml');

            if ($categoryBlock) {
                $categoryBlock->setCurrentCategory($currentCategory);
                $categoryBlock->toHtml();
            }

            if ($this->isCustomDimensionPageTypeEnabled()) {
                $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_CATEGORY;
                $this->storage->setData('pageType', $pageType);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * Set search result page product impressions
     */
    public function addSearchResultPageInformation()
    {
        $moduleName = $this->_request->getModuleName();
        $controllerName = $this->_request->getControllerName();
        $listPrefix = '';
        if ($controllerName == 'advanced') {
            $listPrefix = __('Advanced');
        }

        if ($moduleName == 'catalogsearch') {
            if ($this->isCustomDimensionPageTypeEnabled()) {
                $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_SEARCHRESULTS;
                $this->storage->setData('pageType', $pageType);
            }

            $searchBlock = $this->createBlock('Search', 'search.phtml');

            if ($searchBlock) {
                $searchBlock->setListPrefix($listPrefix);
                return $searchBlock->toHtml();
            }
        }
    }

    /**
     * Set product page detail infromation
     */
    public function addProductPageInformation()
    {
        $currentProduct = $this->getCurrentProduct();

        if (!empty($currentProduct)) {
            $productBlock = $this->createBlock('Product', 'product.phtml');

            if ($productBlock) {
                $productBlock->setCurrentProduct($currentProduct);
                $productBlock->toHtml();
            }

            if ($this->isCustomDimensionPageTypeEnabled()) {
                $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_PRODUCT;
                $this->storage->setData('pageType', $pageType);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Set crossell productImpressions
     */
    public function addCartPageInformation()
    {
        $requestPath = $this->_request->getModuleName() .
            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
            DIRECTORY_SEPARATOR . $this->_request->getActionName();

        if ($requestPath == 'checkout/cart/index') {
            $cartBlock = $this->createBlock('Cart', 'cart.phtml');

            if ($cartBlock) {
                $quote = $this->checkoutSession->getQuote();
                $cartBlock->setQuote($quote);
                $cartBlock->toHtml();
            }

            if ($this->isCustomDimensionPageTypeEnabled()) {
                $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_CART;
                $this->storage->setData('pageType', $pageType);
            }
        }
    }

    public function addCheckoutInformation()
    {
        $requestPath = $this->_request->getModuleName() .
            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
            DIRECTORY_SEPARATOR . $this->_request->getActionName();

        if ($requestPath == 'checkout/index/index' || $requestPath == 'firecheckout/index/index') {
            $checkoutBlock = $this->createBlock('Checkout', 'checkout.phtml');

            if ($checkoutBlock) {
                $quote = $this->checkoutSession->getQuote();
                $checkoutBlock->setQuote($quote);
                $checkoutBlock->toHtml();
            }

            if ($this->isCustomDimensionPageTypeEnabled()) {
                $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_CHECKOUT;
                $this->storage->setData('pageType', $pageType);
            }
        }
    }

    /**
     * Set purchase details
     */
    public function addOrderInformation()
    {
        $lastOrderId = $this->checkoutSession->getLastOrderId();
        $requestPath = $this->_request->getModuleName() .
            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
            DIRECTORY_SEPARATOR . $this->_request->getActionName();

        $successPagePaths = [
            'checkout/onepage/success'
        ];

        $customSuccessPagePaths = trim($this->getCustomSuccessPagePaths());

        if (strlen($customSuccessPagePaths)) {
            $successPagePaths = array_merge($successPagePaths, explode(",", $customSuccessPagePaths));
        }

        if (!in_array($requestPath, $successPagePaths) || !$lastOrderId) {
            return;
        }

        $orderBlock = $this->createBlock('Order', 'order.phtml');
        if ($orderBlock) {
            $order = $this->orderRepository->get($lastOrderId);
            $orderBlock->setOrder($order);
            $orderBlock->toHtml();
        }

        if ($this->isCustomDimensionPageTypeEnabled()) {
            $pageType = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_PURCHASE;
            $this->storage->setData('pageType', $pageType);
        }
    }

    /**
     * @return string
     */
    public function getAffiliationName()
    {
        return $this->storeManager->getWebsite()->getName() . ' - ' .
            $this->storeManager->getGroup()->getName() . ' - ' .
            $this->storeManager->getStore()->getName();
    }

    /**
     * @param $productOptions
     * @param $productType
     * @return bool|string
     */
    public function checkVariantForProductOptions($productOptions, $productType)
    {
        $variant = [];
        if ($productType == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            if (isset($productOptions['attributes_info'])) {
                foreach ($productOptions['attributes_info'] as $attribute) {
                    $variant[] = $attribute['label'] . ": " . $attribute['value'];
                }
            }
        }

        // Variant for Custom Options
        if (isset($productOptions['options'])) {
            foreach ($productOptions['options'] as $option) {
                $variant[] = $option['label'] . ": " . $option['print_value'];
            }
        }

        if ($variant) {
            return implode(' | ', $variant);
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $buyRequest
     * @param \Magento\Wishlist\Model\Item $wishlistItem
     * @param boolean $checkForCustomOptions
     * @return bool|string
     */
    public function checkVariantForProduct($product, $buyRequest = [], $wishlistItem = null, $checkForCustomOptions = false)
    {
        $variant = [];

        /** get the configurable products variants, options */
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $options = $product->getTypeInstance(true)->getSelectedAttributesInfo($product);
            foreach ($options as $option) {
                $variant[] = $option['label'] . ": " . $option['value'];
            }

            if (!$variant && isset($buyRequest['super_attribute'])) {
                $superAttributeLabels = [];
                $superAttributeOptions = [];
                $_attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
                foreach ($_attributes as $_attribute) {
                    $superAttributeLabels[$_attribute['attribute_id']] = $_attribute['label'];
                    foreach ($_attribute->getOptions() as $option) {
                        $superAttributeOptions[$_attribute['attribute_id']][$option['value_index']] = $option['store_label'];
                    }
                }

                foreach ($buyRequest['super_attribute'] as $id => $value) {
                    $variant[] = $superAttributeLabels[$id] . ": " . $superAttributeOptions[$id][$value];
                }
            }
        }

        $customOptionFound = false;
        /** This is for the custom options for products */
        $_customOptions = $product->getTypeInstance(true)->getOrderOptions($product);
        if (array_key_exists('options', $_customOptions)) {
            foreach ($_customOptions['options'] as $option) {
                $customOptionFound = true;
                $variant[] = $option['label'] . ": " . $option['print_value'];
            }
        }

        if ($wishlistItem && !$customOptionFound) {
            $options = $this->configurationHelper->getOptions($wishlistItem);
            foreach ($options as $customOption) {
                if (isset($customOption['print_value'])) {
                    $variant[] = $customOption['label'] . ": " . $customOption['print_value'];
                }
            }
        }

        /** Wishlist add to cart with not preselected custom options */
        if ($checkForCustomOptions && isset($buyRequest['options'])) {
            $productOptions = $this->productOptionRepository->getProductOptions($product);
            $productCustomOptionsLabel = [];
            $productCustomOptionsValues = [];
            foreach ($productOptions as $option) {
                $productCustomOptionsLabel[$option['option_id']] = $option['title'];
                if ($option->hasValues()) {
                    $values = $option->getValues();
                    foreach ($values as $value) {
                        $productCustomOptionsValues[$option['option_id']][$value->getOptionTypeId()] = $value->getTitle();
                    }
                }
            }

            foreach ($buyRequest['options'] as $optionId => $optionValues) {
                if (is_array($optionValues)) {
                    $optValue = [];
                    foreach ($optionValues as $value) {
                        $optValue[] = $productCustomOptionsValues[$optionId][$value];
                    }
                    $variant[] = $productCustomOptionsLabel[$optionId] . ": " . implode(',', $optValue);
                } elseif (isset($productCustomOptionsValues[$optionId])) {
                    $variant[] = $productCustomOptionsLabel[$optionId] . ": " . $productCustomOptionsValues[$optionId][$optionValues];
                } else {
                    $variant[] = $productCustomOptionsLabel[$optionId] . ": " . $optionValues;
                }
            }
        }

        if ($variant) {
            return implode(' | ', $variant);
        }

        return false;
    }

    /**
     * @param int $qty
     * @param \Magento\Catalog\Model\Product $product
     * @param array $buyRequest
     * @param boolean $checkForCustomOptions
     * @return array
     */
    public function addToCartPushData($qty, $product, $buyRequest = [], $checkForCustomOptions = false)
    {
        $result = [];

        $result['event'] = 'addToCart';
        $result['eventLabel'] = html_entity_decode($product->getName());
        $result['ecommerce'] = [];
        $result['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $result['ecommerce']['add'] = [];
        $result['ecommerce']['add']['products'] = [];

        $productData = [];
        $productData['name'] = html_entity_decode($product->getName());
        $productData['id'] = $this->getGtmProductId($product);
        if ($this->checkoutSession->getLastProductPrice()) {
            $productData['price'] = number_format($this->checkoutSession->getLastProductPrice(), 2, '.', '');
            $this->checkoutSession->setLastProductPrice(null);
        } else {
            $productData['price'] = number_format($product->getPriceInfo()->getPrice('final_price')->getValue(), 2, '.', '');
        }

        if ($this->isBrandEnabled()) {
            $productData['brand'] = $this->getGtmBrand($product);
        }

        $categoryName = $this->getGtmCategoryFromCategoryIds($product->getCategoryIds());
        $productData['category'] = $categoryName;
        $productData['list'] = $categoryName;
        $productData['quantity'] = $qty;

        /**  Set the custom dimensions */
        $customDimensions = $this->dimensionModel->getProductDimensions($product, $this);
        foreach ($customDimensions as $name => $value) :
            $productData[$name] = $value;
        endforeach;

        if ($this->isVariantEnabled()) {
            $variant = $this->checkVariantForProduct($product, $buyRequest, null, $checkForCustomOptions);
            if ($variant) {
                $productData['variant'] = $variant;
            }
        }

        $result['ecommerce']['add']['products'][] = $productData;

        return $result;
    }

    /**
     * @param array $currentAddToCartData
     * @param array $addToCartPushData
     * @return array
     */
    public function mergeAddToCartPushData($currentAddToCartData, $addToCartPushData)
    {
        if (!is_array($currentAddToCartData)) {
            $currentAddToCartData = $addToCartPushData;
        } else {
            $currentAddToCartData['eventLabel'] = $currentAddToCartData['eventLabel'] . ', ' . $addToCartPushData['eventLabel'];
            $currentAddToCartData['ecommerce']['add']['products'][] = $addToCartPushData['ecommerce']['add']['products'][0];
        }

        return $currentAddToCartData;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Returns the product id or sku based on the backend settings
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGtmProductId($product)
    {
        $idOption = $this->_gtmOptions['general']['id_selection'];
        $gtmProductId = '';

        switch ($idOption) {
            case 'sku':
                $gtmProductId = $product->getData('sku');
                break;
            case 'id':
            default:
                $gtmProductId = $product->getId();
                break;
        }

        return $gtmProductId;
    }

    /**
     * Get the product id or sku for order item
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string
     */
    public function getGtmOrderItemId($item)
    {
        $idOption = $this->_gtmOptions['general']['id_selection'];
        $gtmProductId = '';

        switch ($idOption) {
            case 'sku':
                $gtmProductId = $item->getProduct()->getData('sku');//$item->getSku();
                break;
            case 'id':
            default:
                $gtmProductId = $item->getProductId();
                break;
        }

        return $gtmProductId;
    }

    /**
     * @return boolean
     */
    public function isBrandEnabled()
    {
        return $this->_gtmOptions['general']['enable_brand'];
    }

    /**
     * @return mixed
     */
    public function isVariantEnabled()
    {
        return $this->_gtmOptions['general']['enable_variant'];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGtmBrand($product)
    {
        $gtmBrand = '';
        if ($this->isBrandEnabled()) {
            $brandAttribute = $this->_gtmOptions['general']['brand_attribute'];
            $brandOptions = $this->getBrandOptions($product, $brandAttribute);

            $frontendValue =  $product->getData($brandAttribute);
            if (is_array($frontendValue)) {
                $result = [];
                foreach ($frontendValue as $value) {
                    $result[] = ($brandOptions[$value]) ? $brandOptions[$value] : null;
                }
                $gtmBrand = implode(',', array_filter($result));
            } elseif (isset($brandOptions[$frontendValue])) {
                $gtmBrand = $brandOptions[$frontendValue];
            }
        }

        return $gtmBrand;
    }

    /**
     * @param  \Magento\Catalog\Model\Product $product
     * @param string $brand
     * @return array
     */
    protected function getBrandOptions($product, $brand)
    {
        if (empty($this->_brandOptions)) {
            try {
                $options = $product->getResource()->getAttribute($brand)->getSource()->getAllOptions();
                foreach ($options as $option) {
                    $this->_brandOptions[$option['value']] = $option['label'];
                }
            } catch (Exception $ex) {
            }
        }

        return $this->_brandOptions;
    }

    /**
     * @return string
     */
    public function getOrderTotalCalculation()
    {
        return $this->_gtmOptions['general']['order_total_calculation'];
    }

    /**
     * Returns category tree path
     * @param array $categoryIds
     * @return string
     */
    public function getGtmCategoryFromCategoryIds($categoryIds)
    {
        if (!count($categoryIds)) {
            return '';
        }

        if (empty($this->storeCategories)) {
            $this->_populateStoreCategories();
        }

        $categoryId = $categoryIds[0];

        $categoryPath = '';
        if (isset($this->storeCategories[$categoryId])) {
            $categoryPath = $this->storeCategories[$categoryId]['path'];
        }

        return $this->_buildCategoryPath($categoryPath);
    }

    /**
     * @param string $categoryPath
     * @return string
     */
    private function _buildCategoryPath($categoryPath)
    {
        $categIds = explode('/', $categoryPath);
        $ignoreCategories = 2;
        if (count($categIds) < 3) {
            $ignoreCategories = 1;
        }
        /* first 2 categories can be ignored, or 1st if root category */
        $categoriIds = array_slice($categIds, $ignoreCategories);
        $categoriesWithNames = [];

        foreach ($categoriIds as $categoriId) {
            if (isset($this->storeCategories[$categoriId])) {
                $categoriesWithNames[] = $this->storeCategories[$categoriId]['name'];
            }
        }

        return implode('/', $categoriesWithNames);
    }

    /**
     * @param int $qty
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return array
     */
    public function removeFromCartPushData($qty, $product, $quoteItem)
    {
        $result = [];

        $result['event'] = 'removeFromCart';
        $result['eventLabel'] = html_entity_decode($product->getName());
        $result['ecommerce'] = [];
        $result['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $result['ecommerce']['remove'] = [];
        $result['ecommerce']['remove']['products'] = [];

        $productData = [];
        $productData['name'] = html_entity_decode($product->getName());
        $productData['id'] = $this->getGtmProductId($product);
        $productData['price'] = number_format($quoteItem->getPrice(), 2, '.', '');
        if ($this->isBrandEnabled()) {
            $productData['brand'] = $this->getGtmBrand($product);
        }

        $productData['category'] = $this->getGtmCategoryFromCategoryIds($product->getCategoryIds());
        $productData['quantity'] = $qty;

        if ($this->isVariantEnabled()) {
            $productFromQuote = $quoteItem->getProduct();
            $variant = $this->checkVariantForProduct($productFromQuote);
            if ($variant) {
                $productData['variant'] = $variant;
            }
        }

        $result['ecommerce']['remove']['products'][] = $productData;

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $buyRequest
     * @param \Magento\Wishlist\Model\Item $wishlistItem
     * @return array
     */
    public function addToWishListPushData($product, $buyRequest, $wishlistItem)
    {
        $result = [];

        $result['event'] = 'addToWishlist';
        $result['eventLabel'] = html_entity_decode($product->getName());
        $result['ecommerce'] = [];
        $result['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $result['ecommerce']['add'] = [];
        $result['ecommerce']['add']['products'] = [];

        $productData = [];
        $productData['name'] = html_entity_decode($product->getName());
        $productData['id'] = $this->getGtmProductId($product);
        $productData['price'] = number_format($product->getPriceInfo()->getPrice('final_price')->getValue(), 2, '.', '');
        if ($this->isBrandEnabled()) {
            $productData['brand'] = $this->getGtmBrand($product);
        }

        $productData['category'] = $this->getGtmCategoryFromCategoryIds($product->getCategoryIds());

        /**  Set the custom dimensions */
        $customDimensions = $this->dimensionModel->getProductDimensions($product, $this);
        foreach ($customDimensions as $name => $value) :
            $productData[$name] = $value;
        endforeach;

        if ($this->isVariantEnabled()) {
            $variant = $this->checkVariantForProduct($product, $buyRequest, $wishlistItem);
            if ($variant) {
                $productData['variant'] = $variant;
            }
        }

        $result['ecommerce']['add']['products'][] = $productData;

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function addToComparePushData($product)
    {
        $result = [];

        $result['event'] = 'addToCompare';
        $result['eventLabel'] = html_entity_decode($product->getName());
        $result['ecommerce'] = [];
        $result['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $result['ecommerce']['add'] = [];
        $result['ecommerce']['add']['products'] = [];

        $productData = [];
        $productData['name'] = html_entity_decode($product->getName());
        $productData['id'] = $this->getGtmProductId($product);
        $productData['price'] = number_format($product->getPriceInfo()->getPrice('final_price')->getValue(), 2, '.', '');
        if ($this->isBrandEnabled()) {
            $productData['brand'] = $this->getGtmBrand($product);
        }

        $productData['category'] = $this->getGtmCategoryFromCategoryIds($product->getCategoryIds());

        $result['ecommerce']['add']['products'][] = $productData;

        return $result;
    }

    /**
     * @param int $step
     * @param string $checkoutOption
     * @return array
     */
    public function addCheckoutStepPushData($step, $checkoutOption)
    {
        $checkoutStepResult = [];

        $checkoutStepResult['event'] = 'checkout';
        $checkoutStepResult['eventLabel'] = 'Checkout';
        $checkoutStepResult['ecommerce'] = [];
        $checkoutStepResult['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $checkoutStepResult['ecommerce']['checkout']['actionField'] =  [
            'step' => $step,
            'option' => $checkoutOption
        ];

        $products = [];
        $checkoutBlock = $this->createBlock('Checkout', 'checkout.phtml');

        if ($checkoutBlock) {
            $quote = $this->checkoutSession->getQuote();
            $checkoutBlock->setQuote($quote);
            $products = $checkoutBlock->getProducts();
        }

        $checkoutStepResult['ecommerce']['checkout']['products'] = $products;

        $checkoutOptionResult['event'] = 'checkoutOption';
        $checkoutOptionResult['eventLabel'] = 'Checkout Steps';
        $checkoutOptionResult['ecommerce'] = [];
        $checkoutOptionResult['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $checkoutOptionResult['ecommerce']['checkout_option'] = [];
        $optionData = [];
        $optionData['step'] = $step;
        $optionData['option'] = $checkoutOption;
        $checkoutOptionResult['ecommerce']['checkout_option']['actionField'] = $optionData;

        $result = [];
        $result[] = $checkoutStepResult;
        $result[] = $checkoutOptionResult;

        return $result;
    }

    /**
     * @return boolean
     */
    public function isCustomDimensionCustomerIdEnabled()
    {
        return $this->_gtmOptions['general']['custom_dimension_customerid'];
    }

    /**
     * @return boolean
     */
    public function isCustomDimensionCustomerGroupEnabled()
    {
        return $this->_gtmOptions['general']['custom_dimension_customergroup'];
    }

    /**
     * @return boolean
     */
    public function trackStockStatusEnabled()
    {
        return $this->_gtmOptions['general']['track_stockstatus'];
    }

    /**
     * @return integer
     */
    public function getTrackStockStatusIndexNumber()
    {
        return $this->_gtmOptions['general']['track_stockstatus_indexnumber'];
    }

    /**
     * @return boolean
     */
    public function trackReviewsCountEnabled()
    {
        return $this->_gtmOptions['general']['track_reviewscount'];
    }

    /**
     * @return integer
     */
    public function getTrackReviewsCountIndexNumber()
    {
        return $this->_gtmOptions['general']['track_reviewscount_indexnumber'];
    }

    /**
     * @return boolean
     */
    public function trackReviewsScoreEnabled()
    {
        return $this->_gtmOptions['general']['track_reviewsscore'];
    }

    /**
     * @return integer
     */
    public function getTrackReviewsScoreIndexNumber()
    {
        return $this->_gtmOptions['general']['track_reviewsscore_indexnumber'];
    }

    /**
     * @return boolean
     */
    public function trackSaleProductEnabled()
    {
        return $this->_gtmOptions['general']['track_saleproduct'];
    }

    /**
     * @return integer
     */
    public function getTrackSaleProductIndexNumber()
    {
        return $this->_gtmOptions['general']['track_saleproduct_indexnumber'];
    }

    /**
     * @return boolean
     */
    public function isCustomDimensionPageNameEnabled()
    {
        return $this->_gtmOptions['general']['custom_dimension_pagename'];
    }

    /**
     * @return boolean
     */
    public function isCustomDimensionPageTypeEnabled()
    {
        return $this->_gtmOptions['general']['custom_dimension_pagetype'];
    }

    /**
     * @return boolean
     */
    public function isAdWordConversionTrackingEnabled()
    {
        return $this->_gtmOptions['adwords_conversion_tracking']['enable'];
    }

    /**
     * @return boolean
     */
    public function isAdWordsRemarketingEnabled()
    {
        return $this->_gtmOptions['adwords_remarketing']['enable'];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int $index
     * @param string $list
     * @param string $listId
     * @return string
     */
    public function addProductClick($product, $index = 0, $list = '', $listId = '')
    {
        $productClickBlock = $this->createBlock('Core', 'product_click.phtml');
        $html = '';

        if ($this->isProductClickTrackingOnlyForGtmEnabled()) {
            if ($productClickBlock) {
                $productClickBlock->setProduct($product);
                $productClickBlock->setIndex($index);

                /**
                 * If a list value is set use that one, if nothing add one
                 */
                if (!$list) {
                    $currentCategory = $this->getCurrentCategory();
                    if (!empty($currentCategory)) {
                        $list = $this->getGtmCategory($currentCategory);
                    } else {
                        /* Check if it is from a listing from search or advanced search*/
                        $requestPath = $this->_request->getModuleName() .
                            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
                            DIRECTORY_SEPARATOR . $this->_request->getActionName();
                        switch ($requestPath) {
                            case 'catalogsearch/advanced/result':
                                $list = __('Advanced Search Result');
                                break;
                            case 'catalogsearch/result/index':
                                $list = __('Search Result');
                                break;
                        }
                    }
                }
                $productClickBlock->setList($list);
                $html = trim($productClickBlock->toHtml());
            }

            if (!empty($html)) {
                $eventCallBack = ", 'eventCallback': function() { document.location = '" .
                    $this->escaper->escapeHtml($product->getUrlModel()->getUrl($product)) . "';return false; }});";
                $html = substr(rtrim($html, ");"), 0, -1);
                $html .= $eventCallBack;
            }
        }

        $htmlObject = new \Magento\Framework\DataObject(['html' => $html]);
        $this->_eventManager->dispatch('weltpixel_googletagmanager_afterproductclick', [
            'html' => $htmlObject,
            'product' => $product,
            'index' => $index,
            'list' => $list,
            'listId' => $listId,
        ]);
        $html = $htmlObject->getHtml();

        if (!empty($html)) {
            $html = 'onclick="' . $html . '"';
        }

        return $html;
    }

    /**
     * Returns category tree path
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getGtmCategory($category)
    {
        $categoryPath = $category->getData('path');
        $this->_populateStoreCategories();

        return $this->_buildCategoryPath($categoryPath);
    }

    /**
     * @return string
     */
    public function getDimensionsActionUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'weltpixel_gtm/index/dimensions';
    }

    /**
     * @param boolean $index
     * @return boolean
     */
    public function trackCustomAttribute($index)
    {
        return $this->_gtmOptions['general']['track_custom_attribute_' . $index];
    }

    /**
     * @param boolean $index
     * @return string
     */
    public function getCustomAttributeCode($index)
    {
        return $this->_gtmOptions['general']['track_custom_attribute_' . $index . '_code'];
    }

    /**
     * @param boolean $index
     * @return string
     */
    public function getCustomAttributeName($index)
    {
        return $this->_gtmOptions['general']['track_custom_attribute_' . $index . '_name'];
    }

    /**
     * @param boolean $index
     * @return string
     */
    public function getCustomAttributeType($index)
    {
        return $this->_gtmOptions['general']['track_custom_attribute_' . $index . '_type'];
    }

    /**
     * @param boolean $index
     * @return string
     */
    public function getCustomAttributeIndexNumber($index)
    {
        return $this->_gtmOptions['general']['track_custom_attribute_' . $index . '_indexnumber'];
    }

    /**
     * @param integer $index
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getCustomAttributeValue($index, $product)
    {
        $attributeValue = '';
        if ($this->trackCustomAttribute($index)) {
            $attributeCode = $this->getCustomAttributeCode($index);
            try {
                $frontendValue = $product->getAttributeText($attributeCode);
                if (is_array($frontendValue)) {
                    $attributeValue = implode(',', $product->getAttributeText($attributeCode));
                } else {
                    $attributeValue = trim($product->getAttributeText($attributeCode));
                }
                if (!$attributeValue) {
                    $attributeValue = $product->getData($attributeCode);
                }
            } catch (\Exception $e) {
            }
        }

        return $attributeValue;
    }

    /**
     * @return bool
     */
    public function isDevMoveJsBottomEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEV_MOVE_JS_TO_BOTTOM, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
