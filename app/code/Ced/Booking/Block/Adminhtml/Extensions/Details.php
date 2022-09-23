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
namespace Ced\Booking\Block\Adminhtml\Extensions;

class Details extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Ced\Booking\Helper\Feed
     */
    protected $_feedHelper;

    /**
     * @var \Ced\Booking\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Details constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Ced\Booking\Helper\Feed $feedHelper
     * @param \Ced\Booking\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Ced\Booking\Helper\Feed $feedHelper,
        \Ced\Booking\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_feedHelper = $feedHelper;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function getModules(){
        $modules = $this->_feedHelper->getCedCommerceExtensions();
        $args = '';
        foreach ($modules as $moduleName=>$releaseVersion)
        {
            $m = strtolower($moduleName); if(!preg_match('/ced/i',$m)){ return $this; }  $h =
            $this->_dataHelper->getStoreConfig(\Ced\Booking\Block\Extensions::HASH_PATH_PREFIX.$m.'_hash');
            for($i=1;$i<=(int)$this->_dataHelper->getStoreConfig(\Ced\Booking\Block\Extensions::HASH_PATH_PREFIX.$m.'_level');$i++)
            {$h = base64_decode($h);}$h = json_decode($h,true);
            if(is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) &&
                strtolower($h['module_name']) == $m && $h['license'] ==
                $this->_dataHelper->getStoreConfig(\Ced\Booking\Block\Extensions::HASH_PATH_PREFIX.$m)){}else{
                $args .= $m.',';
            }
        }

        $args = trim($args,',');
        return $args;

    }

    public function checkLicense(){

        if(trim($this->getModules())!=''){
            if($this->_dataHelper->getStoreConfig('ced/booking/islicensevalid'))
            {
                $this->_dataHelper->setStoreConfig('ced/booking/islicensevalid',0);
            }
        }
        else
        {
            $this->_dataHelper->setStoreConfig('ced/booking/islicensevalid',1);
        }
    }

}