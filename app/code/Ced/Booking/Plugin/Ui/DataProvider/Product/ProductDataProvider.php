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
namespace Ced\Booking\Plugin\Ui\DataProvider\Product;

/**
 * Class ProductDataProvider
 * @package Ced\Booking\Plugin\Ui\DataProvider\Product
 */
class ProductDataProvider
{
    /**
     * ProductDataProvider constructor.
     * @param \Ced\Booking\Helper\Data $helperData
     */
    public function __construct(\Ced\Booking\Helper\Data $helperData)
    {
        $this->_helperData = $helperData;
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\MassDelete $subject
     * @param $result
     * @return mixed
     */
    public function aroundgetData(\Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider $subject,callable $proceed)
    {
        $excludeType = [];
        if (!$this->_helperData->isModuleEnabled('Ced_Appointment'))
        {
            $excludeType[] = 'appointment'; 
        }
        if (!$this->_helperData->isModuleEnabled('Ced_Event'))
        {
            $excludeType[] = 'event'; 
        }

        if (!empty($excludeType))
            $subject->getCollection()->addAttributeToFilter('type_id',['nin'=>$excludeType]);
        return $proceed();
    }
}