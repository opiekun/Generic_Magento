<?php
namespace ClassWallet\Payment\Model\Config\Source;

class ShippingMethod implements \Magento\Framework\Data\OptionSourceInterface
{

	protected $scopeConfig; 
	protected $shipconfig;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Shipping\Model\Config $shipconfig
	) {
		$this->shipconfig 	= 	$shipconfig;
		$this->scopeConfig 	= 	$scopeConfig;
	}

	public function toOptionArray()
	{

	    $activeCarriers = $this->shipconfig->getActiveCarriers();
	    $methods 		=	array();
	    foreach($activeCarriers as $carrierCode => $carrierModel) {
	       	$options = array();

	       	if ($carrierMethods = $carrierModel->getAllowedMethods()) {
	           	foreach ($carrierMethods as $methodCode => $method) {
	                $code = $carrierCode . '_' . $methodCode;
	                $options[] = array('value' => $code, 'label' => $method);
	           	}
	           	$carrierTitle = $this->scopeConfig->getValue('carriers/'.$carrierCode.'/title');
	        }

	        $methods[] = array('value' => $options, 'label' => $carrierTitle);
	    }

	    return $methods;   

/*		return [
				['value' => 'grid', 'label' => __('Grid Only')],
				['value' => 'list', 'label' => __('List Only')],
				['value' => 'grid-list', 'label' => __('Grid (default) / List')],
				['value' => 'list-grid', 'label' => __('List (default) / Grid')]
			];*/
	}
}
?>