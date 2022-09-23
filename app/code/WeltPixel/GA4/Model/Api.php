<?php

namespace WeltPixel\GA4\Model;

/**
 * Class \WeltPixel\GA4\Model\Api
 */
class Api extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Item types
     */
    const TYPE_VARIABLE_DATALAYER = 'v';
    const TYPE_VARIABLE_CONSTANT = 'c';
    const TYPE_TRIGGER_CUSTOM_EVENT = 'customEvent';
    const TYPE_TRIGGER_PAGEVIEW = 'pageview';
    const TYPE_TAG_GAAWC = 'gaawc';
    const TYPE_TAG_GAAWE = 'gaawe';
    const TYPE_TAG_AWCT = 'awct';
    const TYPE_TAG_SP = 'sp';

    /**
     * Variable names
     */
    const VARIABLE_MEASUREMENT_ID = 'WP - MEASUREMENT ID';
    const VARIABLE_CUSTOMER_ID = 'WP - GA4 - customerId';
    const VARIABLE_CUSTOMER_GROUP = 'WP - GA4 - customerGroup';
    const VARIABLE_PAGE_TYPE = 'WP - GA4 - Page Type';
    const VARIABLE_ECOMMERCE_ITEMS = 'WP - GA4 - ecommerce.items';
    const VARIABLE_ECOMMERCE_PURCHASE_ITEMS = 'WP - GA4 - ecommerce.purchase.items';
    const VARIABLE_ECOMMERCE_ACTION_ITEMS = 'WP - GA4 - ecommerce.action.items';
    const VARIABLE_TRANSACTION_ID = 'WP - GA4 - transaction_id';
    const VARIABLE_COUPON = 'WP - GA4 - coupon';
    const VARIABLE_TAX = 'WP - GA4 - tax';
    const VARIABLE_SHIPPING = 'WP - GA4 - shipping';
    const VARIABLE_CURRENCY = 'WP - GA4 - currency';
    const VARIABLE_AFFILIATION = 'WP - GA4 - affiliation';
    const VARIABLE_ORDER_VALUE = 'WP - GA4 - Order Value';
    const VARIABLE_CUSTOMER_TOTAL_ORDER_COUNT = 'WP - GA4 - Customer - total_order_count';
    const VARIABLE_CUSTOMER_TOTAL_LIFETIME_VALUE = 'WP - GA4 - Customer - total_lifetime_value';
    const VARIABLE_PURCHASE_VALUE = 'WP - GA4 - Purchase Value';

    /**
     * Trigger names
     */
    const TRIGGER_SELECT_ITEM = 'WP - GA4 - select_item';
    const TRIGGER_GTM_DOM = 'WP - GA4 - gtm.dom';
    const TRIGGER_ADD_TO_CART = 'WP - GA4 - add_to_cart';
    const TRIGGER_REMOVE_FROM_CART = 'WP - GA4 - remove_from_cart';
    const TRIGGER_VIEW_ITEM = 'WP - GA4 - view_item';
    const TRIGGER_VIEW_ITEM_LIST = 'WP - GA4 - view_item_list';
    const TRIGGER_SELECT_PROMOTION = 'WP - GA4 - select_promotion';
    const TRIGGER_VIEW_PROMOTION = 'WP - GA4 - view_promotion';
    const TRIGGER_BEGIN_CHECKOUT = 'WP - GA4 - begin_checkout';
    const TRIGGER_PURCHASE = 'WP - GA4 - purchase';

    const TRIGGER_ALL_PAGES_ID = '2147479553';

    /**
     * Tag names
     */
    const TAG_MEASUREMENT_ID = 'WP - GA4';
    const TAG_ITEM_LIST_VIEWS_IMPRESSIONS = 'WP - GA4 - item list views/impressions';
    const TAG_PRODUCT_ITEM_LIST_CLICKS = 'WP - GA4 - product/item list clicks';
    const TAG_ITEM_ADD_TO_CART = 'WP - GA4 - add to cart';
    const TAG_ITEM_REMOVE_FROM_CART = 'WP - GA4 - remove from cart';
    const TAG_ITEM_VIEWS_IMPRESSIONS = 'WP - GA4 - item views/impressions';
    const TAG_VIEW_PROMOTION = 'WP - GA4 - View Promotion';
    const TAG_CLICK_PROMOTION = 'WP - GA4 - Click Promotion';
    const TAG_BEGIN_CHECKOUT = 'WP - GA4 - Begin Checkout';
    const TAG_PURCHASE = 'WP - GA4 - Purchase';

    /**
     * Return list of variables for api creation
     * @param $measurementId
     * @return array
     */
    private function _getVariables($measurementId)
    {
        $variables = [
            self::VARIABLE_MEASUREMENT_ID => [
                'name' => self::VARIABLE_MEASUREMENT_ID,
                'type' => self::TYPE_VARIABLE_CONSTANT,
                'parameter' => [
                    [
                        'type' => 'template',
                        'key' => 'value',
                        'value' => $measurementId
                    ]
                ]
            ],
            self::VARIABLE_PAGE_TYPE => [
                'name' => self::VARIABLE_PAGE_TYPE,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'pageType'
                    ]
                ]
            ],
            self::VARIABLE_ECOMMERCE_ITEMS => [
                'name' => self::VARIABLE_ECOMMERCE_ITEMS,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.items'
                    ]
                ]
            ],
            self::VARIABLE_ECOMMERCE_PURCHASE_ITEMS => [
                'name' => self::VARIABLE_ECOMMERCE_PURCHASE_ITEMS,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.items'
                    ]
                ]
            ],
            self::VARIABLE_ECOMMERCE_ACTION_ITEMS => [
                'name' => self::VARIABLE_ECOMMERCE_ACTION_ITEMS,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.action.items'
                    ]
                ]
            ],
            self::VARIABLE_CUSTOMER_ID => [
                'name' => self::VARIABLE_CUSTOMER_ID,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'customerId'
                    ]
                ]
            ],
            self::VARIABLE_CUSTOMER_GROUP => [
                'name' => self::VARIABLE_CUSTOMER_GROUP,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'customerGroup'
                    ]
                ]
            ],
            self::VARIABLE_TRANSACTION_ID => [
                'name' => self::VARIABLE_TRANSACTION_ID,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.transaction_id'
                    ]
                ]
            ],
            self::VARIABLE_COUPON => [
                'name' => self::VARIABLE_COUPON,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.coupon'
                    ]
                ]
            ],
            self::VARIABLE_TAX => [
                'name' => self::VARIABLE_TAX,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.tax'
                    ]
                ]
            ],
            self::VARIABLE_SHIPPING => [
                'name' => self::VARIABLE_SHIPPING,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.shipping'
                    ]
                ]
            ],
            self::VARIABLE_CURRENCY => [
                'name' => self::VARIABLE_CURRENCY,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.currency'
                    ]
                ]
            ],
            self::VARIABLE_AFFILIATION => [
                'name' => self::VARIABLE_AFFILIATION,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.affiliation'
                    ]
                ]
            ],
            self::VARIABLE_ORDER_VALUE => [
                'name' => self::VARIABLE_ORDER_VALUE,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'value'
                    ]
                ]
            ],
            self::VARIABLE_CUSTOMER_TOTAL_ORDER_COUNT => [
                'name' => self::VARIABLE_CUSTOMER_TOTAL_ORDER_COUNT,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.total_order_count'
                    ]
                ]
            ],
            self::VARIABLE_CUSTOMER_TOTAL_LIFETIME_VALUE => [
                'name' => self::VARIABLE_CUSTOMER_TOTAL_LIFETIME_VALUE,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.total_lifetime_value'
                    ]
                ]
            ],
            self::VARIABLE_PURCHASE_VALUE => [
                'name' => self::VARIABLE_PURCHASE_VALUE,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.value'
                    ]
                ]
            ]
        ];
        return $variables;
    }

    /**
     * Return list of triggers for api creation
     * @return array
     */
    private function _getTriggers()
    {
        $triggers = [
            self::TRIGGER_GTM_DOM => [
                'name' => self::TRIGGER_GTM_DOM,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'gtm.dom'
                            ]
                        ]
                    ]
                ],
                'filter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{Event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'gtm.dom'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_SELECT_ITEM => [
                'name' => self::TRIGGER_SELECT_ITEM,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'select_item'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_ADD_TO_CART => [
                'name' => self::TRIGGER_ADD_TO_CART,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'add_to_cart'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_REMOVE_FROM_CART => [
                'name' => self::TRIGGER_REMOVE_FROM_CART,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'remove_from_cart'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_SELECT_PROMOTION => [
                'name' => self::TRIGGER_SELECT_PROMOTION,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'select_promotion'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_BEGIN_CHECKOUT => [
                'name' => self::TRIGGER_BEGIN_CHECKOUT,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'begin_checkout'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_VIEW_ITEM_LIST => [
                'name' => self::TRIGGER_VIEW_ITEM_LIST,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'view_item_list'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_VIEW_ITEM => [
                'name' => self::TRIGGER_VIEW_ITEM,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'view_item'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_VIEW_PROMOTION => [
                'name' => self::TRIGGER_VIEW_PROMOTION,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'view_promotion'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_PURCHASE => [
                'name' => self::TRIGGER_PURCHASE,
                'type' => self::TYPE_TRIGGER_CUSTOM_EVENT,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'purchase'
                            ]
                        ]
                    ]
                ]
            ],

        ];
        return $triggers;
    }

    /**
     * Return list of tags for api creation
     * @param array $triggers
     * @return array
     */
    private function _getTags($triggers)
    {
        $tags = [
            self::TAG_MEASUREMENT_ID => [
                'name' => self::TAG_MEASUREMENT_ID,
                'firingTriggerId' => [
                    self::TRIGGER_ALL_PAGES_ID
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWC,
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'sendPageView',
                        'value' => "true"
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'measurementId',
                        'value' => '{{' . self::VARIABLE_MEASUREMENT_ID . '}}'
                    ]
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_ITEM_LIST_VIEWS_IMPRESSIONS => [
                'name' => self::TAG_ITEM_LIST_VIEWS_IMPRESSIONS,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_VIEW_ITEM_LIST]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'view_item_list'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_PRODUCT_ITEM_LIST_CLICKS => [
                'name' => self::TAG_PRODUCT_ITEM_LIST_CLICKS,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_SELECT_ITEM]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'select_item'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_ACTION_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_ITEM_ADD_TO_CART => [
                'name' => self::TAG_ITEM_ADD_TO_CART,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_ADD_TO_CART]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'add_to_cart'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_ACTION_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_ITEM_REMOVE_FROM_CART => [
                'name' => self::TAG_ITEM_REMOVE_FROM_CART,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_REMOVE_FROM_CART]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'remove_from_cart'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_ACTION_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_ITEM_VIEWS_IMPRESSIONS => [
                'name' => self::TAG_ITEM_VIEWS_IMPRESSIONS,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_VIEW_ITEM]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'view_item'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_VIEW_PROMOTION => [
                'name' => self::TAG_VIEW_PROMOTION,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_VIEW_PROMOTION]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'view_promotion'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_CLICK_PROMOTION => [
                'name' => self::TAG_CLICK_PROMOTION,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_SELECT_PROMOTION]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'select_promotion'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_BEGIN_CHECKOUT => [
                'name' => self::TAG_BEGIN_CHECKOUT,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_BEGIN_CHECKOUT]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'begin_checkout'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            self::TAG_PURCHASE => [
                'name' => self::TAG_PURCHASE,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_PURCHASE]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => self::TYPE_TAG_GAAWE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'total_order_count'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_TOTAL_ORDER_COUNT . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'total_lifetime_value'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_TOTAL_LIFETIME_VALUE . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'purchase'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_ECOMMERCE_PURCHASE_ITEMS . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'transaction_id'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_TRANSACTION_ID . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'affiliation'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_AFFILIATION . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'tax'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_TAX . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'shipping'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_SHIPPING . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'currency'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CURRENCY . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'coupon'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_COUPON . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'value'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_PURCHASE_VALUE . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => self::TAG_MEASUREMENT_ID
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
        ];

        return $tags;
    }

    /**
     * @param string $measurementId
     * @return array
     */
    public function getVariablesList($measurementId)
    {
        return $this->_getVariables($measurementId);
    }

    /**
     * @return array
     */
    public function getTriggersList()
    {
        return $this->_getTriggers();
    }

    /**
     * @param array $triggersMapping
     * @return array
     */
    public function getTagsList($triggersMapping)
    {
        return $this->_getTags($triggersMapping);
    }
}
