<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model\Service;

use Magento\Framework\Exception\LocalizedException;

class RateService
{
    private const ENDPOINT = 'rates';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $cached = [];

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $zipcode
     * @return array
     * @throws LocalizedException
     */
    public function getRates(string $zipcode): array
    {
        $request = self::ENDPOINT . '?zipcode=' . $zipcode;
        if (!isset($this->cached[$request])) {
            $this->cached[$request] = $this->request->execute($request, true);
        }

        return $this->cached[$request];
    }
}
