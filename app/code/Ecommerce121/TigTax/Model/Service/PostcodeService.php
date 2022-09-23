<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model\Service;

use Magento\Framework\Exception\LocalizedException;

class PostcodeService
{
    private const ENDPOINT = 'postcodes';

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $state
     * @return array
     * @throws LocalizedException
     */
    public function getPostcodes(string $state): array
    {
        return $this->request->execute(self::ENDPOINT . '?state=' . $state);
    }
}
