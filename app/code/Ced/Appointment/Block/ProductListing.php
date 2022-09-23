<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Appointment
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Appointment\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Render;
use Ced\Booking\Helper\Data as bookingHelper;
/**
 * Class ProductListing
 * @package Ced\Appointment\Block
 */
class ProductListing extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $_category;
    protected $_appointmentHelper;

    /**
     * ProductListing constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Ced\Booking\Helper\Data $helperdata
     * @param \Magento\Framework\Pricing\Helper\Data $priceRender
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\Rating $ratingFactory
     * @param \Ced\Appointment\Helper\Data $appoitntmentHelper
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        bookingHelper $helperdata,
        \Magento\Framework\Pricing\Helper\Data $priceRender,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\Rating $ratingFactory,
        \Ced\Appointment\Helper\Data $appointmentHelper,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->_helper = $helperdata;
        $this->_priceRender = $priceRender;
        $this->_reviewFactory = $reviewFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->eavConfig = $eavConfig;
        $this->_appointmentHelper = $appointmentHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    public function getCurrentPage()
    {
        return $this->getRequest()->getActionName();
    }

    public function getCurrencySymbol()
    {
        return $this->_priceRender;
    }

    /* get hotel banner from config */

    public function getConfigBanner()
    {
        $configvalue = $this->_helper->getStoreConfig('booking/appointment_config/appointment_banner');
        return $configvalue;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductPrice(Product $product)
    {
        $priceRender = $this->getPriceRender();
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST
                ]
            );
        }
        return $price;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */

    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $cedProductcollection = $this->productCollectionFactory->create();
            $cedProductcollection->addAttributeToSelect('*');
            $cedProductcollection->addAttributeToFilter('type_id', 'appointment');

            if ($location = $this->getRequest()->getParam('location')) {
                $cedProductcollection->addAttributeToFilter('booking_location', ['like' => '%' . $location . '%']);
            }

            if ($appointmentDate = $this->getRequest()->getParam('appointment_date')) {
                $productCollection = $this->productCollectionFactory->create();
                $productIds = $productCollection->addAttributeToFilter('type_id', bookingHelper::APPOINTMENT_PRODUCT_TYPE)->getColumnValues('entity_id');
                $unavailableDates = [];
                $unavailableProductIds = [];
                foreach ($productIds as $productId) {
                    $unavailableDates = $this->_appointmentHelper->getAvailableSlotsByDate($appointmentDate, $productId);
                    if (!isset($unavailableDates['error'])) {
                        $unavailableProductIds[] = $productId;
                    }
                }
                $cedProductcollection->addAttributeToFilter('entity_id', ['in' => $unavailableProductIds]);
            }

            if ($serviceType = $this->getRequest()->getParam('service_type')) {
                $ids = [$serviceType, $this->_appointmentHelper->getServiceTypeBothOptionId()];
                $cedProductcollection->addAttributeToFilter('service_type', ['in' => $ids]);
            }
            if (($searchByPrice = $this->getRequest()->getParam('search_by_price'))) {
                $price = explode("-", $searchByPrice);
                $cedProductcollection->addAttributeToFilter(
                    'price',
                    [['from' => $price[0], 'to' => $price[1]]
                    ]
                );
            }
            $this->_productCollection = $cedProductcollection;
        }
        $this->_productCollection->getSize();
        return $this->_productCollection;
    }

    /** get appointment service type options */
    public function getServiceTypeOptions()
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'service_type');
        $options = $attribute->getSource()->getAllOptions();
        return $options;
    }
}
