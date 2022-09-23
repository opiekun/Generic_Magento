<?php

namespace MalibuCommerce\AmastyShippingTableRates\Model\Navision;

use Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory as MethodCollectionFactory;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Model\Region;
use Magento\Framework\App\Config as CommonConfig;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\GiftMessage\Model\Message;
use Magento\Store\Model\ScopeInterface;
use MalibuCommerce\MConnect\Model\Config;
use MalibuCommerce\MConnect\Model\Navision\Connection;
use MalibuCommerce\MConnect\Model\Navision\Order as OrderSource;
use Psr\Log\LoggerInterface;

class Order extends OrderSource
{
    const AMASTY_CARRIER_CODE       = 'amstrates';
    const AMASTY_METHOD_CODE_SUFFIX = 'amstrates';

    /**
     * @var CommonConfig
     */
    protected $commonConfig;

    /**
     * @var MethodCollectionFactory
     */
    protected $methodCollectionFactory;

    /**
     * @param Region $directoryRegion
     * @param Address $customerAddress
     * @param CustomerFactory $customerFactory
     * @param Message $giftMessage
     * @param Config $config
     * @param Connection $mConnectNavisionConnection
     * @param ProductMetadataInterface $productMetadata
     * @param Json $serializer
     * @param Manager $moduleManager
     * @param LoggerInterface $logger
     * @param CommonConfig $commonConfig
     * @param MethodCollectionFactory $methodCollectionFactory
     * @param array $data
     */
    public function __construct(
        Region $directoryRegion,
        Address $customerAddress,
        CustomerFactory $customerFactory,
        Message $giftMessage,
        Config $config,
        Connection $mConnectNavisionConnection,
        ProductMetadataInterface $productMetadata,
        Json $serializer,
        Manager $moduleManager,
        LoggerInterface $logger,
        /**
         * MALC-489 WEST-38: Amasty Shipping rates carrier/service level on order export
         * @customization START
         */
        CommonConfig $commonConfig,
        MethodCollectionFactory $methodCollectionFactory,
        /** @customization END */
        array $data = []
    ) {
        $this->commonConfig = $commonConfig;
        $this->methodCollectionFactory = $methodCollectionFactory;

        parent::__construct(
            $directoryRegion,
            $customerAddress,
            $customerFactory,
            $giftMessage,
            $config,
            $mConnectNavisionConnection,
            $productMetadata,
            $serializer,
            $moduleManager,
            $logger,
            $data
        );
    }

    /**
     * Construct NAV shipping information XML
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $orderEntity
     * @param \simpleXMLElement $root
     *
     * @return Order
     */
    protected function addShipping(\Magento\Sales\Api\Data\OrderInterface $orderEntity, &$root)
    {
        /**
         * MALC-489 WEST-38: Amasty Shipping rates carrier/service level on order export
         * @customization START
         */
        parent::addShipping($orderEntity, $root);

        if ($root->shipping_carrier != self::AMASTY_CARRIER_CODE) {
            return $this;
        }
        if (strpos($root->shipping_method, self::AMASTY_METHOD_CODE_SUFFIX) !== 0) {
            return $this;
        }
        $methodId = substr($root->shipping_method, strlen(self::AMASTY_METHOD_CODE_SUFFIX));
        $methodLabel = $this->methodCollectionFactory->create()
            ->joinLabels($methodId)
            ->addFieldToFilter('store_id', ['eq' => $orderEntity->getStoreId()])
            ->getFirstItem();
        $root->shipping_method = empty($methodLabel->getLabel())
            ? $methodLabel->getName()
            : $methodLabel->getLabel();
        $root->shipping_carrier = $this->commonConfig->getValue(
            'carriers/amstrates/title',
            ScopeInterface::SCOPE_STORE,
            $orderEntity->getStoreId()
        );

        return $this;
        /** @customization END */
    }
}
