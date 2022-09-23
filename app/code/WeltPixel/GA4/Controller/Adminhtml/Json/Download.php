<?php
namespace WeltPixel\GA4\Controller\Adminhtml\Json;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use WeltPixel\GA4\Model\JsonGenerator;

/**
 * Class Download
 * @package WeltPixel\GA4\Controller\Adminhtml\Json
 */
class Download extends Action
{

    /**
     * @var Http
     */
    protected $http;

    /**
     * @var JsonGenerator
     */
    protected $jsonGenerator;

    /**
     * Download constructor.
     * @param Context $context
     * @param Http $http
     * @param JsonGenerator $jsonGenerator
     */
    public function __construct(
        Context $context,
        Http $http,
        JsonGenerator $jsonGenerator
    ) {
        parent::__construct($context);
        $this->http = $http;
        $this->jsonGenerator = $jsonGenerator;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws FileSystemException
     */
    public function execute()
    {
        $response = $this->jsonGenerator->getGeneratedJsonContent();
        $this->http->getHeaders()->clearHeaders();
        $this->http->setHeader('Content-Type', 'application/json')
            ->setHeader("Content-Disposition", "attachment; filename=ga4Export.json")
            ->setBody($response);
    }
}
