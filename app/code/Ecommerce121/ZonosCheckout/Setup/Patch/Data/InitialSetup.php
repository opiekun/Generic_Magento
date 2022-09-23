<?php

declare(strict_types=1);

namespace Ecommerce121\ZonosCheckout\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class InitialSetup implements DataPatchInterface
{
    const XML_PATH_ZONOS_CHECKOUT_STORE_ID = 'zonos_checkout_integration/zonos_checkout/store_id';
    const XML_PATH_ZONOS_CHECKOUT_SERVICE_TOKEN = 'zonos_checkout_integration/zonos_checkout/service_token';
    const XML_PATH_ZONOS_HELLO_SITE_KEY = 'zonos_hello_integration/zonos_hello_general/zonos_site_key';


    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param WriterInterface $writer
     * @param LoggerInterface $logger
     */
    public function __construct(
        WriterInterface $writer,
        LoggerInterface $logger
    ) {
        $this->writer = $writer;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        try {
            $this->writer->save(
                self::XML_PATH_ZONOS_CHECKOUT_STORE_ID,
                '5163'
            );
            $this->writer->save(
                self::XML_PATH_ZONOS_CHECKOUT_SERVICE_TOKEN,
                'c7d05bba-04df-48d3-b9fc-ff61d17bdba6'
            );
            $this->writer->save(
                self::XML_PATH_ZONOS_HELLO_SITE_KEY,
                '1DC8ZZ5JSWIYO'
            );
        } catch (\Exception $e) {
            $this->logger->info(
               ' Error while running ' .  __CLASS__ . ': ' . $e->getMessage()
            );
        }
    }


    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }


    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

}
