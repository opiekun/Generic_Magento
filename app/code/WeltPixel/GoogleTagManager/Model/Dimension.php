<?php

namespace WeltPixel\GoogleTagManager\Model;

/**
 * Class \WeltPixel\GoogleTagManager\Model\Dimension
 */
class Dimension extends \WeltPixel\GoogleTagManager\Model\Storage
{
    const DIMENSION_TYPE = 'dimension';
    const METRIC_TYPE = 'metric';
    /**
     * @var \Magento\Review\Model\Review\SummaryFactory
     */
    protected $reviewSummaryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context, $registry);
        $this->reviewSummaryFactory = $reviewSummaryFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $product
     * @return \Magento\Review\Model\Review\Summary
     */
    public function getReviewSummary($product)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $reviewSummary = $this->reviewSummaryFactory->create();
        $reviewSummary->setData('store_id', $storeId);
        $summaryModel = $reviewSummary->load($product->getId());

        return $summaryModel;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \WeltPixel\GoogleTagManager\Helper\Data $gtmHelper
     * @return array
     */
    public function getProductDimensions($product, $gtmHelper)
    {
        $dimensions = [];

        if ($gtmHelper->trackStockStatusEnabled()) {
            $productStockStatus = ($product->isAvailable()) ? 'In stock' : 'Out of stock';
            $stockDimensionIndex = self::DIMENSION_TYPE . $gtmHelper->getTrackStockStatusIndexNumber();
            $dimensions[$stockDimensionIndex] = $productStockStatus;
        }

        if ($gtmHelper->trackSaleProductEnabled()) {
            $saleProduct = ($product->getSale()) ? 'Yes' : 'No';
            $saleDimensionIndex = self::DIMENSION_TYPE . $gtmHelper->getTrackSaleProductIndexNumber();
            $dimensions[$saleDimensionIndex] = $saleProduct;
        }

        $summaryModel = $this->getReviewSummary($product);

        if ($gtmHelper->trackReviewsCountEnabled()) {
            $reviewCount = ($summaryModel->getReviewsCount()) ? $summaryModel->getReviewsCount() : 0;
            $reviewCountDimensionIndex = self::DIMENSION_TYPE . $gtmHelper->getTrackReviewsCountIndexNumber();
            $dimensions[$reviewCountDimensionIndex] = strval($reviewCount);
        }

        if ($gtmHelper->trackReviewsScoreEnabled()) {
            $ratingSummary = $summaryModel->getRatingSummary();
            $reviewScoreDimensionIndex = self::DIMENSION_TYPE . $gtmHelper->getTrackReviewsScoreIndexNumber();
            $dimensions[$reviewScoreDimensionIndex] = strval($ratingSummary / 20);
        }

        for ($i=1; $i<=5; $i++) {
            if ($gtmHelper->trackCustomAttribute($i)) {
                $attributeValue = $gtmHelper->getCustomAttributeValue($i, $product);
                $customAttributeDimensionIndex = $gtmHelper->getCustomAttributeType($i) . $gtmHelper->getCustomAttributeIndexNumber($i);
                $dimensions[$customAttributeDimensionIndex] = $attributeValue;
            }
        }

        return $dimensions;
    }
}