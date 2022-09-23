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
 * @category    Ced
 * @package     Ced_Appointment
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Ui\DataProvider\Product\Form\Modifier;

use Ced\Booking\Helper\Data;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class NonWorkingDates
 * @package Ced\Booking\Ui\DataProvider\Product\Form\Modifier
 */
class NonWorkingDates extends AbstractModifier
{
    const GROUP_NON_WORKING_DATES_NAME = 'ced-booking-nonworkingdates';
    const GROUP_NON_WORKING_SCOPE = 'data.product';
    const GROUP_NON_WORKING_PREVIOUS_NAME = 'appointment-slots';
    const GROUP_NON_WORKING_DEFAULT_SORT_ORDER = 20;
    const ADD_NON_WORKING_DATES = 'non_working_dates';

    /**
     * @var string
     * @since 101.0.0
     */
    protected $scopeName;

    /**
     * @var array
     * @since 101.0.0
     */
    protected $meta = [];

    /**
     * AppointmentSlots constructor.
     * @param string $scopeName
     */
    public function __construct(
        LocatorInterface $locator,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Data $helper,
        $scopeName = ''
    ) {
        $this->scopeName = $scopeName;
        $this->locator = $locator;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $product = $this->locator->getProduct();
        $this->meta = $meta;
        if (in_array($product->getTypeId(), $this->helper->getEnabledBookingTypes())) {
            $this->createNonWorkingDatesPanel();
            unset($this->meta['container_visibility']);
            unset($this->meta['related']);
            unset($this->meta['gift-options']);
        }
        return $this->meta;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $nonWorkingDates = $product->getData('non_working_dates');
        $types = $this->helper->getAllBookingTypes();
        if (in_array($product->getTypeId(), $types)) {
            if ($nonWorkingDates) {
                $nonWorkingDatesArray = $this->jsonHelper->jsonDecode($nonWorkingDates);
                $data[$this->locator->getProduct()->getId()]['product'][static::ADD_NON_WORKING_DATES] = $nonWorkingDatesArray;
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    protected function createNonWorkingDatesPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_NON_WORKING_DATES_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Add Non Working Dates'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_NON_WORKING_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $this->meta,
                                    static::GROUP_NON_WORKING_PREVIOUS_NAME,
                                    static::GROUP_NON_WORKING_DEFAULT_SORT_ORDER
                                ),
                            ],
                        ],
                    ],
                    'children' => [
                        static::ADD_NON_WORKING_DATES => $this->getNonWorkingDates(),
                    ],
                ]
            ]
        );
        return $this;
    }

    /** add non working dates/unavailable dates */
    private function getNonWorkingDates()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'component' => 'Magento_Catalog/js/components/dynamic-rows-tier-price',
                        'label' => __('Add Non Working Dates'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'disabled' => false,
                        'required' => false
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'start_date' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Text::NAME,
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope' => 'start_date',
                                        'label' => __('Start Date'),
                                        'sortOrder' => 10,
                                        'validation' => [
                                            'required-entry' => true
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'end_date' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'dataScope' => 'end_date',
                                        'label' => __('End Date'),
                                        'validation' => [
                                            'required-entry' => true
                                        ],
                                        'sortOrder' => 20,
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                        'sortOrder' => 50,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
