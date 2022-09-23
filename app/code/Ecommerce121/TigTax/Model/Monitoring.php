<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model;

use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Ecommerce121\TigTax\Model\Resource\Logger as ResponseErrorLogger;
use Ecommerce121\TigTax\Model\LockerFactory;

class Monitoring implements MessageInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ResponseErrorLogger
     */
    private $responseErrorLogger;

    /**
     * @var LockerFactory
     */
    private $lockerFactory;

    /**
     * @param UrlInterface $urlBuilder
     * @param ResponseErrorLogger $responseErrorLogger
     * @param LockerFactory $lockerFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ResponseErrorLogger $responseErrorLogger,
        LockerFactory $lockerFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->responseErrorLogger = $responseErrorLogger;
        $this->lockerFactory = $lockerFactory;
    }

    /**
     * @return bool
     */
    public function isDisplayed(): bool
    {
        $timestamp = (int) $this->lockerFactory->create()->getTime();
        if (!$timestamp) {
            return false;
        }

        $time = date('Y-m-d H:i:s', $timestamp);
        return $this->responseErrorLogger->ifLogsExistForTime($time);
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        // phpcs:ignore Magento2.Security.InsecureFunction
        return md5('TIGTAX_INVALID');
    }

    /**
     * @return Phrase
     */
    public function getText(): Phrase
    {
        return __(
            'TigTax integration requires your attention. Go <a href="%1">here</a> from more information.',
            $this->urlBuilder->getUrl('tigtax/error/messages')
        );
    }

    /**
     * @return int
     */
    public function getSeverity(): int
    {
        return self::SEVERITY_MAJOR;
    }
}
