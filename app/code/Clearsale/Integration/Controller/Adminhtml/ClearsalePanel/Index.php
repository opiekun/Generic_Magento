<?php
namespace Clearsale\Integration\Controller\Adminhtml\ClearsalePanel;

class Index extends \Magento\Backend\App\Action
{
  protected $resultPageFactory;
  protected $observer;
  protected $resultJsonFactory;

  public function __construct(
      \Magento\Backend\App\Action\Context $context,
      \Magento\Framework\View\Result\PageFactory $resultPageFactory,
      \Clearsale\Integration\Observer\ClearsaleObserver $observer,
      \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory


  ) {
       parent::__construct($context);
       $this->resultPageFactory = $resultPageFactory;
       $this->observer = $observer;
       $this->resultJsonFactory = $resultJsonFactory;
  }

  public function execute()
  {
      $params = $this->getRequest()->getParams();
      $clearSend = $this->getRequest()->getParam('sendPendingOrders');
      $clearUpdate = $this->getRequest()->getParam('updateOrders');
      if($clearSend) {
        $this->observer->sendPendingOrders();
        $result = $this->resultJsonFactory->create();
        return $result->setData([
            'messages' => 'Successfully. Params: ' . json_encode($params),
            'error' => false
        ]);
      }
      else if($clearUpdate) {
        $this->observer->getClearsaleOrderStatus();
        $result = $this->resultJsonFactory->create();
        return $result->setData([
            'messages' => 'Successfully. Params: ' . json_encode($params),
            'error' => false
        ]);
      }
      return  $resultPage = $this->resultPageFactory->create();
  }
}
   



     