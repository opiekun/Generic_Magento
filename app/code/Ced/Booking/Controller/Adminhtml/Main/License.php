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
namespace Ced\Booking\Controller\Adminhtml\Main;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class License extends \Magento\Backend\App\Action
{
    protected $_licenseActivateUrl = null;
    protected $_feedHelper = null;

    const LICENSE_ACTIVATION_URL_PATH = 'system/license/activate_url';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Ced\Booking\Helper\Feed $feedHelper,
        \Ced\Booking\Block\Adminhtml\Extensions\Details $details
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_feedHelper = $feedHelper;
        $this->_details = $details;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $resultJson = $this->resultJsonFactory->create();
        $postData = $this->getRequest()->getParams();
        unset($postData['key']);
        unset($postData['form_key']);
        unset($postData['isAjax']);

        $json = ['success' => 0, 'message' => __('There is an Error Occurred.')];
        if ($postData) {
            foreach ($postData as $moduleName => $licensekey) {
                if (preg_match('/ced_/i', $moduleName)) {
                    if (strlen($licensekey) == 0) {
                        $json = ['success' => 1, 'message' => ''];
                        $resultJson->setData($json);
                        return $resultJson;
                    }
                    unset($postData[$moduleName]);
                    $postData['module_name'] = $moduleName;
                    $allModules = $this->_feedHelper->getAllModules();

                    $postData['module_version'] = isset($allModules[$moduleName]['release_version']) ? $allModules[$moduleName]['release_version'] : '';
                    $postData['module_license'] = $licensekey;
                    break;
                }
            }

            $response = $this->validateAndActivateLicense($postData);

            if ($response && isset($response['hash']) && isset($response['level'])) {

                $json = ['success' => 0, 'message' => __('There is an Error Occurred.')];
                $valid = $response['hash'];
                try {

                    for ($i = 1; $i <= $response['level']; $i++) {
                        $valid = base64_decode($valid);
                    }
                    $valid = json_decode($valid, true);

                    if (is_array($valid) &&
                        isset($valid['domain']) &&
                        isset($valid['module_name']) &&
                        isset($valid['license']) &&
                        $valid['module_name'] == $postData['module_name'] &&
                        $valid['license'] == $postData['module_license']
                    ) {
                        

                        $path = \Ced\Booking\Block\Extensions::HASH_PATH_PREFIX . strtolower($postData['module_name']) . '_hash';
                        $this->_feedHelper->setDefaultStoreConfig($path, $response['hash'], 0);
                        $path = \Ced\Booking\Block\Extensions::HASH_PATH_PREFIX . strtolower($postData['module_name']) . '_level';
                        $this->_feedHelper->setDefaultStoreConfig($path, $response['level'], 0);
                        $json['success'] = 1;
                        $json['message'] = __('Module Activated successfully.');
                        $this->_details->checkLicense();
                        
                    } else {
                        $json['success'] = 0;
                        $json['message'] = isset($response['error']['code']) && isset($response['error']['msg']) ? 'Error (' . $response['error']['code'] . '): ' . $response['error']['msg'] : __('Invalid License Key.');
                    }
                } catch (\Exception $e) {
                    $json['success'] = 0;
                    $json['message'] = $e->getMessage();
                }
            }
        }
        $resultJson->setData($json);
        return $resultJson;

    }

    /**
     * Retrieve local license url
     *
     * @return string
     */
    private function getLicenseActivateUrl()
    {
        if (is_null($this->_licenseActivateUrl)) {
            $this->_licenseActivateUrl = ($this->_feedHelper->getStoreConfig(\Ced\Booking\Block\Extensions::LICENSE_USE_HTTPS_PATH) ? 'https://' : 'https://')
                . $this->_feedHelper->getStoreConfig(self::LICENSE_ACTIVATION_URL_PATH);
        }
        return $this->_licenseActivateUrl;
    }

    /**
     * @param array $urlParams
     * @return bool|mixed
     */
    private function validateAndActivateLicense($urlParams = [])
    {
        $body = '';
        if (isset($urlParams['form_key'])) unset($urlParams['form_key']);
        $urlParams = array_merge($this->_feedHelper->getEnvironmentInformation(), $urlParams);

        if (is_array($urlParams) && count($urlParams) > 0) {

            if (isset($urlParams['installed_extensions_by_cedcommerce'])) unset($urlParams['installed_extensions_by_cedcommerce']);
            $body = $this->_feedHelper->addParams('', $urlParams);
            $body = trim($body, '?');

        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getLicenseActivateUrl());
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            if (isset($info['http_code']) && $info['http_code'] != 200) return false;
            $result = json_decode($result, true);
        } catch (\Exception $e) {
            return false;
        }

        return $result;
    }


}
