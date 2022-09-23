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
namespace Ced\Appointment\Block\Product\View;

use Magento\Framework\View\Element\Template;

/**
 * Class Form
 * @package Ced\Appointment\Block\Product\View
 */
class Form extends Template
{
    protected $_template = 'product/view/form.phtml';

    /**
     * ExtraTabs constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Quote\Model\Quote\Item\OptionFactory $itemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item\Option $itemResourceModel,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data=[]
    )
    {
        parent::__construct($context,$data);
        $this->_coreRegistry = $coreRegistry;
        $this->itemFactory = $itemFactory;
        $this->itemResourceModel = $itemResourceModel;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * get current product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @param $quoteItemId
     * @return \Magento\Quote\Model\Quote\Item\Option
     */
    public function getCustomOption($quoteItemId)
    {
        $quoteItem = $this->itemFactory->create();
        $quoteItemData = $this->itemResourceModel->load($quoteItem,$quoteItemId,'item_id');
        return $this->jsonHelper->jsonDecode($quoteItem->getValue());
    }
}
