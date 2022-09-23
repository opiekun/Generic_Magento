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

namespace Ced\Appointment\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Element\Select;
use Ced\Appointment\Helper\Data;
use Magento\Catalog\Model\Locator\LocatorInterface;

/**
 * Class AppointmentSlots
 * @package Ced\Appointment\Ui\DataProvider\Product\Form\Modifier
 */
class AppointmentSlots extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const GROUP_APPOINTMENT_SLOT_NAME = 'appointment-slots';
    const GROUP_APPOINTMENT_SLOT_SCOPE = 'data.product';
    const GROUP_APPOINTMENT_SLOT_PREVIOUS_NAME = 'booking-general-information';
    const GROUP_APPOINTMENT_SLOT_DEFAULT_SORT_ORDER = 10;
    const ADD_APPOINTMENT_SLOTS = 'add-appointment-slots-same-for-allweek';
    const ADD_APPOINTMENT_SLOTS_FOR_WEEKDAYS = 'add-appointment-slots-for-weekdays';
    const SLOT_FOR_MONDAY = 'monday_slots';
    const SLOT_FOR_TUESDAY = 'tuesday_slots';
    const SLOT_FOR_WEDNESDAY = 'wednesday_slots';
    const SLOT_FOR_THURSDAY = 'thursday_slots';
    const SLOT_FOR_FRIDAY = 'friday_slots';
    const SLOT_FOR_SATURDAY = 'saturday_slots';
    const SLOT_FOR_SUNDAY = 'sunday_slots';
    const WEEKDAY_STATUS = 'status';

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
        $scopeName = ''
    )
    {
        $this->scopeName = $scopeName;
        $this->locator = $locator;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $product = $this->locator->getProduct();
        if ($product->getTypeId() == 'appointment') {
            $this->createAppointmentSlotsPanel();
        }
        return $this->meta;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    protected function createAppointmentSlotsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_APPOINTMENT_SLOT_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Appointment Slots'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_APPOINTMENT_SLOT_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $this->meta,
                                    static::GROUP_APPOINTMENT_SLOT_PREVIOUS_NAME,
                                    static::GROUP_APPOINTMENT_SLOT_DEFAULT_SORT_ORDER
                                ),
                            ],
                        ],
                    ],
                    'children' => [
                        static::ADD_APPOINTMENT_SLOTS => $this->getAddAppointmentSlotsStructure(),
                        static::ADD_APPOINTMENT_SLOTS_FOR_WEEKDAYS => $this->getAddAppointmentSlotsForWeekdaysStructure()
                    ],
                ]
            ]
        );
        return $this;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        $appointmentSlots = $this->locator->getProduct()->getData('appointment_slots');
        if ($appointmentSlots) {
            $appointmentSlotsArray = $this->jsonHelper->jsonDecode($appointmentSlots);
            if ($this->locator->getProduct()->getData('same_slot_all_week_days') == 0) {
                if (count($appointmentSlotsArray) > 0) {
                    foreach ($appointmentSlotsArray as $day=>$array) {
                        $data[$this->locator->getProduct()->getId()]['product'][$day.'_slots'] = $array['slots'];
                        $data[$this->locator->getProduct()->getId()]['product'][$day.'status'] = $array['status'];
                    }
                }
            } else {
                $data[$this->locator->getProduct()->getId()]['product'][static::ADD_APPOINTMENT_SLOTS] = $appointmentSlotsArray;
            }
        }
        return $data;
    }

    /** add appointmenr slots same for all weeks */
    private function getAddAppointmentSlotsStructure()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'component' => 'Magento_Catalog/js/components/dynamic-rows-tier-price',
                        'label' => __('Add Slots'),
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
                        'slot_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Text::NAME,
                                        'formElement' => Hidden::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope' => 'slot_id',
                                        'label' => __('Slot Id'),
                                        'visible' => false,
                                        'value' => 0,
                                        'sortOrder' => 10,
                                    ],
                                ],
                            ],
                        ],
                        'start_time' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Text::NAME,
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope' => 'start_time',
                                        'label' => __('Start Time'),
                                        'sortOrder' => 10,
                                        'validation' => [
                                            'required-entry' => true
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'end_time' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'dataScope' => 'end_time',
                                        'label' => __('End Time'),
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

    private function getAddAppointmentSlotsForWeekdaysStructure()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => 50,
                        'additionalClasses' => 'different-slot-for-weekdays-container',
                        'label' => __('Add Slots'),
                    ],
                ],
            ],
            'children' => [
                static::SLOT_FOR_SUNDAY => $this->getslots(Data::SUNDAY_NAME,Data::SUNDAY_CODE),
                static::SLOT_FOR_SATURDAY => $this->getslots(Data::SATURDAY_NAME,Data::SATURDAY_CODE),
                static::SLOT_FOR_FRIDAY => $this->getslots(Data::FRIDAY_NAME,Data::FRIDAY_CODE),
                static::SLOT_FOR_THURSDAY => $this->getslots(Data::THURSDAY_NAME,Data::THURSDAY_CODE),
                static::SLOT_FOR_WEDNESDAY => $this->getslots(Data::WEDNESDAY_NAME,Data::WEDNESDAY_CODE),
                static::SLOT_FOR_TUESDAY => $this->getslots(Data::TUESDAY_NAME,Data::TUESDAY_CODE),
                static::SLOT_FOR_MONDAY => $this->getslots(Data::MONDAY_NAME,Data::MONDAY_CODE)
            ],
        ];
    }


    /** add appointment slots for week days */
    private function getslots($dayName,$dayCode)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => 50,
                        'label' => __($dayName),
                    ],
                ],
            ],
            'children' => [
                'status'  => $this->getWeekdaysStatus(10,$dayCode.static::WEEKDAY_STATUS),
                $dayCode.'_slots'  => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'dynamicRows',
                                'component' => 'Magento_Catalog/js/components/dynamic-rows-tier-price',
                                'renderDefaultRecord' => false,
                                'recordTemplate' => 'slots',
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
                        'slots' => [
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
                                'slot_id' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'dataType' => Text::NAME,
                                                'formElement' => Hidden::NAME,
                                                'componentType' => Field::NAME,
                                                'dataScope' => 'slot_id',
                                                'label' => __('Slot Id'),
                                                'visible' => false,
                                                'value' => 0,
                                                'sortOrder' => 10,
                                            ],
                                        ],
                                    ],
                                ],
                                'start_time' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'dataType' => Text::NAME,
                                                'formElement' => Input::NAME,
                                                'componentType' => Field::NAME,
                                                'dataScope' => 'start_time',
                                                'label' => __('Start Time'),
                                                'validation' => [
                                                    'required-entry' => true
                                                ],
                                                'sortOrder' => 10,
                                            ],
                                        ],
                                    ],
                                ],

                                'end_time' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => Input::NAME,
                                                'componentType' => Field::NAME,
                                                'dataType' => Text::NAME,
                                                'dataScope' => 'end_time',
                                                'label' => __('End Time'),
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
                ]

            ]
        ];
    }

    /**
     * @param $sortOrder
     * @param $datascope
     * @return array
     */
    private function getWeekdaysStatus($sortOrder,$datascope)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'dataScope' => $datascope,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->getDayStatusOptions(),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    private function getDayStatusOptions()
    {
        $options = [
            ['value'=>'closed','label'=>__('Closed')],
            ['value'=>'open','label'=>__('Open')]
        ];
        return $options;
    }
}
