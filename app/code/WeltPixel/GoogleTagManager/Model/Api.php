<?php

namespace WeltPixel\GoogleTagManager\Model;

use WeltPixel\GoogleTagManager\lib\Google\Client as Google_Client;

/**
 * Class \WeltPixel\GoogleTagManager\Model\Api
 */
class Api extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Item types
     */
    const TYPE_VARIABLE_DATALAYER = 'v';
    const TYPE_VARIABLE_CONSTANT = 'c';
    const TYPE_TRIGGER_CUSTOM_EVENT = 'customEvent';
    const TYPE_TRIGGER_LINK_CLICK = 'linkClick';
    const TYPE_TRIGGER_PAGEVIEW = 'pageview';
    const TYPE_TRIGGER_DOMREADY = 'domReady';
    const TYPE_TAG_UA = 'ua';
    const TYPE_TAG_AWCT = 'awct';
    const TYPE_TAG_SP = 'sp';

    /**
     * Variable names
     */
    const VARIABLE_UA_TRACKING = 'WP - UA Tracking ID';
    const VARIABLE_EVENTLABEL = 'WP - Event Label';
    const VARIABLE_EVENTVALUE = 'WP - Event Value';
    const VARIABLE_CUSTOMER_ID = 'WP - Customer ID';
    const VARIABLE_CUSTOMER_GROUP = 'WP - Customer Group';
    const VARIABLE_TRACK_STOCK_STATUS = 'WP - Stock Status';
    const VARIABLE_TRACK_REVIEW_COUNT = 'WP - Review Count';
    const VARIABLE_TRACK_REVIEW_SCORE = 'WP - Review Score';
    const VARIABLE_TRACK_SALE_PRODUCT = 'WP - Sale Product';
    const VARIABLE_PAGE_NAME = 'WP - Page Name';
    const VARIABLE_PAGE_TYPE = 'WP - Page Type';

    /**
     * Trigger names
     */
    const TRIGGER_PRODUCT_CLICK = 'WP - Product Click';
    const TRIGGER_GTM_DOM = 'WP - gtm.dom';
    const TRIGGER_ADD_TO_CART = 'WP - Add To Cart';
    const TRIGGER_REMOVE_FROM_CART = 'WP - Remove From Cart';
    const TRIGGER_ALL_PAGES = 'WP - All Pages';
    const TRIGGER_EVENT_IMPRESSION = 'WP - Event Impression';
    const TRIGGER_PROMOTION_CLICK = 'WP - Promotion Click';
    const TRIGGER_CHECKOUT_OPTION = 'WP - Checkout Option';
    const TRIGGER_CHECKOUT_STEPS = 'WP - Checkout Steps';
    const TRIGGER_PROMOTION_VIEW = 'WP - Promotion View';
    const TRIGGER_ADD_TO_WISHLIST = 'WP - Add To Wishlist';
    const TRIGGER_ADD_TO_COMPARE = 'WP - Add To Compare';
    /** Newsletter Module Related Triggers */
    const TRIGGER_NEWSLETTER_POPUP_IMPRESSION = 'WP - Newsletter Popup Impression';
    const TRIGGER_NEWSLETTER_POPUP_SUCCESS = 'WP - Newsletter Popup Success';
    const TRIGGER_NEWSLETTER_POPUP_FAILED = 'WP - Newsletter Popup Failed';
    const TRIGGER_NEWSLETTER_POPUP_IMPRESSION_STEP_1 = 'WP - Newsletter Popup Impression Step 1';
    const TRIGGER_NEWSLETTER_POPUP_CLOSED = 'WP - Newsletter Popup Closed';
    const TRIGGER_NEWSLETTER_EXITINTENT_IMPRESSION = 'WP - Exit Intent Impression';
    const TRIGGER_NEWSLETTER_EXITINTENT_SUCCESS = 'WP - Exit Intent Success';
    const TRIGGER_NEWSLETTER_EXITINTENT_FAILED = 'WP - Exit Intent Failed';
    const TRIGGER_NEWSLETTER_EXITINTENT_IMPRESSION_STEP_1 = 'WP - Exit Intent Impression Step 1';
    const TRIGGER_NEWSLETTER_EXITINTENT_CLOSED = 'WP - Exit Intent Closed';
    /** Newsletter Module Related Triggers */

    /**
     * Tag names
     */
    const TAG_GOOGLE_ANALYTICS = 'WP - Google Analytics';
    const TAG_PRODUCT_EVENT_CLICK = 'WP - Product Event - Click';
    const TAG_PRODUCT_EVENT_ADD_TO_CART = 'WP - Product Event - Add to Cart';
    const TAG_PRODUCT_EVENT_REMOVE_FROM_CART = 'WP - Product Event - Remove from Cart';
    const TAG_PRODUCT_EVENT_PRODUCT_IMPRESSIONS = 'WP - Product Event - Product Impressions';
    const TAG_CHECKOUT_STEP_OPTION = 'WP - Checkout Step Option';
    const TAG_CHECKOUT_STEP = 'WP - Checkout Step';
    const TAG_PROMOTION_IMPRESSION = 'WP - Promotion Impression';
    const TAG_PROMOTION_CLICK = 'WP - Promotion Click';
    const TAG_PRODUCT_EVENT_ADD_TO_WISHLIST = 'WP - Product Event - Add to Wishlist';
    const TAG_PRODUCT_EVENT_ADD_TO_COMPARE = 'WP - Product Event - Add to Compare';
    /** Newsletter Module Related Tags */
    const TAG_NEWSLETTER_POPUP_IMPRESSION = 'WP - Newsletter Popup Impression';
    const TAG_NEWSLETTER_POPUP_IMPRESSION_STEP_1 = 'WP - Newsletter Popup Impression Step 1';
    const TAG_NEWSLETTER_POPUP_SUCCESS = 'WP - Newsletter Popup Success';
    const TAG_NEWSLETTER_POPUP_FAILED = 'WP - Newsletter Popup Failed';
    const TAG_NEWSLETTER_POPUP_CLOSED = 'WP - Newsletter Popup Closed';
    const TAG_NEWSLETTER_EXITINTENT_IMPRESSION = 'WP - Exit Intent Impression';
    const TAG_NEWSLETTER_EXITINTENT_IMPRESSION_STEP_1 = 'WP - Exit Intent Impression Step 1';
    const TAG_NEWSLETTER_EXITINTENT_SUCCESS = 'WP - Exit Intent Success';
    const TAG_NEWSLETTER_EXITINTENT_FAILED = 'WP - Exit Intent Failed';
    const TAG_NEWSLETTER_EXITINTENT_CLOSED = 'WP - Exit Intent Closed';
    /** Newsletter Module Related Tags */

    /**
     * @var string
     */
    private $oauthUrl = 'http://www.oauth.weltpixel.com';

    /**
     * @var string
     */
    private $clientId = '343821107733-2ctqe2qsq8j80on7pe5k9idtqf76lhk1.apps.googleusercontent.com';

    /**
     * @var string
     */
    private $clientSecret = 'GhEXBWj7Rcrdvs438XFxv3tn';

    /**
     * @var \Google_Client
     */
    private $client = null;

    /**
     * @var array
     */
    private $scopes = [
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/tagmanager.readonly',
        'https://www.googleapis.com/auth/tagmanager.edit.containers',
    ];

    /**
     * @var string
     */
    protected $apiUrl = 'https://www.googleapis.com/tagmanager/v1/';

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context, $registry);
        $this->_urlBuilder = $urlBuilder;
        $this->_backendSession = $backendSession;
        $this->request = $request;
