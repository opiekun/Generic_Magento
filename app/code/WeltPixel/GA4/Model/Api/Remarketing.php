<?php
namespace WeltPixel\GA4\Model\Api;

/**
 * Class \WeltPixel\GA4\Model\Api\Remarketing
 */
class Remarketing extends \WeltPixel\GA4\Model\Api
{
    /**
     * Variable names
     */
    const VARIABLE_REMARKETING_GOOGLE_TAG = 'WP - Google Tag Params';

    /**
     * Tag names
     */
    const TAG_REMARKETING_ADWORDS_REMARKETING = 'WP - AdWords Remarketing';

    /**
     * Field names used in sending data to dataLayer
     */
    const FIELD_REMARKEING_GOOGLE_TAG = 'google_tag_params';


    const ECOMM_PAGETYPE_HOME = 'home';
    const ECOMM_PAGETYPE_CATEGORY = 'category';
    const ECOMM_PAGETYPE_SEARCHRESULTS = 'searchresults';
    const ECOMM_PAGETYPE_PRODUCT = 'product';
    const ECOMM_PAGETYPE_CART = 'cart';
    const ECOMM_PAGETYPE_PURCHASE = 'purchase';
    const ECOMM_PAGETYPE_CHECKOUT = 'checkout';
    const ECOMM_PAGETYPE_OTHER = 'other';

    /**
     * Return list of variables for remarketing
     * @return array
     */
    private function _getRemarketingVariables()
    {
        $variables = array
        (
            self::VARIABLE_REMARKETING_GOOGLE_TAG => array
            (
                'name' => self::VARIABLE_REMARKETING_GOOGLE_TAG,
                'type' => self::TYPE_VARIABLE_DATALAYER,
                'parameter' => array
                (
                    array
                    (
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ),
                    array
                    (
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ),
                    array
                    (
                        'type' => 'template',
                        'key' => 'name',
                        'value' => self::FIELD_REMARKEING_GOOGLE_TAG
                    )
                )
            )
        );

        return $variables;
    }

    /**
     * Return a list of tags for remarketing
     * @param array $triggers
     * @param array $params
     * @return array
     */
    private function _getRemarketingTags($triggers, $params)
    {
        $tags = array
        (
            self::TAG_REMARKETING_ADWORDS_REMARKETING => array
            (
                'name' => self::TAG_REMARKETING_ADWORDS_REMARKETING,
                'firingTriggerId' => array
                (
                    self::TRIGGER_ALL_PAGES_ID
                ),
                'type' => self::TYPE_TAG_SP,
                'tagFiringOption' => 'oncePerEvent',
                'parameter' => array
                (
                    array
                    (
                        'type' => 'template',
                        'key' => 'dataLayerVariable',
                        'value' => '{{' . self::VARIABLE_REMARKETING_GOOGLE_TAG . '}}'
                    ),
                    array
                    (
                        'type' => 'template',
                        'key' => 'customParamsFormat',
                        'value' => 'DATA_LAYER'
                    ),
                    array
                    (
                        'type' => 'template',
                        'key' => 'conversionId',
                        'value' => $params['conversion_code']
                    ),
                    array
                    (
                        'type' => 'template',
                        'key' => 'conversionLabel',
                        'value' => $params['conversion_label']
                    )
                )
            )
        );

        return $tags;
    }

    /**
     * @return array
     */
    public function getRemarketingVariablesList()
    {
        return $this->_getRemarketingVariables();
    }

    /**
     * @param array $triggers
     * @param array $params
     * @return array
     */
    public function getRemarketingTagsList($triggers, $params)
    {
        return $this->_getRemarketingTags($triggers, $params);
    }
}
