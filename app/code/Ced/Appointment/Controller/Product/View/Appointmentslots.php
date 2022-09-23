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

namespace Ced\Appointment\Controller\Product\View;

use Ced\Appointment\Helper\Data;
use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Appointmentslots
 * @package Ced\Appointment\Controller\Product\View
 */
class Appointmentslots extends \Magento\Framework\App\Action\Action
{

    /**
     * Appointmentslots constructor.
     * @param Context $context
     * @param JsonFactory $resultJson
     * @param Data $appointmentHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJson,
        Data $appointmentHelper
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJson;
        $this->appointmentHelper = $appointmentHelper;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $selectedDate = $postData['selected_date'];
        $productId = $postData['product_id'];
        try {
            $availableSlots = $this->appointmentHelper->getAvailableSlotsByDate($selectedDate, $productId);
            if (isset($availableSlots['error'])) {
                throw new LocalizedException(__($availableSlots['message']));
            }
            $data = ['success' => true, 'date' => $selectedDate, 'slots' => $availableSlots];
        } catch (\Exception $e) {
            $data = ['success' => false, 'message' => $e->getMessage()];
        }
        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData($data);
    }
}
