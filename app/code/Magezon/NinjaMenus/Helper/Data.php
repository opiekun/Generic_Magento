<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context       
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $_storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $_storeManager;
    }
   
    /**
     * @param  string $key
     * @param  null|int $store
     * @return null|string
     */
    public function getConfig($key, $store = null)
    {
        $store     = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();
        $result    = $this->scopeConfig->getValue(
            'ninjamenus/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getConfig('general/enabled');
    }

    public function filter($content)
    {
        $content = $this->filterImages($content);
        $content = $this->filterIframes($content);
        return $content;
    }

    /**
     * Filter images with placeholders in the content
     * 
     * @param  string $content
     * @return string
     */
    public function filterImages($content)
    {
        $matches = $search = $replace = [];
        preg_match_all( '/<img[\s\r\n]+.*?>/is', $content, $matches );
        $placeHolderUrl = $this->getPlaceHolderUrl();

        $lazyClasses = $this->getLazyClasses();

        foreach ($matches[0] as $imgHTML) {
            if ( ! preg_match( "/src=['\"]data:image/is", $imgHTML ) && strpos($imgHTML, 'data-src')===false && ! $this->isSkipElement($imgHTML) ) {

                // replace the src and add the data-src attribute
                $replaceHTML = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . $placeHolderUrl . '" data-src=', $imgHTML );

                // add the lazy class to the img element
                if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
                    $replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1' . $lazyClasses . ' $2$1', $replaceHTML );
                } else {
                    $replaceHTML = preg_replace( '/<img/is', '<img class="' . $lazyClasses . '"', $replaceHTML );
                }

                $search[]  = $imgHTML;
                $replace[] = $replaceHTML;
            }
        }

        $content = str_replace( $search, $replace, $content );

        return $content;
    }

    /**
     * Filter images with placeholders in the content
     * 
     * @param  string $content
     * @return string
     */
    public function filterIframes($content)
    {
        $matches = $search = $replace = [];
        preg_match_all( '|<iframe\s+.*?</iframe>|si', $content, $matches );
        $placeHolderUrl = $this->getPlaceHolderUrl();
        $lazyClasses = $this->getLazyClasses();

        foreach ($matches[0] as $imgHTML) {
            if ( ! preg_match( "/src=['\"]data:image/is", $imgHTML ) && strpos($imgHTML, 'data-src')===false && ! $this->isSkipElement($imgHTML) ) {

                // replace the src and add the data-src attribute
                $replaceHTML = preg_replace( '/<iframe(.*?)src=/is', '<iframe$1src="' . $placeHolderUrl . '" data-src=', $imgHTML );

                // add the lazy class to the iframe element
                if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
                    $replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1' . $lazyClasses . ' $2$1', $replaceHTML );
                } else {
                    $replaceHTML = preg_replace( '/<iframe/is', '<iframe class="' . $lazyClasses . '"', $replaceHTML );
                }

                $search[]  = $imgHTML;
                $replace[] = $replaceHTML;
            }
        }

        $content = str_replace( $search, $replace, $content );

        return $content;
    }

    /**
     * @return string
     */
    public function getLazyClasses()
    {
        return 'ninjamenus-lazy ninjamenus-lazy-blur';
    }

    /**
     * @return string
     */
    public function getPlaceHolderUrl()
    {
        return '';
    }

    /**
     * Check is skip element via specific classes
     * @param  string  $content
     * @return boolean
     */
    protected function isSkipElement($content)
    {
        return false;
    }
}
