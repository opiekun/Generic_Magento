<?php
namespace WeltPixel\GA4\Block;

/**
 * Class \WeltPixel\GA4\Block\Core
 */
class Core extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \WeltPixel\GA4\Helper\Data
     */
    protected $helper;

    /**
     * @var \WeltPixel\GA4\Model\Storage
     */
    protected $storage;

    /**
     * @var \WeltPixel\GA4\Model\Dimension
     */
    protected $dimensionModel;

    /**
     * @var \WeltPixel\GA4\Model\CookieManager
     */
    protected $cookieManager;

    /**
     * @var  \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \WeltPixel\GA4\Helper\Data $helper
     * @param \WeltPixel\GA4\Model\Storage $storage
     * @param \WeltPixel\GA4\Model\Dimension $dimensionModel
     * @param \WeltPixel\GA4\Model\CookieManager $cookieManager
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \WeltPixel\GA4\Helper\Data $helper,
        \WeltPixel\GA4\Model\Storage $storage,
        \WeltPixel\GA4\Model\Dimension $dimensionModel,
        \WeltPixel\GA4\Model\CookieManager $cookieManager,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->storage = $storage;
        $this->dimensionModel = $dimensionModel;
        $this->cookieManager = $cookieManager;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled();
    }


    /**
     * @return bool
     */
    public function excludeTaxFromTransaction()
    {
        return $this->helper->excludeTaxFromTransaction();
    }

    /**
     * @return bool
     */
    public function excludeShippingFromTransaction()
    {
        return $this->helper->excludeShippingFromTransaction();
    }

    /**
     * @return bool
     */
    public function excludeShippingFromTransactionIncludingTax()
    {
        return $this->helper->excludeShippingFromTransactionIncludingTax();
    }

    /**
     * @param $label
     * @param $value
     * @return $this
     */
    public function setEcommerceData($label, $value) {
        $ecommerceData = $this->getEcommerceData();
        if (!$ecommerceData)  {
            $ecommerceData = [];
        }
        $ecommerceData[$label] = $value;

        $this->setDataLayerOption('ecommerce', $ecommerceData);
        return $this;
    }

    /**
     * @param $label
     * @return mixed
     */
    public function getEcommerceData($label = null) {
        $ecommerceData = $this->getDataLayerOption('ecommerce');
        if (isset($label)) {
            return $ecommerceData[$label];
        }

        return $ecommerceData;
    }

    /**
     * @param $label
     * @param $value
     * @return $this
     */
    public function setDataLayerOption($label, $value) {
        $this->storage->setData($label, $value);
        return $this;
    }

    /**
     * @param $dataLayerData
     * @return $this
     */
    public function setAdditionalDataLayerData($dataLayerData) {
        $additionalDataLayerData = $this->storage->getData('additional_datalayer_option');
        if (!$additionalDataLayerData) {
            $additionalDataLayerData = [];
        }
        $additionalDataLayerData[] = $dataLayerData;
        $this->storage->setData('additional_datalayer_option', $additionalDataLayerData);
        return $this;
    }

    /**
     * @param $label
     * @return mixed
     */
    public function getDataLayerOption($label = null) {
        if ($label) {
            return $this->storage->getData($label);
        }

        $storageData =  $this->storage->getData();
        unset($storageData['additional_datalayer_option']);

        return $storageData;
    }

    /**
     * @return string
     */
    public function getDataLayerAsJson()
    {
        $options = $this->getDataLayerOption();
        $options = $this->_splitImpressions($options);
        $additionalDataLayerData = $this->storage->getData('additional_datalayer_option');

        if ($additionalDataLayerData) {
            foreach ($additionalDataLayerData as $dataOptions) {
                $dataOptions = $this->_splitImpressions($dataOptions);
                $options = array_merge($options, $dataOptions);
            }
        }

        return json_encode($options);
    }

    /**
     * @return string
     */
    public function getCurrencyCode() {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }


    /**
     * @param $options
     * @return mixed
     */
    private function _splitImpressions($options) {

        $result = [];
        $chunkLimit = $this->helper->getImpressionChunkSize();

        if (isset($options['ecommerce']['items'])) {
            $originalImpressions = $options['ecommerce']['items'];
            $impressionsCount = count($originalImpressions);
            if ($impressionsCount <= $chunkLimit) {
                $result[] = $options;
                return $result;
            }

            $impressionChunks = array_chunk($originalImpressions, $chunkLimit);
            $options['ecommerce']['items'] = $impressionChunks[0];
            $result[] = $options;

            $chunkCount = count($impressionChunks);
            for ($i = 1; $i<$chunkCount; $i++ ) {
                $newImpressionChunk = [];
                $newImpressionChunk['ecommerce'] = [];
                $newImpressionChunk['ecommerce']['items'] = $impressionChunks[$i];

                $newImpressionChunk['event'] = 'view_item_list';

                $result[] = $newImpressionChunk;
            }

            return $result;
        } else {
            $result[] = $options;
            return $result;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getProductDimensions($product) {
        return $this->dimensionModel->getProductDimensions($product, $this->helper);
    }

    /**
     * @return string
     */
    public function getWpGA4CookiesForJs() {
        $cookies = $this->cookieManager->getWpGA4Cookie();
        return implode(',', array_map(function ($a) { return "'" . $a . "'"; } ,$cookies));
    }
}
