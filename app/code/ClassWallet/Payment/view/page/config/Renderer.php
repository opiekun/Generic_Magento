<?php

namespace ClassWallet\Payment\view\page\config;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\Metadata\MsApplicationTileImage;

class Renderer extends \Magento\Framework\View\Page\Config\Renderer
{
	public function __construct(
		Config $pageConfig,
        \Magento\Framework\View\Asset\MergeService $assetMergeService,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Psr\Log\LoggerInterface $logger,
        MsApplicationTileImage $msApplicationTileImage = null,

		\Magento\Catalog\Model\Session $catalogSession) {
		parent::__construct($pageConfig, $assetMergeService,$urlBuilder, $escaper, $string, $logger, $msApplicationTileImage);
		$this->catalogSession = $catalogSession;
	}
    public function renderHeadContent()
    {
		$result = parent::renderHeadContent();
		if($this->catalogSession->getIsClasswalletLogin()) {
//			$result .= "<style>div.bolt-checkout-button.bolt-multi-step-checkout{display:none;}</style>";
		}
       	return $result; 
    }
}
