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
 * @category  Ced
 * @package   Ced_Booking
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license   https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Booking\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Ced\Booking\Model\Product
 */
class Type implements OptionSourceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $_config;

    /**
     * Product types
     *
     * @var array|string
     */
    protected $_types;

    /**
     * Composite product type Ids
     *
     * @var array
     */
    protected $_compositeTypes;

    /**
     * Price models
     *
     * @var array
     */
    protected $_priceModels;

    /**
     * Product types by type indexing priority
     *
     * @var array
     */
    protected $_typesPriority;

    /**
     * Product type factory
     *
     * @var \Magento\Catalog\Model\Product\Type\Pool
     */
    protected $_productTypePool;

    /**
     * Price model factory
     *
     * @var \Magento\Catalog\Model\Product\Type\Price\Factory
     */
    protected $_priceFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceInfo\Factory
     */
    protected $_priceInfoFactory;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     * @param \Magento\Catalog\Model\Product\Type\Pool $productTypePool
     * @param \Magento\Catalog\Model\Product\Type\Price\Factory $priceFactory
     * @param \Magento\Framework\Pricing\PriceInfo\Factory $priceInfoFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config,
        \Magento\Catalog\Model\Product\Type\Pool $productTypePool,
        \Magento\Catalog\Model\Product\Type\Price\Factory $priceFactory,
        \Magento\Framework\Pricing\PriceInfo\Factory $priceInfoFactory,
        \Ced\Booking\Helper\Data $bookingHelper
    ) {
        $this->_config = $config;
        $this->_productTypePool = $productTypePool;
        $this->_priceFactory = $priceFactory;
        $this->_priceInfoFactory = $priceInfoFactory;
        $this->bookingHelper = $bookingHelper;
    }

    /**
     * Factory to product singleton product type instances
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  \Magento\Catalog\Model\Product\Type\AbstractType
     */
    public function factory($product)
    {
        $types = $this->getTypes();
        $typeId = $product->getTypeId();

        if (!empty($types[$typeId]['model'])) {
            $typeModelName = $types[$typeId]['model'];
        } else {
            $typeModelName = self::DEFAULT_TYPE_MODEL;
            $typeId = self::DEFAULT_TYPE;
        }

        $typeModel = $this->_productTypePool->get($typeModelName);
        $typeModel->setConfig($types[$typeId]);
        return $typeModel;
    }

    /**
     * Product type price model factory
     *
     * @param   string $productType
     * @return  \Magento\Catalog\Model\Product\Type\Price
     */
    public function priceFactory($productType)
    {
        if (isset($this->_priceModels[$productType])) {
            return $this->_priceModels[$productType];
        }

        $types = $this->getTypes();

        if (!empty($types[$productType]['price_model'])) {
            $priceModelName = $types[$productType]['price_model'];
        } else {
            $priceModelName = self::DEFAULT_PRICE_MODEL;
        }

        $this->_priceModels[$productType] = $this->_priceFactory->create($priceModelName);
        return $this->_priceModels[$productType];
    }

    /**
     * Get Product Price Info object
     *
     * @param Product $saleableItem
     * @return \Magento\Framework\Pricing\PriceInfoInterface
     */
    public function getPriceInfo(Product $saleableItem)
    {
        return $this->_priceInfoFactory->create($saleableItem);
    }

    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        foreach ($this->getTypes() as $typeId => $type) {
            $options[$typeId] = (string)$type['label'];
        }
        return $options;
    }

    /**
     * Get product type labels array with empty value
     *
     * @return array
     */
    public function getAllOption()
    {
        $options = $this->getOptionArray();
        array_unshift($options, ['value' => '', 'label' => '']);
        return $options;
    }

    /**
     * Get product type labels array with empty value for option element
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);
        return $res;
    }

    /**
     * Get product type labels array for option element
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

    /**
     * Get product type label
     *
     * @param string $optionId
     * @return null|string
     */
    public function getOptionText($optionId)
    {
        $options = $this->getOptionArray();
        return $options[$optionId] ?? null;
    }

    /**
     * Get product types
     *
     * @return array
     */
    public function getTypes()
    {
        $productTypes = [];
        if ($this->_types === null) {
            $bookingTypes = $this->bookingHelper->getEnabledBookingTypes();
            if (!empty($bookingTypes))
            {
                foreach ($bookingTypes as $type)
                {
                    $productTypes[$type]['label'] = __(ucfirst($type).' Booking');
                }
            }
            $this->_types = $productTypes;
        }
        return $this->_types;
    }

    /**
     * Return composite product type Ids
     *
     * @return array
     */
    public function getCompositeTypes()
    {
        if ($this->_compositeTypes === null) {
            $this->_compositeTypes = [];
            $types = $this->getTypes();
            foreach ($types as $typeId => $typeInfo) {
                if (array_key_exists('composite', $typeInfo) && $typeInfo['composite']) {
                    $this->_compositeTypes[] = $typeId;
                }
            }
        }
        return $this->_compositeTypes;
    }

    /**
     * Return product types by type indexing priority
     *
     * @return array
     */
    public function getTypesByPriority()
    {
        if ($this->_typesPriority === null) {
            $this->_typesPriority = [];
            $simplePriority = [];
            $compositePriority = [];

            $types = $this->getTypes();
            foreach ($types as $typeId => $typeInfo) {
                $priority = isset($typeInfo['index_priority']) ? abs(intval($typeInfo['index_priority'])) : 0;
                if (!empty($typeInfo['composite'])) {
                    $compositePriority[$typeId] = $priority;
                } else {
                    $simplePriority[$typeId] = $priority;
                }
            }

            asort($simplePriority, SORT_NUMERIC);
            asort($compositePriority, SORT_NUMERIC);

            foreach (array_keys($simplePriority) as $typeId) {
                $this->_typesPriority[$typeId] = $types[$typeId];
            }
            foreach (array_keys($compositePriority) as $typeId) {
                $this->_typesPriority[$typeId] = $types[$typeId];
            }
        }
        return $this->_typesPriority;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
