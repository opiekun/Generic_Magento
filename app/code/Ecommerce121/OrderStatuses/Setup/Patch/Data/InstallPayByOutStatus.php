<?php

declare(strict_types=1);

namespace Ecommerce121\OrderStatuses\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Psr\Log\LoggerInterface;


class InstallPayByOutStatus implements DataPatchInterface
{
    const ORDER_STATUS_PAY_BY_PO_CODE = 'pending_po';
    const ORDER_STATUS_PAY_BY_PO_LABEL = 'Paid by Po';

    /**
     * @var StatusFactory
     */
    private $statusFactory;

    /**
     * @var StatusResourceFactory
     */
    private $statusResourceFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        LoggerInterface $logger
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->logger = $logger;
    }

    /**
     * @return InstallPayByOutStatus
     * @throws Exception
     */
    public function apply(): InstallPayByOutStatus
    {
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();

        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => self::ORDER_STATUS_PAY_BY_PO_CODE,
            'label' => self::ORDER_STATUS_PAY_BY_PO_LABEL,
        ]);

        try {
            $statusResource->save($status);
        } catch (AlreadyExistsException $exception) {
            $this->logger->critical('Order status: ' . self::ORDER_STATUS_PAY_BY_PO_CODE . ' already exists');
        }

        $status->assignState(Order::STATE_NEW, false, true);

        return $this;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
