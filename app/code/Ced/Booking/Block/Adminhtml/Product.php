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

namespace Ced\Booking\Block\Adminhtml;

use Ced\Booking\Helper\Data;
use Magento\Backend\Block\Widget\Button\SplitButton;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\TypeFactory;
use Magento\Catalog\Model\ProductFactory;

/**
 * @api
 * @since 100.0.2
 */
class Product extends Container
{
    /**
     * @var TypeFactory
     */
    protected $_typeFactory;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Context $context
     * @param TypeFactory $typeFactory
     * @param ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        TypeFactory $typeFactory,
        ProductFactory $productFactory,
        Data $bookingHelper,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_typeFactory = $typeFactory;
        $this->bookingHelper = $bookingHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        if ($this->bookingHelper->isAnyBookingModuleEnabled()) {
            $addButtonProps = [
                'id' => 'add_new_product',
                'label' => __('Add Product'),
                'class' => 'add',
                'button_class' => '',
                'class_name' => SplitButton::class,
                'options' => $this->_getAddProductButtonOptions(),
            ];
            $this->buttonList->add('add_new', $addButtonProps);
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {
        $splitButtonOptions = [];
        $bookingTypes[Data::APPOINTMENT_PRODUCT_TYPE] = ['label' => __('Appointment')];
        $bookingTypes[Data::EVENT_PRODUCT_TYPE] = ['label' => __('Event')];
        $bookingTypes[Data::RENTAL_PRODUCT_TYPE] = ['label' => __('Rental Booking')];

        foreach ($bookingTypes as $typeId => $type) {
            $module = 'Ced_' . ucfirst($typeId);
            if ($this->bookingHelper->isModuleEnabled($module)) {
                $splitButtonOptions[$typeId] = [
                    'label' => __($type['label']),
                    'onclick' => "setLocation('" . $this->_getProductCreateUrl($typeId) . "')",
                    'default' => Type::DEFAULT_TYPE == $typeId,
                ];
            }
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {
        return $this->getUrl(
            'catalog/product/new',
            ['set' => $this->_productFactory->create()->getDefaultAttributeSetId(), 'type' => $type]
        );
    }
}
