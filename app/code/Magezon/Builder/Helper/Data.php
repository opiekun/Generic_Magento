<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Helper;

use Magento\Framework\HTTP\ClientInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * vendor/magento/framework/View/Template/Html/Minifier.php
     * All inline HTML tags
     *
     * @var array
     */
    protected $inlineHtmlTags = [
        'b',
        'big',
        'i',
        'small',
        'tt',
        'abbr',
        'acronym',
        'cite',
        'code',
        'dfn',
        'em',
        'kbd',
        'strong',
        'samp',
        'var',
        'a',
        'bdo',
        'br',
        'img',
        'map',
        'object',
        'q',
        'span',
        'sub',
        'sup',
        'button',
        'input',
        'label',
        'select',
        'textarea',
        '\?',
    ];

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Framework\Stdlib\ArrayManager
     */
    protected $arrayManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Magezon\Builder\Model\Source\ResizableSizes
     */
    protected $resizableSizes;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magezon\Builder\Model\CacheManager
     */
    protected $cacheManager;

    /**
     * @var array
     */
    protected $_flatElements = [];

    /**
     * @param ClientInterface                                                 $client                    
     * @param \Magento\Framework\App\Helper\Context                           $context                   
     * @param \Magento\Framework\View\Asset\Repository                        $assetRepo                 
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager              
     * @param \Magento\Backend\Model\UrlInterface                             $backendUrl                
     * @param \Magento\Framework\Stdlib\ArrayManager                          $arrayManager              
     * @param \Magento\Framework\App\State                                    $appState                  
     * @param \Magento\Framework\View\LayoutInterface                         $layout                    
     * @param \Magento\Framework\Filter\Template\Tokenizer\ParameterFactory   $parameterFactory          
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory         $pageCollectionFactory     
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory 
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory  
     * @param \Magezon\Core\Helper\Data                                       $coreHelper                
     * @param \Magezon\Builder\Model\Source\ResizableSizes                    $resizableSizes            
     * @param \Magezon\Builder\Model\CacheManager                             $cacheManager              
     */
    public function __construct(
        ClientInterface $client,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Filter\Template\Tokenizer\ParameterFactory $parameterFactory,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magezon\Core\Helper\Data $coreHelper,
        \Magezon\Builder\Model\Source\ResizableSizes $resizableSizes,
        \Magezon\Builder\Model\CacheManager $cacheManager
    ) {
        parent::__construct($context);
        $this->client                    = $client;
        $this->_assetRepo                = $assetRepo;
        $this->_storeManager             = $storeManager;
        $this->_backendUrl               = $backendUrl;
        $this->arrayManager              = $arrayManager;
        $this->appState                  = $appState;
        $this->layout                    = $layout;
        $this->parameterFactory          = $parameterFactory;
        $this->pageCollectionFactory     = $pageCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->urlBuilder                = $context->getUrlBuilder();
        $this->coreHelper                = $coreHelper;
        $this->resizableSizes            = $resizableSizes;
        $this->cacheManager              = $cacheManager;
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
            'mgzbuilder/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl()
    { 
        try {
            $fileId = '/';
            $params = array_merge(['_secure' => $this->_request->isSecure()], []);
            return $this->_assetRepo->getUrlWithParams($fileId, $params) . '/';
        } catch (\Magento\Framework\Exception\LocalizedException $e) {}
    }

    /**
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
    }

    /**
     * @param  string $string
     * @return string         
     */
    public function getImageUrl($string)
    {
        if ($string && is_string($string) && strpos($string, 'http') === false && (strpos($string, '<div') === false)) {
            $mediaUrl = $this->coreHelper->getMediaUrl();
            $string   = $mediaUrl . $string;
        }
        return $string;
    }

    /**
     * @param  string $haystack 
     * @param  string $needle   
     * @return boolean           
     */
    public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param  string $haystack 
     * @param  string $needle   
     * @return boolean           
     */
    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
        (substr($haystack, -$length) === $needle);
    }

    /**
     * @param  string $path   
     * @param  array $params 
     * @return string         
     */
    public function getUrl($path = null, $params = null)
    {
        return $this->_backendUrl->getUrl($path, $params);
    }

    /**
     * @param  string $value 
     * @return string        
     */
    public function getStyleColor($value, $isImportant = false)
    {
        if ($value && (!$this->startsWith($value, '#') && !$this->startsWith($value, 'rgb'))) {
            if ($value != 'transparent') {
                $value = '#' . $value;
            }
        }
        if ($value && $isImportant) {
            $value .= ' !important';
        }
        return $value;
    }

    /**
     * @param  string $value
     * @return string       
     */
    public function getStyleProperty($value, $isImportant = false, $unit = '')
    {
        if (is_numeric($value)) {
            if ($unit) {
                $value .= $unit;
            } else {
                $value .= 'px';
            }
        }
        if ($value == '-') $value = '';
        if ($value && $isImportant) {
            $value .= ' !important';
        }
        return $value;
    }

    /**
     * @param  string|array $target 
     * @param  array $styles 
     * @param  string $suffix 
     * @return string         
     */
    public function getStyles($target, $styles, $suffix = '')
    {
        $html = '';
        if (is_array($target)) {
            foreach ($target as $k => $_selector) {
                if (!$_selector) {
                    unset($target[$k]);
                }
            }
            $i = 0;
            $count = count($target);
            foreach ($target as $_selector) {
                $html .= $_selector . $suffix;
                if ($i!=$count-1)  {
                    $html .= ',';
                }
                $i++;
            }
        } else {
            $html = $target . $suffix;
        }
        $stylesHtml = $this->parseStyles($styles);
        if (!$stylesHtml) return;
        if ($styles) {
            $html .= '{';
            $html .= $stylesHtml;
            $html .= '}';
        }
        return $html;
    }

    /**
     * @param  array $styles 
     * @return string       
     */
    public function parseStyles($styles)
    {
        $result = '';
        foreach ($styles as $k => $v) {
            if ($v=='') continue;
            $result .= $k . ':' . $v . ';';
        }
        return $result;
    }

    /**
     * @param  array $classes 
     * @return string       
     */
    public function parseClasses($_classes)
    {
        $classes = [];
        foreach ($_classes as $k => $v) {
            if ($v=='') continue;
            $classes[] = $v;
        }
        return implode(' ', $classes);
    }

    /**
     * @param  array $attrs 
     * @return string       
     */
    public function parseAttrs($attrs)
    {
        $result = '';
        foreach ($attrs as $k => $v) {
            if ($v=='') continue;
            $result .= $k . '="' . $v . '" ';
        }
        return substr($result, 0, -1);
    }

    /**
     * @return \Magento\Framework\Stdlib\ArrayManager
     */
    public function getArrayManager()
    {
        return $this->arrayManager;
    }

    /**
     * @return array
     */
    public function getResponsiveColumn()
    {
        return [
            [
                'label' => 6,
                'value' => 2
            ],
            [
                'label' => 5,
                'value' => 15
            ],
            [
                'label' => 4,
                'value' => 3
            ],
            [
                'label' => 3,
                'value' => 4
            ],
            [
                'label' => 2,
                'value' => 6
            ],
            [
                'label' => 1,
                'value' => 12
            ]
        ];
    }

    /**
     * Check is admin area
     *
     * @return bool
     */
    public function isAdminStore()
    {
        return ($this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML);
    }

    /**
     * @return array
     */
    public function getResizableSizes()
    {
        return $this->resizableSizes->getOptions();
    }

    /**
     * @return string
     */
    public function getGoogleMapApi()
    {
        return $this->getConfig('general/google_api_key');
    }

    public function findElement($_elements, $elemName, $convertJobject = false) {
        foreach ($_elements as $_element) {
            if (isset($_element['id']) && $_element['id'] == $elemName) {
                if ($convertJobject) {
                    return (new \Magento\Framework\DataObject($_element));
                }
                return $_element;
            }
            if (isset($_element['elements'])) {
                $result = $this->findElement($_element['elements'], $elemName, $convertJobject);
                if ($result) {
                    return $result;
                }
            }
        }
    }

    public function prepareProfileBlock($block, $profile)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        if (is_string($profile)) {
            $search = $replace = $proIds = $pageIds = $catIds = [];
            preg_match_all('/(.*?){{mgzlink(.*?)}}/si', $profile, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $_str = stripslashes($match[2]);
                $tokenizer = $this->parameterFactory->create();
                $tokenizer->setString($_str);
                $params = $tokenizer->tokenize();

                if ($params && isset($params['type']) && isset($params['id'])) {
                    switch ($params['type']) {
                        case 'category':
                            $catIds[] = $params['id'];
                            break;

                        case 'product':
                            $proIds[] = $params['id'];
                            break;

                        case 'page':
                            $pageIds[] = $params['id'];
                            break;
                    }
                    if ($params['type'] == 'custom') {
                        $params['url'] = $this->coreHelper->filter(stripcslashes($params['url']));
                    } else {
                        $params['url'] = 'mgzlink_' . $params['type'] . '_' . $params['id'];
                    }
                    $search[]  = '"{{mgzlink' . $match[2] . '}}"';
                    $replace[] = $this->coreHelper->serialize($params);
                }
            }
            $profile = str_replace($search, $replace, $profile);
            $search  = $replace = [];
            if (!empty($catIds)) {
                $categoryCollection = $this->categoryCollectionFactory->create();
                $categoryCollection->addFieldToFilter('entity_id', ['in' => $catIds]);
                $categoryCollection->setStoreId($storeId);
                $categoryCollection->addUrlRewriteToResult();
                foreach ($categoryCollection as $category) {
                    $search[]  = 'mgzlink_category_' . $category->getId();
                    $replace[] = $category->getUrl();
                }
            }
            if (!empty($proIds)) {
                $productCollection = $this->productCollectionFactory->create();
                $productCollection->addFieldToFilter('entity_id', ['in' => $proIds]);
                $productCollection->setStoreId($storeId);
                $productCollection->addUrlRewrite();
                foreach ($productCollection as $product) {
                    $search[]  = 'mgzlink_product_' . $product->getId();
                    $replace[] = $product->getProductUrl();
                }
            }
            if (!empty($pageIds)) {
                $pageCollection = $this->pageCollectionFactory->create();
                $pageCollection->addFieldToFilter('page_id', ['in' => $pageIds]);
                foreach ($pageCollection as $page) {
                    $search[]  = 'mgzlink_page_' . $page->getId();
                    $replace[] = $this->_urlBuilder->getUrl(null, ['_direct' => $page->getIdentifier()]);
                }
            }

            //18
            //181
            foreach ($search as &$_key) {
                $_key = '"' . $_key . '"';
            }
            foreach ($replace as &$_key) {
                $_key = '"' . $_key . '"';
            }

            $profile = str_replace($search, $replace, $profile);
        }
        $profile = $this->coreHelper->unserialize($profile);
        if (is_array($profile)) {
            $elements = isset($profile['elements']) ? $profile['elements'] : [];
            $profile['elements'] = $this->prepareElements($elements);
        } else {
            $profile = [];
            $profile['elements'] = [];
        }
        $block = $this->layout->createBlock($block, '', [
            'data' => $profile
        ]);
        return $block;
    }

    /**
     * @param  array $elements 
     * @return array           
     */
    public function prepareProfile($profile, $suffixId = '')
    {
        $profile = $this->coreHelper->unserialize($profile);
        if (is_array($profile)) {
            $elements = isset($profile['elements']) ? $profile['elements'] : [];
            $profile['elements'] = $this->prepareElements($elements, $suffixId);
        } else {
            $profile = [];
            $profile['elements'] = [];
        }
        return $profile;
    }

    private function prepareElements($elements, $suffixId = '') {
        foreach ($elements as &$element) {
            $element['id'] .= $suffixId;
            $this->_flatElements[] = (new \Magento\Framework\DataObject($element));
            foreach ($element as $key => &$value) {
                if (!is_array($value)) {
                    $value = str_replace('&quot;', '"', $value);
                }
            }
            if (isset($element['elements'])) {
                $element['elements'] = $this->prepareElements($element['elements'], $suffixId);
            }
        }
        return $elements;
    }

    public function getFlatElements($profile, $suffixId = '')
    {
        $this->_flatElements = [];
        $profile = $this->prepareProfile($profile, $suffixId);
        return $this->_flatElements;
    }

    /**
     * vendor/magento/framework/View/Template/Html/Minifier.php
     * @param string $content
     * @return void
     */
    public function minify($content)
    {
        $result =  preg_replace(
            '#((?:<\?php\s+(?!echo|print|if|elseif|else)[^\?]*)\?>)\s+#',
            '$1 ',
            preg_replace(
                '#(?<!' . implode('|', $this->inlineHtmlTags) . ')\> \<#',
                '><',
                preg_replace(
                    '#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre|script)\b))*+)'
                    . '(?:<(?>textarea|pre|script)\b|\z))#',
                    ' ',
                    preg_replace(
                        '#(?<!:|\\\\|\'|")//(?!\s*\<\!\[)(?!\s*]]\>)[^\n\r]*#',
                        '',
                        preg_replace(
                            '#(?<!:|\'|")//[^\n\r]*(\?\>)#',
                            ' $1',
                            preg_replace(
                                '#(?<!:)//[^\n\r]*(\<\?php)[^\n\r]*(\s\?\>)[^\n\r]*#',
                                '',
                                $content
                            )
                        )
                    )
                )
            )
        );
        return rtrim($result);
    }

    /**
     * @return array
     */
    public function getListSocial()
    {
        return [
            [
                'label' => __('Facebook'),
                'value' => 'fab mgz-fa-facebook-f'
            ],
            [
                'label' => __('Twitter'),
                'value' => 'fab mgz-fa-twitter'
            ],
            [
                'label' => __('Pinterest'),
                'value' => 'fab mgz-fa-pinterest-p'
            ],
            [
                'label' => __('LinkedIn'),
                'value' => 'fab mgz-fa-linkedin-in'
            ],
            [
                'label' => __('Tumblr'),
                'value' => 'fab mgz-fa-tumblr'
            ],
            [
                'label' => __('Instagram'),
                'value' => 'fab mgz-fa-instagram'
            ],
            [
                'label' => __('Skype'),
                'value' => 'fab mgz-fa-skype'
            ],
            [
                'label' => __('Flickr'),
                'value' => 'fab mgz-fa-flickr'
            ],
            [
                'label' => __('Dribbble'),
                'value' => 'fab mgz-fa-dribbble'
            ],
            [
                'label' => __('Youtube'),
                'value' => 'fab mgz-fa-youtube'
            ],
            [
                'label' => __('Vimeo'),
                'value' => 'fab mgz-fa-vimeo-v'
            ],
            [
                'label' => __('RSS'),
                'value' => 'fas mgz-fa-rss'
            ],
            [
                'label' => __('Behance'),
                'value' => 'fab mgz-fa-behance'
            ]
        ];
    }

    /**
     * $attributes = [
            'row_width' => [
                'type'     => 'unit',
                'property' => 'width',
                'value'    => '100%' //Default value
            ]
        ];
     * @param  string                         $target     
     * @param  string                         $type       
     * @param  \Magezon\Builder\Model\Element $element    
     * @param  array                          $attributes 
     * @param  string                         $prefix 
     * @return string                                     
     */
    public function getStylesHtml(string $target, string $type, \Magezon\Builder\Model\Element $element, array $attributes, $prefix = '')
    {
        $result = '';

        switch ($type) {
            case 'custom':
                $prefixes = ['', 'lg_', 'md_', 'sm_', 'xs_'];
                break;
            
            default:
                $prefixes = [''];
                break;
        }

        foreach ($prefixes as $_k => $_prefix) {
            $styles = [];
            foreach ($attributes as $_attr => $options) {
                if (!isset($options['property'])) continue;
                $_property    = $options['property'];
                $_suffixField = (isset($options['suffix_field'])) ? $options['suffix_field'] : '';
                $_type        = (isset($options['type'])) ? $options['type'] : '';
                $_important   = (isset($options['important'])) ? $options['important'] : false;
                $_value       = (isset($options['value'])) ? $options['value'] : $element->getData($prefix . $_prefix . $_attr);
                $_unit        = (isset($options['unit'])) ? $options['unit'] : '';

                switch ($_type) {
                    case 'color':
                        $styles[$_property] = $this->getStyleColor($_value, $_important, $_unit);
                        break;

                    case 'unit':
                        $styles[$_property] = $this->getStyleProperty($_value, $_important, $_unit);
                        break;

                    default:
                        $styles[$_property] = $_value;
                        if ($_important) $styles[$_property] .= ' !important';
                        break;
                }

                // background-size: 200% 200px;
                if ($styles[$_property] && $_suffixField) {
                    $styles[$_property] .= ' ' . $this->getStyleProperty($element->getData($prefix . $_prefix . $_suffixField));
                }

            }

            if ($_styleHtml = $this->parseStyles($styles)) {
                if ($type == 'custom') {
                    switch ($_prefix) {
                        case 'xs_':
                            $result .= '@media (max-width: 575px) {';
                            break;

                        case 'sm_':
                            $result .= '@media (max-width: 767px) {';
                            break;

                        case 'md_':
                            $result .= '@media (max-width: 991px) {';
                            break;

                        case 'lg_':
                            $result .= '@media (max-width: 1199px) {';
                            break;

                        // Defualt xl
                        default:
                            //$result .= '@media (min-width: 1200px) {';
                            break;
                    }
                }
                $result .= $target . '{';
                $result .= $_styleHtml;
                $result .= '}';
                if ($type == 'custom' && $_prefix) {
                    $result .= '}';
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getTemplates($url)
    {
        $templates = [];
        try {
            $key = $url;
            $templates = $this->cacheManager->getFromCache($key);
            if ($templates) {
                return $this->coreHelper->unserialize($templates);
            }
            $this->client->get($url);
            $content = $this->client->getBody();
            if ($content) {
                $templates = $this->coreHelper->unserialize($content);
                $newTemplates = [];
                foreach ($templates as $template) {
                    try {
                        $newTemplates[] = $template;
                    } catch (\Exception $e) {
                    }
                }
                $templates = $newTemplates;
            }
            $this->cacheManager->saveToCache($key, $templates);
        } catch (\Exception $e) {}
        return $templates;
    }
}