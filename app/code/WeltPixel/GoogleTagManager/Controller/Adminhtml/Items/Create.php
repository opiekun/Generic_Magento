<?php
namespace WeltPixel\GoogleTagManager\Controller\Adminhtml\Items;

/**
 * Class \WeltPixel\GoogleTagManager\Controller\Adminhtml\Items\Create
 */
class Create extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \WeltPixel\GoogleTagManager\Model\Api
     */
    protected $apiModel = null;

    /**
     * @var \WeltPixel\GoogleTagManager\Model\JsonGenerator
     */
    protected $jsonGenerator;

    /**
     * Version constructor.
     *
     * @param \WeltPixel\GoogleTagManager\Model\Api $apiModel
     * @param \WeltPixel\GoogleTagManager\Model\JsonGenerator $jsonGenerator
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \WeltPixel\GoogleTagManager\Model\Api $apiModel,
        \WeltPixel\GoogleTagManager\Model\JsonGenerator $jsonGenerator,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->apiModel = $apiModel;
        $this->jsonGenerator = $jsonGenerator;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $msg = $this->_validateParams($params);
        $apiOptions = ['variables', 'triggers', 'tags'];

        $formData = [];
        parse_str($params['form_data'], $formData);
        $apiParams = $this->_parseParams($formData);

        if (!count($msg)) {
            foreach ($apiOptions as $option) {
                try {
                    $result = $this->apiModel->
                    createItem(
                        $option,
                        $params['account_id'],
                        $params['container_id'],
                        $params['ua_tracking_id'],
                        $params['ip_anonymization'],
                        $params['display_advertising'],
                        $apiParams
                    );
                    $msg = array_merge($msg, $result);
                } catch (\Exception $ex) {
                    $msg[] = $ex->getMessage();
                }
            }

            /** Delete dynamic variables if not used anymore */
            try {
                $result = $this->apiModel->deleteItem('variables', $params['account_id'], $params['container_id'], $apiParams);
                $msg = array_merge($msg, $result);
            } catch (\Exception $ex) {
                $msg[] = $ex->getMessage();
            }
        }

        if (!count($msg)) {
            $msg[] = __('Nothing was done, items were already created.');
        }


        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($msg);
        return $resultJson;
    }


    /**
     * @param $formData
     * @return array
     */
    protected function _parseParams($formData) {
        $productDimensions = [
            'track_stockstatus' => [
                'enabled' => false
            ],
            'track_reviewscount' => [
                'enabled' => false
            ],
            'track_reviewsscore' => [
                'enabled' => false
            ],
            'track_saleproduct' => [
                'enabled' => false
            ]
        ];

        $customDimensions = [
            'custom_dimension_customerid' => [
                'enabled' => false
            ],
            'custom_dimension_customergroup' => [
                'enabled' => false
            ],
            'custom_dimension_pagetype' => [
                'enabled' => false
            ]
        ];

        $productCustomAttributeDimensions = [];


        if (isset($formData['groups']['general']['fields'])) {
            $formFields = $formData['groups']['general']['fields'];

            /** Gather the product dimensions */
            foreach ($productDimensions as $trackId => &$options) {
                $options['enabled'] = $formFields[$trackId]['value'];

                $indexnumber = $trackId.'_indexnumber';
                if ( isset ($formFields[$indexnumber]) ) {
                    $options['type'] = \WeltPixel\GoogleTagManager\Model\Dimension::DIMENSION_TYPE . $formFields[$indexnumber]['value'];
                    $options['track_option'] = \WeltPixel\GoogleTagManager\Model\Dimension::DIMENSION_TYPE;
                    $options['index'] = $formFields[$indexnumber]['value'];
                }

            }

            for ($i = 1; $i<=5; $i++) {
                $customAttributeTrackPrefix = 'track_custom_attribute_'. $i;
                $indexnumber = $customAttributeTrackPrefix .'_indexnumber';
                $type = $customAttributeTrackPrefix.'_type';
                $attributeCode = $customAttributeTrackPrefix.'_code';
                $attributeName = $customAttributeTrackPrefix.'_name';

                if (isset($formFields[$customAttributeTrackPrefix]) && $formFields[$customAttributeTrackPrefix]['value'] == 1) {
                    $options = [];
                    $options['enabled'] = true;
                    $options['type'] = $formFields[$type]['value'] . $formFields[$indexnumber]['value'];
                    $options['track_option'] = $formFields[$type]['value'];
                    $options['index'] = $formFields[$indexnumber]['value'];
                    $options['attribute_code'] = $formFields[$attributeCode]['value'];
                    $options['attribute_name'] = $formFields[$attributeName]['value'];
                    $productCustomAttributeDimensions[] = $options;
                }
            }



            /** Gather the customer and hit dimensions */
            foreach ($customDimensions as $index => &$options) {
                $options['enabled'] = $formFields[$index]['value'];
                $indexnumber = $index.'_indexnumber';
                if ( isset ($formFields[$indexnumber]) ) {
                    $options['index'] = $formFields[$indexnumber]['value'];
                }
            }
        }

        return [
            'product_dimensions' => $productDimensions,
            'custom_dimensions' => $customDimensions,
            'product_custom_attributes_dimensions' => $productCustomAttributeDimensions
        ];
    }

    /**
     * @param $params
     * @return array
     */
    protected function _validateParams($params) {
        $accountId      = $params['account_id'];
        $containerId    = $params['container_id'];
        $uaTrackingId   = $params['ua_tracking_id'];

        $msg = [];

        if (!strlen(trim($accountId))) {
            $msg[] = __('Account Id must be specified');
        }

        if (!strlen(trim($containerId))) {
            $msg[] = __('Container Id must be specified');
        }

        if (!strlen(trim($uaTrackingId))) {
            $msg[] = __('Universal Tracking Id must be specified');
        }

        return $msg;
    }

}
