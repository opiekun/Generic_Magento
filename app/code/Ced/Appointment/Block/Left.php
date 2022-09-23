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

/**
 * Class Left
 * @package Ced\Appointment\Block
 */
class Left extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Left constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Ced\Booking\Helper\Data $helperdata
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Ced\Booking\Helper\Data $helperdata,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->_helper = $helperdata;
        $this->eavConfig = $eavConfig;
        $this->_currencyFactory = $currencyFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * @return array
     */
    public function getProductPrices()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('type_id','appointment');
        $collection->addFinalPrice();
        $productPrices = $collection->getColumnValues('final_price');
        return $productPrices;
    }

    /**
     * @return mixed
     */
    public function getCurrentSymbol()
    {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $currencyFactory = $this->_currencyFactory->create();
        $currencySymbol = $currencyFactory->load($currencyCode)->getCurrencySymbol();
        return $currencySymbol;
    }

    /** get appointment service type options */
    public function getServiceTypeOptions()
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'service_type');
        $options = $attribute->getSource()->getAllOptions();
        return $options;
    }

    /** form submit url */
    public function getFormActionUrl(){
        return $this->getUrl('*/*/');
    }
}
