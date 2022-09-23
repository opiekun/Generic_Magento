<?php

declare(strict_types=1);

namespace Ecommerce121\StockStatus\Block;

use Amasty\Stockstatus\Block\CustomStockStatus as AmastyCustomStockStatus;
use Magento\Framework\View\Element\Template;

class CustomStockStatus extends AmastyCustomStockStatus
{
    /**
     * Additional content template
     */
    const ADDITIONAL_CONTENT_TEMPLATE = 'Ecommerce121_StockStatus::additional_content.phtml';

    /**
     * @param string|null $additionalContent
     * @return string
     */
    public function getAdditionalContentHtml(?string $additionalContent): string
    {
        $html = '';
        if ($additionalContent) {
            $html = $this->_layout->createBlock(Template::class)
                ->setTemplate(self::ADDITIONAL_CONTENT_TEMPLATE)
                ->setData('additional_content', $additionalContent)
                ->toHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->hasStockstatusInformation()) {
            $html = parent::_toHtml();
        }

        return $html ?? '';
    }
}
