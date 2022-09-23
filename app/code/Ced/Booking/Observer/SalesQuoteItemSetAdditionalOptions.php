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
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Observer;

use Ced\Booking\Helper\Data;
use Magento\Catalog\Model\Product\Option\Type\DefaultType;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SalesQuoteItemSetAdditionalOptions
 * @package Ced\Booking\Observer
 */
class SalesQuoteItemSetAdditionalOptions implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * SalesQuoteItemSetAdditionalOptions constructor.
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        ProductRepository $productRepository,
        Data $helper
    ) {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getQuoteItem();
        $postData = $this->request->getPostValue();
        $bookingTypes = $this->helper->getAllBookingTypes();

        if (isset($postData[0])) {
            return $this;
        }

        if (in_array($item->getProduct()->getTypeId(), $bookingTypes)) {
            $data = ['booking' => false, 'options' => []];

            /** get booking additional options */
            if (isset($postData['item'][$item->getProductId()])) {

                /** order creation from admin panel and add new item in order */
                $data = $this->getAdditionalOptions($postData['item'][$item->getProductId()], $item);
            } elseif ($item->getId() && isset($postData['item']) && is_array($postData['item'])) {

                /** order edit from admin panel and update/edit item in order */
                $data = $this->getAdditionalOptions($postData['item'][$item->getId()], $item);
            } elseif (!$item->getId() && isset($postData['item']) && is_array($postData['item'])) {

                /** order creation from admin panel and update/edit item in order when quote not created */
                $pdata = array_values($postData['item']);
                $data = $this->getAdditionalOptions($pdata[0], $item);
            } elseif (!empty($postData)) {

                /** add to cart at frontend */
                $data = $this->getAdditionalOptions($postData, $item);
            }

            /** add custom option with booking additional options */
            $optionIds = $item->getOptionByCode('option_ids');

            if (isset($optionIds)) {
                $options = [];
                foreach (explode(',', $optionIds->getValue()) as $optionId) {
                    $option = $item->getProduct()->getOptionById($optionId);
                    if ($option) {
                        $itemOption = $item->getOptionByCode('option_' . $option->getId());
                        /** @var $group DefaultType */
                        $group = $option->groupFactory($option->getType())
                            ->setOption($option)
                            ->setConfigurationItem($item)
                            ->setConfigurationItemOption($itemOption);

                        if ('file' == $option->getType()) {
                            $downloadParams = $item->getFileDownloadParams();
                            if ($downloadParams) {
                                $url = $downloadParams->getUrl();
                                if ($url) {
                                    $group->setCustomOptionDownloadUrl($url);
                                }
                                $urlParams = $downloadParams->getUrlParams();
                                if ($urlParams) {
                                    $group->setCustomOptionUrlParams($urlParams);
                                }
                            }
                        }

                        $options[] = [
                            'label' => $option->getTitle(),
                            'value' => $group->getFormattedOptionValue($itemOption->getValue()),
                            'print_value' => $group->getPrintableOptionValue($itemOption->getValue()),
                            'option_id' => $option->getId(),
                            'option_type' => $option->getType(),
                            'custom_view' => $group->isCustomizedView(),
                        ];
                    }
                }
            }

            /** set options in item */
            if ($data['booking'] && !empty($data['options'])) {
                $item->addOption(
                    [
                        'code' => 'additional_options',
                        'value' => json_encode($data['options']),
                    ]
                );
            }

            return $item;
        }
    }

    /**
     * @param $pitem
     * @param $item
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getAdditionalOptions($pitem, $item)
    {
        $totalPrice = 0;
        $booking = false;
        $additionalOptions = [];
        if ($item->getProduct()->getTypeId() == 'appointment') {
            if (isset($pitem['appointment_selected_date']) && isset($pitem['appointment_selected_time'])) {
                $booking = true;
                $additionalOptions[] = [
                    'code' => 'selected_appointment_date',
                    'label' => __('Date'),
                    'value' => $pitem['appointment_selected_date'],
                ];
                $additionalOptions[] = [
                    'code' => 'selected_appointment_slots',
                    'label' => __('Timing'),
                    'value' => $pitem['appointment_selected_time'],
                ];

                if (isset($pitem['service_type'])) {
                    if ($pitem['service_type'] == 'branch') {
                        $additionalOptions[] = [
                            'code' => 'service_type',
                            'label' => __('Service'),
                            'value' => __('At Shop'),
                        ];
                        $additionalOptions[] = [
                            'code' => 'branch',
                            'label' => __('Shop Location'),
                            'value' => $pitem['branch'],
                        ];
                    } elseif ($pitem['service_type'] == 'home_service') {
                        $additionalOptions[] = [
                            'code' => 'service_type',
                            'label' => __('Service'),
                            'value' => __('At Home'),
                        ];
                        $additionalOptions[] = [
                            'code' => 'home_service_location',
                            'label' => __('Home Location'),
                            'value' => $pitem['home_service_location'],
                        ];
                    }
                } elseif (isset($pitem['branch'])) {
                    $additionalOptions[] = [
                        'code' => 'branch',
                        'label' => __('Shop Location'),
                        'value' => $pitem['branch'],
                    ];
                } elseif (isset($pitem['home_service_location'])) {
                    $additionalOptions[] = [
                        'code' => 'home_service_location',
                        'label' => __('Home Location'),
                        'value' => $pitem['home_service_location'],
                    ];
                }
            }
        } elseif ($item->getProduct()->getTypeId() == 'event') {
            if (isset($pitem['event_date'])) {
                $booking = true;
                if (is_array($pitem['event_date'])) {
                    $additionalOptions[] = [
                        'code' => 'event_date' . $item->getId(),
                        'label' => __('Event Date'),
                        'value' => implode($pitem['event_date'], ','),
                    ];
                } else {
                    $additionalOptions[] = [
                        'code' => 'event_date' . $item->getId(),
                        'label' => __('Event Date'),
                        'value' => $pitem['event_date'],
                    ];
                }
                if (isset($pitem['event_ticket']) && !empty($pitem['event_ticket'])) {
                    foreach ($pitem['event_ticket'] as $ticketId => $ticket) {
                        if ($ticket['qty'] != '' && $ticket['qty'] > 0) {
                            $additionalOptions[] = [
                                'code' => 'event_ticket_' . $ticketId . $item->getId(),
                                'label' => $ticket['name'],
                                'value' => $ticket['qty'] . ' * ' . $ticket['price'],
                            ];
                            $totalPrice += $ticket['qty'] * $ticket['price'];
                        }
                    }
                }
                $customOptionPrice = $this->getCustomOptionPrice($item->getProduct(), $totalPrice);
                if ($customOptionPrice > 0) {
                    $totalPrice = $totalPrice + $customOptionPrice;
                }

                $item->setCustomPrice($totalPrice);
                $item->setOriginalCustomPrice($totalPrice);
            }
        } elseif ($item->getProduct()->getTypeId() == 'rental') {
            if (isset($pitem['number_of_days'])) {
                $pitem['end_date'] = $pitem['number_of_days']==1 ? $pitem['start_date'] : date('Y-m-d', strtotime('+ ' . ($pitem['number_of_days']-1) . ' days', strtotime($pitem['start_date'])));
            }
            $booking = true;
            if (isset($pitem['rental_type'])) {
                if ($pitem['rental_type'] == 'daily') {
                    $additionalOptions[] = [
                        'code' => 'rental_type',
                        'label' => __('Rental'),
                        'value' => __('Day Wise'),
                    ];
                    $additionalOptions[] = [
                        'code' => 'start_date',
                        'label' => __('Start Date'),
                        'value' => $pitem['start_date'],
                    ];

                    $additionalOptions[] = [
                        'code' => 'end_date',
                        'label' => __('End Date'),
                        'value' => $pitem['end_date'],
                    ];

                    if (isset($pitem['total_value']) && $pitem['total_value'] != '') {
                        $additionalOptions[] = [
                            'code' => 'total_value',
                            'label' => __('Total Days'),
                            'value' => $pitem['total_value'],
                        ];
                    }

                    if ($pitem['shop_location'] != '') {
                        $additionalOptions[] = [
                            'code' => 'daily_rental_shop_location',
                            'label' => __('Shop Location'),
                            'value' => $pitem['shop_location'],
                        ];
                    }
                    if (isset($pitem['product_price']) && isset($pitem['qty'])) {
                        $totalPrice = $pitem['total_value'] * $pitem['product_price'];
                    }
                    $customOptionPrice = $this->getCustomOptionPrice($item->getProduct(), $pitem['product_price']);
                    if ($customOptionPrice > 0) {
                        $totalPrice = $totalPrice + ($customOptionPrice * $pitem['total_value']);
                    }
                } elseif ($pitem['rental_type'] == 'hourly') {
                    $additionalOptions[] = [
                        'code' => 'rental_type',
                        'label' => __('Rental'),
                        'value' => __('Hour Wise'),
                    ];
                    $additionalOptions[] = [
                        'code' => 'start_date',
                        'label' => __('Start Date'),
                        'value' => $pitem['start_date'],
                    ];

                    $additionalOptions[] = [
                        'code' => 'start_time',
                        'label' => __('Service Start Time'),
                        'value' => $pitem['start_time'],
                    ];

                    $additionalOptions[] = [
                        'code' => 'end_time',
                        'label' => __('Service End Time'),
                        'value' => $pitem['end_time'],
                    ];

                    if (isset($pitem['total_value']) && $pitem['total_value'] != '') {
                        $additionalOptions[] = [
                            'code' => 'total_value',
                            'label' => __('Total Hours'),
                            'value' => $pitem['total_value'],
                        ];
                    }

                    if ($pitem['shop_location'] != '') {
                        $additionalOptions[] = [
                            'code' => 'daily_rental_shop_location',
                            'label' => __('Shop Location'),
                            'value' => $pitem['shop_location'],
                        ];
                    }
                    if (isset($pitem['product_price']) && isset($pitem['qty'])) {
                        $totalPrice = $pitem['total_value'] * $pitem['product_price'];
                    }

                    $customOptionPrice = $this->getCustomOptionPrice($item->getProduct(), $pitem['product_price']);
                    if ($customOptionPrice > 0) {
                        $totalPrice = $totalPrice + ($customOptionPrice * $pitem['total_value']);
                    }
                }
                $item->setCustomPrice($totalPrice);
                $item->setOriginalCustomPrice($totalPrice);
            }
        }

        return ['options' => $additionalOptions, 'booking' => $booking];
    }

    /**
     * @param $product
     * @param $price
     * @return float|int
     * @throws NoSuchEntityException
     */
    protected function getCustomOptionPrice($product, $price)
    {
        $productOptions = $product->getTypeInstance(true)->getOrderOptions($product);
        $customOptionPrice = 0;
        if (isset($productOptions['options'])) {
            foreach ($productOptions['options'] as $key => $orderedOptvalue) {
                $product = $this->productRepository->getById($product->getId());
                foreach ($product->getOptions() as $option) {
                    if ($option->getValues() !== null) {
                        $values = $option->getValues();
                        foreach ($values as $key => $value) {
                            if (isset($orderedOptvalue['option_type']) && ($orderedOptvalue['option_type'] == 'checkbox' || $orderedOptvalue['option_type'] == 'multiple')) {
                                $optionIdsArray = explode(',', $orderedOptvalue['option_value']);

                                if (!empty($optionIdsArray)) {
                                    foreach ($optionIdsArray as $opid) {
                                        if ($value->getOptionId() == $orderedOptvalue['option_id'] && $value->getOptionTypeId() == $opid) {
                                            if ($value->getPriceType() == 'fixed') {
                                                $customOptionPrice += $value->getPrice();
                                            } else {
                                                $customOptionPrice += ($price * $value->getPrice()) / 100;
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($value->getOptionId() == $orderedOptvalue['option_id'] && $value->getOptionTypeId() == $orderedOptvalue['option_value']) {
                                    if ($value->getPriceType() == 'fixed') {
                                        $customOptionPrice += $value->getPrice();
                                    } else {
                                        $customOptionPrice += ($price * $value->getPrice()) / 100;
                                    }
                                }
                            }
                        }
                    } elseif ($option->getValues() === null) {
                        $optionData = $option->getData();
                        if ($optionData['option_id'] == $orderedOptvalue['option_id']) {
                            if ($optionData['price_type'] == 'fixed') {
                                $customOptionPrice += $optionData['price'];
                            } else {
                                $customOptionPrice += ($price * $optionData['price']) / 100;
                            }
                        }
                    }
                }
            }
        }
        return $customOptionPrice;
    }
}
