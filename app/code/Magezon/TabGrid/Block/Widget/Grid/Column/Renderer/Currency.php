<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_TabGrid
 * @copyright Copyright (C) 2017 Magezon (https://magezon.com)
 */

namespace Magezon\TabGrid\Block\Widget\Grid\Column\Renderer;

class Currency extends \Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var int
     */
    protected $_defaultWidth = 100;

    /**
     * Currency objects cache
     *
     * @var \Magento\Framework\DataObject[]
     */
    protected static $_currencies = [];

    /**
     * Application object
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Directory\Model\Currency\DefaultLocator
     */
    protected $_currencyLocator;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_defaultBaseCurrency;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param \Magezon\TabGrid\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magezon\TabGrid\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_currencyLocator = $currencyLocator;
        $this->_localeCurrency = $localeCurrency;
        $defaultBaseCurrencyCode = $this->_scopeConfig->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
            'default'
        );
        $this->_defaultBaseCurrency = $currencyFactory->create()->load($defaultBaseCurrencyCode);
    }

    /**
     * Get price currency
     *
     * @return \Magento\Framework\Pricing\PriceCurrencyInterface
     *
     * @deprecated
     */
    private function getPriceCurrency()
    {
        if ($this->priceCurrency === null) {
            $this->priceCurrency = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Pricing\PriceCurrencyInterface::class
            );
        }
        return $this->priceCurrency;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $price = $this->_getValue($row) ? $this->_getValue($row) : $this->getColumn()->getDefault();
        $displayPrice = $this->getPriceCurrency()->convertAndFormat($price, false);
        return $displayPrice;
    }

    /**
     * Returns currency code, false on error
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    protected function _getCurrencyCode($row)
    {
        if ($code = $this->getColumn()->getCurrencyCode()) {
            return $code;
        }
        if ($code = $row->getData($this->getColumn()->getCurrency())) {
            return $code;
        }

        return $this->_currencyLocator->getDefaultCurrency($this->_request);
    }

    /**
     * Get rate for current row, 1 by default
     *
     * @param \Magento\Framework\DataObject $row
     * @return float|int
     */
    protected function _getRate($row)
    {
        if ($rate = $this->getColumn()->getRate()) {
            return floatval($rate);
        }
        if ($rate = $row->getData($this->getColumn()->getRateField())) {
            return floatval($rate);
        }
        return $this->_defaultBaseCurrency->getRate($this->_getCurrencyCode($row));
    }

    /**
     * Returns HTML for CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return parent::renderCss() . ' a-right';
    }
}
