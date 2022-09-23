<?php
namespace WeltPixel\GA4\Model\Api;

/**
 * Class \WeltPixel\GA4\Model\Api\ConversionTracking
 */
class ConversionTracking extends \WeltPixel\GA4\Model\Api
{

    /**
     * Variable names
     */
    const VARIABLE_CONVERSION_TRACKING_CONVERSION_VALUE = 'WP - Conversion Value';
    const VARIABLE_CONVERSION_TRACKING_ORDER_ID = 'WP - Order ID';

    /**
     * Trigger names
     */
    const TRIGGER_CONVERSION_TRACKING_MAGENTO_CHECKOUT_SUCCESS_PAGE = 'WP - Magento Checkout Success Page';

    /**
     * Tag names
     */
    const TAG_CONVERSION_TRACKING_ADWORDS_CONVERSION_TRACKING = 'WP - AdWords Conversion Tracking';

    /**
     * Field names used in sending data to dataLayer
     */
    const FIELD_CONVERSION_TRACKING_CONVERSION_VALUE = 'wp_conversion_value';
    const FIELD_CONVERSION_TRACKING_ORDER_ID = 'wp_order_id';

    /**
     * Return list of variables for conversion tracking
     * @return array
     */
    private function _getConversionVariables()
    {
        $variables = [
            self::VARIABLE_CONVERSION_TRACKING_CONVERSION_VALUE => [
                'name' => self::VARIABLE_CONVERSION_TRACKING_CONVERSION_VALUE,
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
                        'value' => self::FIELD_CONVERSION_TRACKING_CONVERSION_VALUE
                    ]
                ]
            ],
            self::VARIABLE_CONVERSION_TRACKING_ORDER_ID => [
                'name' => self::VARIABLE_CONVERSION_TRACKING_ORDER_ID,
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
                        'value' => self::FIELD_CONVERSION_TRACKING_ORDER_ID
                    ]
                ]
            ]
        ];

        return $variables;
    }

    /**
     * Return list of triggers for conversion tracking
     * @return array
     */
    private function _getConversionTriggers()
    {
        $triggers = [
            self::TRIGGER_CONVERSION_TRACKING_MAGENTO_CHECKOUT_SUCCESS_PAGE => [
                'name' => self::TRIGGER_CONVERSION_TRACKING_MAGENTO_CHECKOUT_SUCCESS_PAGE,
                'type' => self::TYPE_TRIGGER_PAGEVIEW,
                'filter' => [
                    [
                        'type' => 'contains',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{Page URL}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => '/checkout/onepage/success'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $triggers;
    }

    /**
     * Return a list of tags for conversion tracking
     * @param array $triggers
     * @param array $params
     * @return array
     */
    private function _getConversionTags($triggers, $params)
    {
        $tags = [
            self::TAG_CONVERSION_TRACKING_ADWORDS_CONVERSION_TRACKING => [
                'name' => self::TAG_CONVERSION_TRACKING_ADWORDS_CONVERSION_TRACKING,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_CONVERSION_TRACKING_MAGENTO_CHECKOUT_SUCCESS_PAGE]
                ],
                'type' => self::TYPE_TAG_AWCT,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'enableConversionLinker',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'conversionValue',
                        'value' => '{{' . self::VARIABLE_CONVERSION_TRACKING_CONVERSION_VALUE . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'orderId',
                        'value' => '{{' . self::VARIABLE_CONVERSION_TRACKING_ORDER_ID . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'conversionId',
                        'value' => $params['conversion_id']
                    ],
                    [
                        'type' => 'template',
                        'key' => 'currencyCode',
                        'value' => $params['conversion_currency_code']
                    ],
                    [
                        'type' => 'template',
                        'key' => 'conversionLabel',
                        'value' => $params['conversion_label']
                    ],
                    [
                        'type' => 'template',
                        'key' => 'conversionCookiePrefix',
                        'value' => '_gcl'
                    ]
                ]
            ]
        ];

        return $tags;
    }

    /**
     * @return array
     */
    public function getConversionVariablesList()
    {
        return $this->_getConversionVariables();
    }

    /**
     * @return array
     */
    public function getConversionTriggersList()
    {
        return $this->_getConversionTriggers();
    }

    /**
     * @param array $triggers
     * @param array $params
     * @return array
     */
    public function getConversionTagsList($triggers, $params)
    {
        return $this->_getConversionTags($triggers, $params);
    }
}
