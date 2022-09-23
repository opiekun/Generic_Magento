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
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Ui\DataProvider\Order;


class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $addFieldStrategies;


    protected $addFilterStrategies;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Pincode $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection,
        \Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory $collection,
        \Ced\Booking\Helper\Data $helperData,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->helperData = $helperData;
        $this->itemcollection = $itemCollection;
        $ocollection = $collection->create();
        $orderIds = $this->getOrderIds();
        $this->collection = $ocollection->addFieldToFilter('entity_id',['in'=>$orderIds]);
        $this->size = sizeof($this->collection->getData());
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        
    }

    protected function getOrderIds()
    {
        $bookingTypes = $this->helperData->getAllBookingTypes();
        $itemscollection = $this->itemcollection
            ->addFieldToFilter('product_type', ['in' => $bookingTypes]);
        $orderIds = $itemscollection->getColumnValues('order_id');

        $unique_order_ids = array_values(array_unique($orderIds));
        return $unique_order_ids;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
       
        return [
            'totalRecords' => $this->size,
            'items' => $this->getCollection()->getData(),
        ];
    }
}