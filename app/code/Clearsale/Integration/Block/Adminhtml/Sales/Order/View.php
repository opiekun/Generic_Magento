<?php
namespace Clearsale\Integration\Block\Adminhtml\Sales\Order;


class Order  extends \Magento\Sales\Block\Adminhtml\Order\View {
    public function  __construct() {

        parent::__construct();
	
	 //create URL to our custom action
        	
	//$url = Mage::getModel('adminhtml/url')->getUrl('integration/observer/sendSpecificOrder');

        //add the button
        //$this->_addButton('cygtest_resubmit', array(
        //        'label'     => 'Reanalysis',
        //        'onclick'   => 'setLocation(\'' . $url . '\')',
        //        'class'     => 'go'
        //));


    }
}