//        set_time_limit(0);
    }

    /**
     * Get Google_Client
     *
     * @return \Google_Client
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = new Google_Client();

            $this->client->setApplicationName('WeltPixel-GTM');
            $this->client->setClientId($this->clientId);
            $this->client->setClientSecret($this->clientSecret);
            $this->client->setScopes($this->scopes);

            $storeId = $this->request->getParam('store');
            $websiteId = $this->request->getParam('website');
            $redirectUrl = $this->_urlBuilder->getUrl("adminhtml/system_config/edit", ['section' => 'weltpixel_googletagmanager', 'website' => $websiteId, 'store' => $storeId]);
            $this->client->setState($redirectUrl);
            $this->client->setRedirectUri($this->oauthUrl);

            $code = $this->request->getParam('code');
            if ($code) {
                try {
                    $this->getClient()->authenticate($code);
                    $this->_backendSession->setAccessToken($this->client->getAccessToken());
                } catch (\Exception $ex) {
                }

                header('Location: ' . $redirectUrl);
                return;
            }

            $token = $this->_backendSession->getAccessToken();
            if ($token) {
                $this->client->setAccessToken($token);
            }
        }

        return $this->client;
    }

    /**
     * @return string
     */
    protected function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param $option
     * @param $accountId
     * @param $containerId
     * @param $uaTrackingId
     * @param $displayAdvertising
     * @param $apiParams
     * @return array|void
     */
    public function createItem($option, $accountId, $containerId, $uaTrackingId, $ipAnonymization, $displayAdvertising, $apiParams)
    {
        $result = [];
        switch ($option) {
            case 'variables':
                $result = $this->_createVariables($accountId, $containerId, $uaTrackingId, $apiParams);
                break;
            case 'triggers':
                $result = $this->_createTriggers($accountId, $containerId);
                break;
            case 'tags':
                $result = $this->_createTags($accountId, $containerId, $ipAnonymization, $displayAdvertising, $apiParams);
                break;
        }

        return $result;
    }

    /**
     * @param $option
     * @param $accountId
     * @param $containerId
     * @param $apiParams
     * @return array
     */
    public function deleteItem($option, $accountId, $containerId, $apiParams)
    {
        $result = [];
        switch ($option) {
            case 'variables':
                $result = $this->_deleteVariables($accountId, $containerId, $apiParams);
                break;
        }

        return $result;
    }

    /**
     * Create the variables using the API
     * @param $accountId
     * @param $containerId
     * @param $apiParams
     * @return array
     */
    protected function _deleteVariables($accountId, $containerId, $apiParams)
    {
        $existingVariables = $this->_getExistingVariables($accountId, $containerId);

        $result = [];
        $variableFlags = [];
        $variableIds = [];

        foreach ($existingVariables as $variable) {
            $variableFlags[$variable['name']] = true;
            $variableIds[$variable['name']] = $variable['variableId'];
        }

        $variablesToDelete = $this->_getVariablesToDelete($apiParams);

        foreach ($variablesToDelete as $name) {
            try {
                if (isset($variableFlags[$name])) {
                    $response = $this->_deleteVariable($accountId, $containerId, $variableIds[$name], $name);
                    if (empty($response)) {
                        $result[] = __('Successfully deleted variable: ') . $name;
                    } else {
                        $result[] = __('Error deleting variable: ') . $name;
                    }
                }
            } catch (\Exception $ex) {
                $result[] = $ex->getMessage();
            }
        }

        return $result;
    }

    /**
     * Create the variables using the API
     * @param $accountId
     * @param $containerId
     * @param $uaTrackingId
     * @param $apiParams
     * @return array
     */
    protected function _createVariables($accountId, $containerId, $uaTrackingId, $apiParams)
    {
        $existingVariables = $this->_getExistingVariables($accountId, $containerId);

        $result = [];
        $variableFlags = [];
        $variableIds = [];

        foreach ($existingVariables as $variable) {
            $variableFlags[$variable['name']] = true;
            $variableIds[$variable['name']] = $variable['variableId'];
        }

        $variablesToCreate = $this->_getVariables($uaTrackingId, $apiParams);

        foreach ($variablesToCreate as $name => $options) {
            try {
                /** Update already created variables */
                if (isset($variableFlags[$name])) {
                    $response = $this->_updateVariable($accountId, $containerId, $options, $variableIds[$name]);
                    if ($response['variableId']) {
                        $result[] = __('Successfully updated variable: ') . $response['name'];
                    } else {
                        $result[] = __('Error updating variable: ') . $response['name'];
                    }
                } else {
                    $response = $this->_createVariable($accountId, $containerId, $options);
                    if ($response['variableId']) {
                        $result[] = __('Successfully created variable: ') . $response['name'];
                    } else {
                        $result[] = __('Error creating variable: ') . $response['name'];
                    }
                }
            } catch (\Exception $ex) {
                $result[] = $ex->getMessage();
            }
        }

        return $result;
    }

    /**
     * Create the triggers using the API
     * @param $accountId
     * @param $containerId
     * @return array
     */
    protected function _createTriggers($accountId, $containerId)
    {
        $existingTriggers = $this->_getExistingTriggers($accountId, $containerId);

        $result = [];
        $triggerFlags = [];
        $triggerIds = [];

        foreach ($existingTriggers as $trigger) {
            $triggerFlags[$trigger['name']] = true;
            $triggerIds[$trigger['name']] = $trigger['triggerId'];
        }

        $triggersToCreate = $this->_getTriggers();

        foreach ($triggersToCreate as $name => $options) {
            try {
                /** Update already created triggers */
                if (isset($triggerFlags[$name])) {
                    $response = $this->_updateTrigger($accountId, $containerId, $options, $triggerIds[$name]);
                    if ($response['triggerId']) {
                        $result[] = __('Successfully updated trigger: ') . $response['name'];
                    } else {
                        $result[] = __('Error updating trigger: ') . $response['name'];
                    }
                } else {
                    $response = $this->_createTrigger($accountId, $containerId, $options);
                    if ($response['triggerId']) {
                        $result[] = __('Successfully created trigger: ') . $response['name'];
                    } else {
                        $result[] = __('Error creating trigger: ') . $response['name'];
                    }
                }
            } catch (\Exception $ex) {
                $result[] = $ex->getMessage();
            }
        }

        return $result;
    }

    /**
     * @param $accountId
     * @param $containerId
     * @param $ipAnonymization
     * @param $displayAdvertising
     * @param $apiParams
     * @return array
     */
    protected function _createTags($accountId, $containerId, $ipAnonymization, $displayAdvertising, $apiParams)
    {
        $ipAnonymization = ($ipAnonymization) ? 'true' : 'false';
        $displayAdvertising = ($displayAdvertising) ? 'true' : 'false';
        $existingTags = $this->_getExistingTags($accountId, $containerId);

        $result = [];
        $tagFlags = [];
        $tagIds = [];

        foreach ($existingTags as $tag) {
            $tagFlags[$tag['name']] = true;
            $tagIds[$tag['name']] = $tag['tagId'];
        }

        $triggersMapping = $this->_getTriggersMapping($accountId, $containerId);
        $tagsToCreate = $this->_getTags($triggersMapping, $ipAnonymization, $displayAdvertising, $apiParams);

        foreach ($tagsToCreate as $name => $options) {
            try {
                /** Update already created tags */
                if (isset($tagFlags[$name])) {
                    $response = $this->_updateTag($accountId, $containerId, $options, $tagIds[$name]);
                    if ($response['tagId']) {
                        $result[] = __('Successfully updated tag: ') . $response['name'];
                    } else {
                        $result[] = __('Error updating tag: ') . $response['name'];
                    }
                } else {
                    $response = $this->_createTag($accountId, $containerId, $options);
                    if ($response['tagId']) {
                        $result[] = __('Successfully created tag: ') . $response['name'];
                    } else {
                        $result[] = __('Error creating tag: ') . $response['name'];
                    }
                }
            } catch (\Exception $ex) {
                $result[] = $ex->getMessage();
            }
        }

        return $result;
    }

    /**
     * Return array with trigger name and trigger id, for usage in tags creation
     * @param $accountId
     * @param $containerId
     * @return array
     */
    protected function _getTriggersMapping($accountId, $containerId)
    {
        $triggers = $this->_getExistingTriggers($accountId, $containerId);
        $triggersMap = [];

        foreach ($triggers as $trigger) {
            $triggersMap[$trigger['name']] = $trigger['triggerId'];
        }

        return $triggersMap;
    }

    /**
     * Return list of variables for api creation
     * @param $uaTrackingId
     * @param $apiParams
     * @return array
     */
    private function _getVariables($uaTrackingId, $apiParams)
    {
        $variables = [
            self::VARIABLE_UA_TRACKING => [
                'name' => self::VARIABLE_UA_TRACKING,
                'type' => self::TYPE_VARIABLE_CONSTANT,
                'parameter' => [
                    [
                        'type' => 'template',
                        'key' => 'value',
                        'value' => $uaTrackingId
                    ]
                ]
            ],
            self::VARIABLE_EVENTLABEL => [
                'name' => self::VARIABLE_EVENTLABEL,
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
                        'value' => 'eventLabel'
                    ]
                ]
            ],
            self::VARIABLE_EVENTVALUE => [
                'name' => self::VARIABLE_EVENTVALUE,
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
                        'value' => 'eventValue'
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
            self::VARIABLE_PAGE_NAME => [
                'name' => self::VARIABLE_PAGE_NAME,
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
                        'value' => 'pageName'
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
            ]
        ];

        $productDimensions = $apiParams['product_dimensions'];

        if ($productDimensions['track_stockstatus']['enabled']) {
            $variables[self::VARIABLE_TRACK_STOCK_STATUS] = [
                'name' => self::VARIABLE_TRACK_STOCK_STATUS,
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
                        'value' => $productDimensions['track_stockstatus']['type']
                    ]
                ]
            ];
        }

        if ($productDimensions['track_reviewscount']['enabled']) {
            $variables[self::VARIABLE_TRACK_REVIEW_COUNT] = [
                'name' => self::VARIABLE_TRACK_REVIEW_COUNT,
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
                        'value' => $productDimensions['track_reviewscount']['type']
                    ]
                ]
            ];
        }

        if ($productDimensions['track_reviewsscore']['enabled']) {
            $variables[self::VARIABLE_TRACK_REVIEW_SCORE] = [
                'name' => self::VARIABLE_TRACK_REVIEW_SCORE,
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
                        'value' => $productDimensions['track_reviewsscore']['type']
                    ]
                ]
            ];
        }

        if ($productDimensions['track_saleproduct']['enabled']) {
            $variables[self::VARIABLE_TRACK_SALE_PRODUCT] = [
                'name' => self::VARIABLE_TRACK_SALE_PRODUCT,
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
                        'value' => $productDimensions['track_saleproduct']['type']
                    ]
                ]
            ];
        }

        $productCustomAttributeDimensions = $apiParams['product_custom_attributes_dimensions'];
        foreach ($productCustomAttributeDimensions as $custDimension) {
            if ($custDimension['enabled']) {
                $variableId = 'WP - Custom - ' . $custDimension['attribute_name'];
                $variables[$variableId] = [
                    'name' => $variableId,
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
                            'value' => $custDimension['type']
                        ]
                    ]
                ];
            }
        }

        return $variables;
    }

    /**
     * @param $apiParams
     * @return array
     */
    private function _getVariablesToDelete($apiParams)
    {
        $variablesToDelete = [];
        $productDimensions = $apiParams['product_dimensions'];

        if (!$productDimensions['track_stockstatus']['enabled']) {
            $variablesToDelete[] = self::VARIABLE_TRACK_STOCK_STATUS;
        }

        if (!$productDimensions['track_reviewscount']['enabled']) {
            $variablesToDelete[] = self::VARIABLE_TRACK_REVIEW_COUNT;
        }

        if (!$productDimensions['track_reviewsscore']['enabled']) {
            $variablesToDelete[] = self::VARIABLE_TRACK_REVIEW_SCORE;
        }

        if (!$productDimensions['track_saleproduct']['enabled']) {
            $variablesToDelete[] = self::VARIABLE_TRACK_SALE_PRODUCT;
        }

        return $variablesToDelete;
    }

    /**
     * Return list of triggers for api creation
     * @return array
     */
    private function _getTriggers()
    {
        $triggers = [
            self::TRIGGER_PRODUCT_CLICK => [
                'name' => self::TRIGGER_PRODUCT_CLICK,
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
                                'value' => 'productClick'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_GTM_DOM => [
                'name' => self::TRIGGER_GTM_DOM,
                'type' => self::TYPE_TRIGGER_DOMREADY
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
                                'value' => 'addToCart'
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
                                'value' => 'removeFromCart'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_ALL_PAGES => [
                'name' => self::TRIGGER_ALL_PAGES,
                'type' => self::TYPE_TRIGGER_PAGEVIEW
            ],
            self::TRIGGER_EVENT_IMPRESSION => [
                'name' => self::TRIGGER_EVENT_IMPRESSION,
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
                                'value' => 'impression'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_PROMOTION_CLICK => [
                'name' => self::TRIGGER_PROMOTION_CLICK,
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
                                'value' => 'promotionClick'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_CHECKOUT_OPTION => [
                'name' => self::TRIGGER_CHECKOUT_OPTION,
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
                                'value' => 'checkoutOption'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_CHECKOUT_STEPS => [
                'name' => self::TRIGGER_CHECKOUT_STEPS,
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
                                'value' => 'checkout'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_PROMOTION_VIEW => [
                'name' => self::TRIGGER_PROMOTION_VIEW,
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
                                'value' => 'promotionView'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_ADD_TO_WISHLIST => [
                'name' => self::TRIGGER_ADD_TO_WISHLIST,
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
                                'value' => 'addToWishlist'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_ADD_TO_COMPARE => [
                'name' => self::TRIGGER_ADD_TO_COMPARE,
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
                                'value' => 'addToCompare'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_POPUP_IMPRESSION => [
                'name' => self::TRIGGER_NEWSLETTER_POPUP_IMPRESSION,
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
                                'value' => 'newsletterPopupImpression'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_POPUP_IMPRESSION_STEP_1 => [
                'name' => self::TRIGGER_NEWSLETTER_POPUP_IMPRESSION_STEP_1,
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
                                'value' => 'newsletterPopupImpressionStep1'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_POPUP_SUCCESS => [
                'name' => self::TRIGGER_NEWSLETTER_POPUP_SUCCESS,
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
                                'value' => 'newsletterPopupSuccess'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_POPUP_FAILED => [
                'name' => self::TRIGGER_NEWSLETTER_POPUP_FAILED,
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
                                'value' => 'newsletterPopupFailed'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_POPUP_CLOSED => [
                'name' => self::TRIGGER_NEWSLETTER_POPUP_CLOSED,
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
                                'value' => 'newsletterPopupClosed'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_EXITINTENT_IMPRESSION => [
                'name' => self::TRIGGER_NEWSLETTER_EXITINTENT_IMPRESSION,
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
                                'value' => 'exitIntentImpression'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_EXITINTENT_IMPRESSION_STEP_1 => [
                'name' => self::TRIGGER_NEWSLETTER_EXITINTENT_IMPRESSION_STEP_1,
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
                                'value' => 'exitIntentImpressionStep1'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_EXITINTENT_CLOSED => [
                'name' => self::TRIGGER_NEWSLETTER_EXITINTENT_CLOSED,
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
                                'value' => 'exitIntentClosed'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_EXITINTENT_FAILED => [
                'name' => self::TRIGGER_NEWSLETTER_EXITINTENT_FAILED,
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
                                'value' => 'exitIntentFailed'
                            ]
                        ]
                    ]
                ]
            ],
            self::TRIGGER_NEWSLETTER_EXITINTENT_SUCCESS => [
                'name' => self::TRIGGER_NEWSLETTER_EXITINTENT_SUCCESS,
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
                                'value' => 'exitIntentSuccess'
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
     * @param bool $ipAnonymization
     * @param bool $displayAdvertising
     * @param array $apiParams
     * @return array
     */
    private function _getTags($triggers, $ipAnonymization, $displayAdvertising, $apiParams)
    {
        $dimensionAllOptions = $this->_getProductTrackOptions($apiParams, 'dimension', $this->_getAvailableDimensions());
        $dimensionCustomerOptions = $this->_getProductTrackOptions($apiParams, 'dimension', ['custom_dimension_customerid', 'custom_dimension_customergroup']);
        $dimensionCustomerPageOptions = $this->_getProductTrackOptions($apiParams, 'dimension', ['custom_dimension_customerid', 'custom_dimension_customergroup', 'custom_dimension_pagetype']);

        $tags = [
            self::TAG_PRODUCT_EVENT_CLICK => [
                'name' => self::TAG_PRODUCT_EVENT_CLICK,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_PRODUCT_CLICK]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setTrackerName',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useDebugVersion',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableLinkId',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Product Click'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionCustomerPageOptions
                ]
            ],
            self::TAG_PRODUCT_EVENT_ADD_TO_CART => [
                'name' => self::TAG_PRODUCT_EVENT_ADD_TO_CART,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_ADD_TO_CART]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setTrackerName',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useDebugVersion',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableLinkId',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Add to Cart'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'evenValue',
                        'value' => '{{' . self::VARIABLE_EVENTVALUE . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionAllOptions
                ]
            ],
            self::TAG_PRODUCT_EVENT_REMOVE_FROM_CART => [
                'name' => self::TAG_PRODUCT_EVENT_REMOVE_FROM_CART,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_REMOVE_FROM_CART]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setTrackerName',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useDebugVersion',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableLinkId',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Remove from Cart'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableLinkId',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'evenValue',
                        'value' => '{{' . self::VARIABLE_EVENTVALUE . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionAllOptions
                ]
            ],
            self::TAG_PRODUCT_EVENT_PRODUCT_IMPRESSIONS => [
                'name' => self::TAG_PRODUCT_EVENT_PRODUCT_IMPRESSIONS,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_EVENT_IMPRESSION]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setTrackerName',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useDebugVersion',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableLinkId',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Impression'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionCustomerPageOptions
                ]
            ],
            self::TAG_GOOGLE_ANALYTICS => [
                'name' => self::TAG_GOOGLE_ANALYTICS,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_ALL_PAGES]
                ],
                'tagFiringOption' => 'oncePerLoad',
                'type' => self::TYPE_TAG_UA,
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setTrackerName',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useDebugVersion',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useHashAutoLink',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_PAGEVIEW'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'decorateFormsAutoLink',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableLinkId',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'anonymizeIp'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => $ipAnonymization
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionAllOptions
                ]
            ],
            self::TAG_CHECKOUT_STEP_OPTION => [
                'name' => self::TAG_CHECKOUT_STEP_OPTION,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_CHECKOUT_OPTION]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Checkout Option'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionCustomerOptions
                ]
            ],
            self::TAG_CHECKOUT_STEP => [
                'name' => self::TAG_CHECKOUT_STEP,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_CHECKOUT_STEPS]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Checkout'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionCustomerOptions
                ]
            ],
            self::TAG_PROMOTION_IMPRESSION => [
                'name' => self::TAG_PROMOTION_IMPRESSION,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_PROMOTION_VIEW]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Promotion'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Promotion View'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionCustomerPageOptions
                ]
            ],
            self::TAG_PROMOTION_CLICK => [
                'name' => self::TAG_PROMOTION_CLICK,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_PROMOTION_CLICK]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Promotion Click'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionCustomerPageOptions
                ]
            ],
            self::TAG_PRODUCT_EVENT_ADD_TO_WISHLIST => [
                'name' => self::TAG_PRODUCT_EVENT_ADD_TO_WISHLIST,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_ADD_TO_WISHLIST]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Wishlist'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionAllOptions
                ]
            ],
            self::TAG_PRODUCT_EVENT_ADD_TO_COMPARE => [
                'name' => self::TAG_PRODUCT_EVENT_ADD_TO_COMPARE,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_ADD_TO_COMPARE]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "false"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'useEcommerceDataLayer',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Ecommerce'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Compare'
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'enableEcommerce',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'doubleClick',
                        'value' => $displayAdvertising
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ],
                    [
                        'type' => 'list',
                        'key' => 'fieldsToSet',
                        'list' => [
                            [
                                'type' => 'map',
                                'map' => [
                                    [
                                        'type' => 'template',
                                        'key' => 'fieldName',
                                        'value' => 'userId'
                                    ],
                                    [
                                        'type' => 'template',
                                        'key' => 'value',
                                        'value' => '{{' . self::VARIABLE_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $dimensionAllOptions
                ]
            ],
            self::TAG_NEWSLETTER_POPUP_IMPRESSION => [
                'name' => self::TAG_NEWSLETTER_POPUP_IMPRESSION,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_POPUP_IMPRESSION]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Newsletter Popup'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Impression'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_POPUP_IMPRESSION_STEP_1 => [
                'name' => self::TAG_NEWSLETTER_POPUP_IMPRESSION_STEP_1,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_POPUP_IMPRESSION_STEP_1]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Newsletter Popup'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Impression Step 1'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_POPUP_SUCCESS => [
                'name' => self::TAG_NEWSLETTER_POPUP_SUCCESS,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_POPUP_SUCCESS]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Newsletter Popup'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Success'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_POPUP_FAILED => [
                'name' => self::TAG_NEWSLETTER_POPUP_FAILED,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_POPUP_FAILED]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Newsletter Popup'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Failed'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_POPUP_CLOSED => [
                'name' => self::TAG_NEWSLETTER_POPUP_CLOSED,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_POPUP_CLOSED]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Newsletter Popup'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Closed'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_EXITINTENT_IMPRESSION => [
                'name' => self::TAG_NEWSLETTER_EXITINTENT_IMPRESSION,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_EXITINTENT_IMPRESSION]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Exit Intent'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Impression'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_EXITINTENT_IMPRESSION_STEP_1 => [
                'name' => self::TAG_NEWSLETTER_EXITINTENT_IMPRESSION_STEP_1,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_EXITINTENT_IMPRESSION_STEP_1]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Exit Intent'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Impression Step 1'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_EXITINTENT_SUCCESS => [
                'name' => self::TAG_NEWSLETTER_EXITINTENT_SUCCESS,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_EXITINTENT_SUCCESS]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Exit Intent'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Success'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_EXITINTENT_FAILED => [
                'name' => self::TAG_NEWSLETTER_EXITINTENT_FAILED,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_EXITINTENT_FAILED]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Exit Intent'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Failed'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
            self::TAG_NEWSLETTER_EXITINTENT_CLOSED => [
                'name' => self::TAG_NEWSLETTER_EXITINTENT_CLOSED,
                'firingTriggerId' => [
                    $triggers[self::TRIGGER_NEWSLETTER_EXITINTENT_CLOSED]
                ],
                'type' => self::TYPE_TAG_UA,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'nonInteraction',
                        'value' => "true"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'overrideGaSettings',
                        'value' => "true"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventCategory',
                        'value' => 'Exit Intent'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackType',
                        'value' => 'TRACK_EVENT'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventAction',
                        'value' => 'Closed'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'eventLabel',
                        'value' => '{{' . self::VARIABLE_EVENTLABEL . '}}'
                    ],
                    [
                        'type' => 'template',
                        'key' => 'trackingId',
                        'value' => '{{' . self::VARIABLE_UA_TRACKING . '}}'
                    ]
                ]
            ],
        ];

        return $tags;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @return mixed
     * @throws \Exception
     */
    protected function _getExistingVariables($accountId, $containerId)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/variables');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on variable listing: ') . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on variable listing: ') . $responseBody['error']['message']);
        }
        $existingVariables = (isset($responseBody['variables'])) ? $responseBody['variables'] : [];
        return $existingVariables;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @return mixed
     * @throws \Exception
     */
    protected function _getExistingTriggers($accountId, $containerId)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/triggers');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on trigger listing: ') . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on trigger listing: ') . $responseBody['error']['message']);
        }
        $existingTriggers = (isset($responseBody['triggers'])) ? $responseBody['triggers'] : [];

        return $existingTriggers;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @return mixed
     * @throws \Exception
     */
    protected function _getExistingTags($accountId, $containerId)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/tags');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on tag listing: ') . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on tag listing: ') . $responseBody['error']['message']);
        }
        $existingTags = (isset($responseBody['tags'])) ? $responseBody['tags'] : [];

        return $existingTags;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    protected function _createVariable($accountId, $containerId, $options)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        $postFields = json_encode($options);

        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/variables');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postFields)
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on variable creation: ') . $options['name'] . ' ' . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on variable creation: ') . $options['name'] . ' ' . $responseBody['error']['message']);
        }
        return $responseBody;
    }

    /**
     * Api should return empty response in case of success
     * @param $accountId
     * @param $containerId
     * @param $variableId
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    protected function _deleteVariable($accountId, $containerId, $variableId, $name)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/variables/' . $variableId);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: Bearer ' . $tokenInfo->access_token
                ]
            );

            $result = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on variable delete: ') . $name . ' ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    protected function _createTrigger($accountId, $containerId, $options)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        $postFields = json_encode($options);
        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/triggers');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postFields)
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on trigger creation:') . $options['name'] . ' ' . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on trigger creation: ') . $options['name'] . ' ' . $responseBody['error']['message']);
        }

        return $responseBody;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    protected function _createTag($accountId, $containerId, $options)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        $postFields = json_encode($options);
        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/tags');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postFields)
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on tag creation:') . $options['name'] . ' ' . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on tag creation: ') . $options['name'] . ' ' . $responseBody['error']['message']);
        }

        return $responseBody;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @param array $options
     * @param int $variableId
     * @return mixed
     * @throws \Exception
     */
    protected function _updateVariable($accountId, $containerId, $options, $variableId)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        $postFields = json_encode($options);

        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/variables/' . $variableId);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postFields)
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on variable update: ') . $options['name'] . ' ' . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on variable update: ') . $options['name'] . ' ' . $responseBody['error']['message']);
        }
        return $responseBody;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @param array $options
     * @param int $triggerId
     * @return mixed
     * @throws \Exception
     */
    protected function _updateTrigger($accountId, $containerId, $options, $triggerId)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        $postFields = json_encode($options);

        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/triggers/' . $triggerId);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postFields)
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on trigger update: ') . $options['name'] . ' ' . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on trigger update: ') . $options['name'] . ' ' . $responseBody['error']['message']);
        }
        return $responseBody;
    }

    /**
     * @param string $accountId
     * @param string $containerId
     * @param array $options
     * @param int $tagId
     * @return mixed
     * @throws \Exception
     */
    protected function _updateTag($accountId, $containerId, $options, $tagId)
    {
        $this->_apiCallLimitation();
        $tokenInfo = json_decode($this->getClient()->getAccessToken());
        $postFields = json_encode($options);

        try {
            $ch = curl_init($this->getApiUrl() . 'accounts/' . $accountId . '/containers/' . $containerId . '/tags/' . $tagId);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: ' . 'Bearer ' . $tokenInfo->access_token,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postFields)
                ]
            );
            $response = curl_exec($ch);
            $responseBody = json_decode($response, true);
        } catch (\Exception $e) {
            throw new \Exception(__('Api error on tag update: ') . $options['name'] . ' ' . $e->getMessage());
        }
        if (isset($responseBody['error'])) {
            throw new \Exception(__('Api error on tag update: ') . $options['name'] . ' ' . $responseBody['error']['message']);
        }
        return $responseBody;
    }

    /**
     * @param $apiParams
     * @param $trackOption
     * @param $availableDimensions
     * @return array|string
     */
    private function _getProductTrackOptions($apiParams, $trackOption, $availableDimensions = [])
    {
        $listResult = [];

        /** Iterate through the product dimensions */
        foreach ($apiParams['product_dimensions'] as $trackId => $options) {
            if (in_array($trackId, $availableDimensions) && $options['enabled'] && ($options['track_option'] == $trackOption)) {
                $mappingVariableValue = $this->_getVariableMapping($trackId);
                $tagListOption = [
                    'type' => 'map',
                    'map' => [
                        [
                            'type' => 'template',
                            'key' => 'index',
                            'value' => $options['index']
                        ],
                        [
                            'type' => 'template',
                            'key' => $trackOption,
                            'value' => $mappingVariableValue
                        ]
                    ]
                ];
                $listResult[] = $tagListOption;
            }
        }

        /** Iterate through the custom dimensions */
        foreach ($apiParams['custom_dimensions'] as $dimensionId => $options) {
            if (in_array($dimensionId, $availableDimensions) && $options['enabled']) {
                $mappingVariableValue = $this->_getVariableMapping($dimensionId);
                $tagListOption = [
                    'type' => 'map',
                    'map' => [
                        [
                            'type' => 'template',
                            'key' => 'index',
                            'value' => $options['index']
                        ],
                        [
                            'type' => 'template',
                            'key' => 'dimension',
                            'value' => $mappingVariableValue
                        ]
                    ]
                ];
                $listResult[] = $tagListOption;
            }
        }

        if (!count($listResult)) {
            return [];
        }

        return [
            'type' => 'list',
            'key' => $trackOption,
            'list' => $listResult
        ];
    }

    /**
     * @param $trackId
     * @return string
     */
    private function _getVariableMapping($trackId)
    {
        switch ($trackId) {
            case 'track_stockstatus':
                return '{{' . self::VARIABLE_TRACK_STOCK_STATUS . '}}';
                break;
            case 'track_reviewscount':
                return '{{' . self::VARIABLE_TRACK_REVIEW_COUNT . '}}';
                break;
            case 'track_reviewsscore':
                return '{{' . self::VARIABLE_TRACK_REVIEW_SCORE . '}}';
                break;
            case 'track_saleproduct':
                return '{{' . self::VARIABLE_TRACK_SALE_PRODUCT . '}}';
                break;
            case 'custom_dimension_customerid':
                return '{{' . self::VARIABLE_CUSTOMER_ID . '}}';
                break;
            case 'custom_dimension_customergroup':
                return '{{' . self::VARIABLE_CUSTOMER_GROUP . '}}';
                break;
            case 'custom_dimension_pagetype':
                return '{{' . self::VARIABLE_PAGE_TYPE . '}}';
                break;
            default:
                return '';
        }
    }

    /**
     * @return array
     */
    private function _getAvailableDimensions()
    {
        return [
            'track_stockstatus',
            'track_reviewscount',
            'track_reviewsscore',
            'track_saleproduct',
            'custom_dimension_customerid',
            'custom_dimension_customergroup',
            'custom_dimension_pagetype'
        ];
    }

    /**
     * Adding delay between GTM Api calls
     */
    protected function _apiCallLimitation()
    {
        sleep(4);
    }

    /**
     * @param string $uaTrackingId
     * @param array $apiParams
     * @return array
     */
    public function getVariablesList($uaTrackingId, $apiParams)
    {
        return $this->_getVariables($uaTrackingId, $apiParams);
    }

    /**
     * @return array
     */
    public function getTriggersList()
    {
        return $this->_getTriggers();
    }

    /**
     * @param boolean $ipAnonymization
     * @param boolean $displayAdvertising
     * @param array $apiParams
     * @param array $triggersMapping
     * @return array
     */
    public function getTagsList($ipAnonymization, $displayAdvertising, $apiParams, $triggersMapping)
    {
        return $this->_getTags($triggersMapping, $ipAnonymization, $displayAdvertising, $apiParams);
    }
}
