<?php
namespace WeltPixel\QuickCart\Block;

use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ProductRepository;
use WeltPixel\QuickCart\Model\Config\Source\CarouselDisplayFor;
use WeltPixel\Quickview\Model\Config\Source\CarouselType;

/**
 * Class CarouselContent
 * @package WeltPixel\QuickCart\Block
 */
class CarouselContent extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \WeltPixel\QuickCart\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * Catalog product visibility
     *
     * @var ProductVisibility
     */
    protected $catalogProductVisibility;

    /**
     * @param \WeltPixel\QuickCart\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param ProductRepository $productRepository
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param ProductVisibility $catalogProductVisibility
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \WeltPixel\QuickCart\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        ProductRepository $productRepository,
        \Magento\Catalog\Model\Config $catalogConfig,
        ProductVisibility $catalogProductVisibility,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->_helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->catalogConfig = $catalogConfig;
        $this->catalogProductVisibility = $catalogProductVisibility;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCarouselTitle()
    {
        return $this->_helper->getCarouselTitle();
    }

    /**
     * @return string
     */
    public function getCarouselTitleAlignment()
    {
        return $this->_helper->getCarouselTitleAlignment();
    }

    /**
     * @return int
     */
    public function getCarouselItemDesktop()
    {
        return $this->_helper->getCarouselItemDesktop();
    }

    /**
     * @return int
     */
    public function getCarouselItemMobile()
    {
        return $this->_helper->getCarouselItemMobile();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCarouselProducts()
    {
        $productIdForCarousel = $this->getProductIdForCarousel();
        if (!$productIdForCarousel) {
            return null;
        }
        $productForCarousel = $this->productRepository->getById($productIdForCarousel);

        if (!$this->_helper->isCarouselEnabled()) {
            return null;
        }

        $productCollection = null;
        $carouselType = $this->_helper->getCarouselType();

        switch ($carouselType) {
            case CarouselType::TYPE_RELATED:
                $productCollection = $productForCarousel->getRelatedProductCollection()->addAttributeToSelect(
                    'required_options'
                )->setPositionOrder()->addStoreFilter();
                break;
            case CarouselType::TYPE_UPSELL:
                $productCollection = $productForCarousel->getUpSellProductCollection()->setPositionOrder()->addStoreFilter();
                break;
            case CarouselType::TYPE_CROSSELL:
                $productCollection = $productForCarousel->getCrossSellProductCollection()->setPositionOrder()->addStoreFilter();
                break;
        }

        $this->_addProductAttributesAndPrices($productCollection);
        $productCollection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $productCollection->load();
        foreach ($productCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $productCollection;
    }

    /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _addProductAttributesAndPrices(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }

    /**
     * @return mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProductIdForCarousel()
    {
        $productId = null;
        $carouselFor = $this->_helper->getCarouselDisplayFor();
        switch ($carouselFor) {
            case CarouselDisplayFor::LAST_ITEM:
                $productId = $this->checkoutSession->getLastAddedProductId();
                break;
            case CarouselDisplayFor::FIRST_ITEM:
                $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
                if (count($quoteItems)) {
                    return $quoteItems[0]['product_id'];
                }
                break;
        }

        return $productId;
    }
}
