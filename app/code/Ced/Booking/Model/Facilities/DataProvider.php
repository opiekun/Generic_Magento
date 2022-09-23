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

namespace Ced\Booking\Model\Facilities;

use Ced\Booking\Model\ResourceModel\Facilities\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\Registry $registry
     * @param Collection $collectionFactory
     * @param \Ced\Booking\Helper\Data $bookingHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Registry $registry,
        Collection $collectionFactory,
        \Ced\Booking\Helper\Data $bookingHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
        $this->_registry = $registry;
        $this->collection = $collectionFactory;
        $this->bookingHelper = $bookingHelper;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        $data = $this->_registry->registry('booking_facilities_data');
        $this->loadedData[$data->getId()] = $data->getData();
        if (isset($data['id'])) {
            if ($data->getImageType() == 'image') {
                $this->loadedData[$data['id']]['image'][0]['name'] = $data->getImageValue();
                $this->loadedData[$data['id']]['image'][0]['url'] = $this->bookingHelper->getImageUrl($data->getImageValue());
            } elseif ($data->getImageType() == 'icon') {
                $this->loadedData[$data['id']]['icon'] = $data->getImageValue();
            }
        } else {
            $this->loadedData = [];
        }
        return $this->loadedData;
    }
}
