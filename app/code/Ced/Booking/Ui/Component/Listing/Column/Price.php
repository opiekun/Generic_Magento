<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Booking
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license   https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Price
 * @package Ced\Booking\Ui\Component\Listing\Column
 */
class Price extends \Magento\Catalog\Ui\Component\Listing\Columns\Price
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * Price constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(ContextInterface $context,
                                UiComponentFactory $uiComponentFactory,
                                \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Catalog\Model\ProductRepository $productRepository,
                                array $components = [],
                                array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $localeCurrency, $storeManager, $components, $data);
        $this->_productRepository = $productRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $store = $this->storeManager->getStore(
                $this->context->getFilterParam('store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
            );
            $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());

            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['type_id'] == 'event') {
                    $price = 0;
                    $_product = $this->_productRepository->getById($item['entity_id']);
                    if ($_product->getEventTickets()!='')
                    {
                        $tickets = json_decode($_product->getEventTickets(),true);
                        if (!empty($tickets))
                        {
                            $price = min(array_column($tickets,'ticket_price'));
                        }
                    }
                } else {
                    $price = $item[$fieldName];
                }
                $item['price'] = $currency->toCurrency(sprintf("%f", $price));
            }
        }
        return $dataSource;
    }
}
