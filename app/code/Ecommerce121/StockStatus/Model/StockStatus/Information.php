<?php

declare(strict_types=1);

namespace Ecommerce121\StockStatus\Model\StockStatus;

use Amasty\Stockstatus\Model\Stockstatus\Information as AmastyInformation;

class Information extends AmastyInformation
{
    private const ADDITIONAL_CONTENT = 'additional_content';

    /**
     * @return mixed|null
     */
    public function getAdditionalContent() : ? string
    {
        return $this->_get(static::ADDITIONAL_CONTENT);
    }

    /**
     * @param string|null $additionalContent
     */
    public function setAdditionalContent(? string $additionalContent) {
        $this->setData(static::ADDITIONAL_CONTENT, $additionalContent);
    }
}
