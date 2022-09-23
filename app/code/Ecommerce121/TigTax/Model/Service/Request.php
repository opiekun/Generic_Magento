<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model\Service;

use Ecommerce121\TigTax\Model\Resource\Logger as ResponseErrorLogger;
use Ecommerce121\TigTax\Model\StoreConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Ecommerce121\TigTax\Model\Logger\Logger;

class Request
{
    private const NUMBER_OF_RETRY = 3;

    private $failedAttempts = 0;

    /**
     * @var StoreConfig
     */
    private $storeConfig;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var ResponseErrorLogger
     */
    private $responseErrorLogger;

    /**
     * @param CurlFactory $curlFactory
     * @param StoreConfig $storeConfig
     * @param Logger $logger
     * @param Json $json
     * @param ResponseErrorLogger $responseErrorLogger
     */
    public function __construct(
        CurlFactory $curlFactory,
        StoreConfig $storeConfig,
        Logger $logger,
        Json $json,
        ResponseErrorLogger $responseErrorLogger
    ) {
        $this->curlFactory = $curlFactory;
        $this->storeConfig = $storeConfig;
        $this->logger = $logger;
        $this->json = $json;
        $this->responseErrorLogger = $responseErrorLogger;
    }

    /**
     * @param string $uriPath
     * @param bool $postMethod
     * @return array
     * @throws LocalizedException
     */
    public function execute(string $uriPath, $postMethod = false): array
    {
        $body = '';
        try {
            /** @var Curl $curl */
            $curl = $this->curlFactory->create();
            $curl->setHeaders($this->getHeaders());
            $url = $this->getBaseUrl() . $uriPath;
            $this->logger->debug($url);
            $curl->setTimeout(30);
            $postMethod ? $curl->post($url, []) : $curl->get($url);
            $body = $curl->getBody();

            if ($curl->getStatus() !== 200) {
                $this->responseErrorLogger->log($url, $curl->getStatus(), $body);
                throw new LocalizedException(__('TigTax service has failed with status: %1', $curl->getStatus()));
            }

            $result = $this->json->unserialize($body);
            $this->failedAttempts = 0;

            return $result;
        } catch (\Exception $e) {
            $this->failedAttempts++;

            $this->logger->critical(
                $e->getMessage(),
                [
                    'url' => $this->getBaseUrl() . $uriPath,
                    'headers' => $this->getHeaders(),
                    'body' => $body,
                    'attempt' =>  $this->failedAttempts,
                ]
            );

            if ($this->failedAttempts !== self::NUMBER_OF_RETRY) {
                sleep(3);
                return $this->execute($uriPath, $postMethod);
            }

            $this->responseErrorLogger->log($url, $curl->getStatus(), $e->getMessage());

            throw new FailedRequestException(__('TigTax service has failed. See logs for more information.'));
        }
    }

    /**
     * @return string
     */
    private function getBaseUrl(): string
    {
        if (substr($this->storeConfig->getApiUrl(), -1) === '/') {
            return $this->storeConfig->getApiUrl();
        }

        return $this->storeConfig->getApiUrl() . '/';
    }

    /**
     * @return array
     */
    private function getHeaders(): array
    {
        return [
            'ApiKey' => $this->storeConfig->getApiKey(),
            'AppVersion' => $this->storeConfig->getAppVersion(),
            'Application' => $this->storeConfig->getAppName(),
            'Content-type' => 'application/json',
        ];
    }
}
