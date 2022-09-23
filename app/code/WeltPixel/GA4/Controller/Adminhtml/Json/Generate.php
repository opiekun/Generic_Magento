<?php
namespace WeltPixel\GA4\Controller\Adminhtml\Json;

use Magento\Backend\App\Action;

/**
 * Class Generate
 * @package WeltPixel\GA4\Controller\Adminhtml\Json
 */
class Generate extends Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \WeltPixel\GA4\Model\JsonGenerator
     */
    protected $jsonGenerator;

    /**
     * Version constructor.
     *
     * @param \WeltPixel\GA4\Model\JsonGenerator $jsonGenerator
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \WeltPixel\GA4\Model\JsonGenerator $jsonGenerator,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonGenerator = $jsonGenerator;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $jsonUrl = null;
        $msg = $this->_validateParams($params);

        $formData = [];
        parse_str($params['form_data'], $formData);

        if (!count($msg)) {
            try {
                $jsonUrl = $this->jsonGenerator->
                generateItemJson(
                    trim($params['account_id']),
                    trim($params['container_id']),
                    trim($params['measurement_id']),
                    trim($params['conversion_enabled']),
                    trim($params['conversion_id']),
                    trim($params['conversion_label']),
                    trim($params['conversion_currency_code']),
                    trim($params['remarketing_enabled']),
                    trim($params['remarketing_conversion_code']),
                    trim($params['remarketing_conversion_label']),
                    trim($params['public_id'])
                );
                $msg[] = __('Json was generated successfully. You can download the file by clicking on the Download Json button.');
            } catch (\Exception $ex) {
                $msg[] = $ex->getMessage();
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData([
            'msg' => $msg,
            'jsonUrl' => $jsonUrl
        ]);
        return $resultJson;
    }

    /**
     * @param $params
     * @return array
     */
    protected function _validateParams($params)
    {
        $accountId = $params['account_id'];
        $containerId = $params['container_id'];
        $measurementId = $params['measurement_id'];
        $publicId = $params['public_id'];
        $conversionEnabled  = $params['conversion_enabled'];
        $conversionId =  $params['conversion_id'];
        $conversionLabel =  $params['conversion_label'];
        $conversionCurrencyCode =  $params['conversion_currency_code'];
        $remarketingEnabled  = $params['remarketing_enabled'];
        $remarketingEnabledConversionCode = $params['remarketing_conversion_code'];

        $msg = [];

        if (!strlen(trim($accountId))) {
            $msg[] = __('Account ID must be specified');
        }

        if (!strlen(trim($containerId))) {
            $msg[] = __('Container ID must be specified');
        }

        if (!strlen(trim($measurementId))) {
            $msg[] = __('Measurement ID must be specified');
        }

        if (!strlen(trim($publicId))) {
            $msg[] = __('Public ID must be specified');
        }

        if ($conversionEnabled) {
            if (!strlen(trim($conversionId))) {
                $msg[] = __('Conversion ID must be specified');
            }

            if (!strlen(trim($conversionLabel))) {
                $msg[] = __('Conversion Label must be specified');
            }

            if (!strlen(trim($conversionCurrencyCode))) {
                $msg[] = __('Conversion Currency Code must be specified');
            }
        }

        if ($remarketingEnabled) {
            if (!strlen(trim($remarketingEnabledConversionCode))) {
                $msg[] = __('Remarketing Conversion Code must be specified');
            }
        }

        return $msg;
    }

}
