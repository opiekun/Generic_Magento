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
namespace Ced\Booking\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ced\Booking\Helper\Data;

/**
 * Class InvoiceSaveAfter
 * @package Ced\Booking\Observer
 */
class InvoiceSaveAfter implements ObserverInterface
{
    /**
     * InvoiceSaveAfter constructor.
     * @param \Ced\Booking\Model\ResourceModel\RentOrders\CollectionFactory $collectionFactory
     * @param Data $bookingHelper
     */
    public function __construct(\Ced\Booking\Model\ResourceModel\RentOrders\CollectionFactory $collectionFactory,
                                \Ced\Booking\Helper\Data $bookingHelper)
    {
        $this->collectionFactory = $collectionFactory;
        $this->_bookingHelper = $bookingHelper;
        
        if ($this->_bookingHelper->isModuleEnabled('Ced_Event'))
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->ticketPdf = $objectManager->get(\Ced\Event\Model\TicketPdf::class);
            $this->_transportBuilder = $objectManager->create(\Ced\Event\Mail\Template\TransportBuilder::class);
        }
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $allItems = $invoice->getOrder()->getAllItems();
        $orderIncrementId = $invoice->getOrder()->getIncrementId();
        $bookingProductTypes = $this->_bookingHelper->getAllBookingTypes();
        foreach ($allItems as $item) {

            if (in_array($item->getProductType(),$bookingProductTypes)) {
                if ($item->getQtyInvoiced() > 0) {

                    $collection = $this->collectionFactory->create();
                    $collection->addFieldToFilter('order_id',$orderIncrementId)
                                ->addFieldToFilter('product_id',$item->getProduct()->getId());

                    if ($item->getQtyInvoiced() < $collection->getFirstItem()->getQtyOrdered())
                    {
                        $status = Data::ORDER_STATUS_PARTIALLY_INVOCED;
                    } else {
                        $status = Data::ORDER_STATUS_COMPLETE;
                    }
                    foreach ($collection as $data) {
                        $data->setStatus($status);
                        $data->setQtyInvoiced($data->getQtyOrdered());
                    }
                    $collection->save();

                   /* $pdfdata = ['order_id'=>000000035,'product_id'=>$item->getProductId()];
                    $pdf = $this->ticketPdf->getPdf($pdfdata);
                    try{
                        $transport = $this->_transportBuilder->setTemplateIdentifier('event_ticket_template')
                            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                            ->setTemplateVars(['message'=>1111])
                            ->setFrom(['name'=>'surabhi','email'=>'owner@example.com'])
                            ->addTo('lisa@cedc')
                            ->addCc(['ryancedwalker@gmail.com'])
                            ->addAttatchment($pdf->render(),'application/pdf','Event-Ticket1.pdf')
                            ->getTransport();
                   

                        $transport->sendMessage();
                    }catch(\Exception $e)
                    {
                        $this->_messageManager->addErrorMessage($e->getMessage());

                    }*/
                }
            }
        }
        return $this;
    }
}



