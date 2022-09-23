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
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Booking\Helper;


class Feed extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_allowedFeedType = [];
    protected $_backendConfig;
    protected $parser;
    private $driver;
    protected $_storeManager;
    protected $_scopeConfigManager;
    protected $_configValueManager;
    protected $_transaction;
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Feed constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\ConfigInterface $backendConfig
     * @param \Magento\Framework\Xml\Parser $parser
     * @param \Magento\Framework\Filesystem\Driver\File $driver
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\ValueInterface $valueInterface
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     * @param \Magento\Config\Model\Config\Factory $configfactory
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context ,
                                \Magento\Framework\Registry $coreRegistry,
                                \Magento\Backend\App\ConfigInterface $backendConfig,
                                \Magento\Framework\Xml\Parser $parser,
                                \Magento\Framework\Filesystem\Driver\File $driver,
                                \Magento\Framework\App\ProductMetadataInterface $productMetadata,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Framework\App\Config\ValueInterface $valueInterface,
                                \Magento\Framework\DB\Transaction $transaction,
                                \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
                                \Magento\Config\Model\Config\Factory $configfactory
    ) {
        parent::__construct($context);
        $this->_backendConfig = $backendConfig;
        $this->parser = $parser;
        $this->driver = $driver;
        $this->urlBuilder = $this->_urlBuilder;
        $this->productMetadata   = $productMetadata;
        $this->_allowedFeedType =  explode(',', $backendConfig->getValue(\Ced\Booking\Model\Feed::XML_FEED_TYPES));
        $this->_storeManager = $storeManager;
        $this->_scopeConfigManager = $scopeConfig;
        $this->_configValueManager = $valueInterface;
        $this->_transaction = $transaction;
        $this->_componentRegistrar = $componentRegistrar;
        $this->_configfactory = $configfactory;
    }


    /**
     * Retrieve admin interest in current feed type
     * @param \SimpleXMLElement $item
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAllowedFeedType(\SimpleXMLElement $item)
    {
        $isAllowed = false;
        if (is_array($this->_allowedFeedType) && count($this->_allowedFeedType) >0) {
            $cedModules = $this->getCedCommerceExtensions();
            switch(trim((string)$item->update_type)) {
                case \Ced\Booking\Model\Source\Updates\Type::TYPE_NEW_RELEASE :
                case \Ced\Booking\Model\Source\Updates\Type::TYPE_INSTALLED_UPDATE :
                    if (in_array(\Ced\Booking\Model\Source\Updates\Type::TYPE_INSTALLED_UPDATE, $this->_allowedFeedType) && strlen(trim($item->module)) > 0 && isset($cedModules[trim($item->module)]) && version_compare($cedModules[trim($item->module)], trim($item->release_version), '<')===true) {
                        $isAllowed = true;
                        break;
                    }
                case \Ced\Booking\Model\Source\Updates\Type::TYPE_UPDATE_RELEASE :
                    if (in_array(\Ced\Booking\Model\Source\Updates\Type::TYPE_UPDATE_RELEASE, $this->_allowedFeedType) && strlen(trim($item->module)) > 0) {
                        $isAllowed = true;
                        break;
                    }
                    if (in_array(\Ced\Booking\Model\Source\Updates\Type::TYPE_NEW_RELEASE, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
                case \Ced\Booking\Model\Source\Updates\Type::TYPE_PROMO :
                    if (in_array(\Ced\Booking\Model\Source\Updates\Type::TYPE_PROMO, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
                case \Ced\Booking\Model\Source\Updates\Type::TYPE_INFO :
                    if (in_array(\Ced\Booking\Model\Source\Updates\Type::TYPE_INFO, $this->_allowedFeedType)) {
                        $isAllowed = true;
                    }
                    break;
            }
        }
        return $isAllowed;
    }

    /**
     * Retrieve all the extensions name and version developed by CedCommerce
     * @param bool $asString
     * @return array|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCedCommerceExtensions($asString = false)
    {
        if ($asString) {
            $cedCommerceModules = '';
        } else {
            $cedCommerceModules = [];
        }

        foreach ($this->getAllModules() as $name=>$module) {
            if (preg_match('/ced_/i', $name) && isset($module['release_version'])) {
                if ($asString) {
                    $cedCommerceModules .= $name.'-'.trim($module['release_version']).'~';
                } else {
                    $cedCommerceModules[$name] = trim($module['release_version']);
                }
            }
        }
        if ($asString) {
            trim($cedCommerceModules, '~');
        }
        return $cedCommerceModules;
    }

    /**
     * Returns module config data and a path to the module.xml file.
     * @return \Generator
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getModuleConfigs()
    {
        $modulePaths = $this->_componentRegistrar
            ->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE);
        foreach ($modulePaths as $modulePath) {
            $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, "$modulePath/etc/module.xml");
            yield [$filePath, $this->driver->fileGetContents($filePath)];
        }
    }

    public function getAllModules($exclude = [])
    {
        $result = [];
        foreach ($this->getModuleConfigs() as list($file, $contents)) {
            try {
                $this->parser->loadXML($contents);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    new \Magento\Framework\Phrase(
                        'Invalid Document: %1%2 Error: %3',
                        [$file, PHP_EOL, $e->getMessage()]
                    ),
                    $e
                );
            }

            $data = $this->convert($this->parser->getDom());
            if (count($data) ){
                $name = key($data);
                if (!in_array($name, $exclude)) {
                    if(isset($data[$name]) && isset($data[$name]['release_version'])) {
                        $result[$name] = $data[$name];
                    }
                }
            }
        }
        return $result;
    }
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function convert($source)
    {
        $modules = [];
        $xpath = new \DOMXPath($source);
        /**
         * @var $moduleNode \DOMNode
         */
        foreach ($xpath->query('/config/module') as $moduleNode) {
            $moduleData = [];
            $moduleAttributes = $moduleNode->attributes;
            $nameNode = $moduleAttributes->getNamedItem('name');
            if (strpos($nameNode->nodeValue, 'Ced') === false) {
                continue;
            }
            if ($nameNode === null) {
                throw new \Exception('Attribute "name" is required for module node.');
            }
            $moduleData['name'] = 'Magento2_'.$nameNode->nodeValue;
            $name = $moduleData['name'];
            $versionNode = $moduleAttributes->getNamedItem('setup_version');
            if ($versionNode === null) {
                throw new \Exception("Attribute 'setup_version' is missing for module '{$name}'.");
            }
            $moduleData['setup_version'] = $versionNode->nodeValue;
            if ($moduleAttributes->getNamedItem('release_version')) {
                $moduleData['release_version'] = $moduleAttributes->getNamedItem('release_version')->nodeValue;
            }
            if ($moduleAttributes->getNamedItem('parent_product_name')) {
                $moduleData['parent_product_name'] = $moduleAttributes->getNamedItem('parent_product_name')->nodeValue;
            }
            $moduleData['sequence'] = [];
            /**
             * @var $csChildNode \DOMNode
             */
            foreach ($moduleNode->childNodes as $csChildNode) {
                switch ($csChildNode->nodeName) {
                    case 'sequence':
                        $moduleData['sequence'] = $this->_readModules($csChildNode);
                        break;
                }
            }
            // Use module name as a key in the result array to allow quick access to module configuration
            $modules['Magento2_'.$nameNode->nodeValue] = $moduleData;

        }
        return $modules;
    }
    /**
     * Convert module depends node into assoc array
     *
     * @param  \DOMNode $node
     * @return array
     * @throws \Exception
     */
    protected function _readModules(\DOMNode $node)
    {
        $result = [];
        /**
         * @var $csChildNode \DOMNode
         */
        foreach ($node->childNodes as $csChildNode) {
            switch ($csChildNode->nodeName) {
                case 'module':
                    $nameNode = $csChildNode->attributes->getNamedItem('name');
                    if ($nameNode === null) {
                        throw new \Exception('Attribute "name" is required for module node.');
                    }
                    $result[] = $nameNode->nodeValue;
                    break;
            }
        }
        return $result;
    }


    /**
     * Retrieve environment information of magento
     * And installed extensions provided by CedCommerce
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEnvironmentInformation()
    {
        $info = [];
        $info['plateform'] = 'Magento2.x';
        $info['domain_name'] = $this->urlBuilder->getBaseUrl();
        $info['magento_edition'] = 'default';
        $info['magento_edition'] =$this->productMetadata->getEdition();
        $info['magento_version'] = $this->productMetadata->getVersion();
        $info['php_version'] = phpversion();
        $info['feed_types'] = $this->_backendConfig->getValue(\Ced\Booking\Model\Feed::XML_FEED_TYPES);
        $info['country_id'] = $this->_backendConfig->getValue('general/store_information/merchant_country');
        if ($info['country_id'] == '') {
            $info['country_id'] = $this->_backendConfig->getValue('general/country/default');
        }
        $info['admin_name'] = $this->_backendConfig->getValue('trans_email/ident_general/name');
        if (strlen($info['admin_name']) == 0) {
            $info['admin_name'] = $this->_backendConfig->getValue('trans_email/ident_sales/name');
        }
        $info['admin_email'] = $this->_backendConfig->getValue('trans_email/ident_general/email');
        if (strlen($info['admin_email']) == 0) {
            $info['admin_email'] = $this->_backendConfig->getValue('trans_email/ident_sales/email');
        }
        $info['installed_extensions_by_cedcommerce'] = $this->getCedCommerceExtensions(true);
        return $info;
    }

    /**
     * Url encode the parameters
     *
     * @param  string | array
     * @return string | array | boolean
     */
    public function prepareParams($data)
    {
        if (!is_array($data) && strlen($data)) {
            return urlencode($data);
        }
        if ($data && is_array($data) && count($data)>0) {
            foreach($data as $key=>$value){
                $data[$key] = urlencode($value);
            }
            return $data;
        }
        return false;
    }

    /**
     * Url decode the parameters
     *
     * @param  string | array
     * @return string | array | boolean
     */
    public function extractParams($data)
    {
        if (!is_array($data) && strlen($data)) {
            return urldecode($data);
        }
        if ($data && is_array($data) && count($data)>0) {
            foreach($data as $key=>$value){
                $data[$key] = urldecode($value);
            }
            return $data;
        }
        return false;
    }

    /**
     * Add params into url string
     *
     * @param  string  $url       (default '')
     * @param  array   $params    (default array())
     * @param  boolean $urlencode (default true)
     * @return string | array
     */
    public function addParams($url = '', $params = [], $urlencode = true)
    {
        if (count($params)>0) {
            foreach ($params as $key=>$value){
                if (parse_url($url, PHP_URL_QUERY)) {
                    if ($urlencode) {
                        $url .= '&'.$key.'='.$this->prepareParams($value);
                    } else {
                        $url .= '&'.$key.'='.$value;
                    }
                } else {
                    if ($urlencode) {
                        $url .= '?'.$key.'='.$this->prepareParams($value);
                    } else {
                        $url .= '?'.$key.'='.$value;
                    }
                }
            }
        }
        return $url;
    }


    /**
     * Function for setting Config value of current store
     * @param $path
     * @param $value
     * @param null $storeId
     * @throws \Exception
     */
    public function setDefaultStoreConfig($path, $value, $storeId=null)
    {
        $store = $this->_storeManager->getStore($storeId);
        $pathDetails = explode('/',$path);
        $configData = [
            'section' => $pathDetails[0] ,
            'website' => null,
            'store'   => null,
            'groups'  => [
                $pathDetails[1] => [
                    'fields' => [
                        $pathDetails[2] => [
                            'value' => $value,
                        ],
                    ],
                ],
            ],
        ];

        /** @var \Magento\Config\Model\Config $configModel */
        $configModel = $this->_configfactory->create(['data' => $configData]);
        $configModel->save();
    }

    /**
     * Function for setting Config value of current store
     *
     * @param string $path,
     * @param string $value,
     */
    public function setStoreConfig($path, $value, $storeId=null)
    {
        $store = $this->_storeManager->getStore($storeId);
        $data = [
            'path' => $path,
            'scope' =>  'stores',
            'scope_id' => $storeId,
            'scope_code' => $store->getCode(),
            'value' => $value,
        ];
        $this->_configValueManager->addData($data);
        $this->_transaction->addObject($this->_configValueManager);
        $this->_transaction->save();
    }


    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getStoreConfig($path,$storeId=0)
    {
        $store = $this->_storeManager->getStore($storeId);
        return $this->_scopeConfigManager->getValue($path, 'store', $store->getCode());
    }

    /**
     * @param $h
     * @param $l
     * @return mixed|string
     */
    public function getLicenseFromHash($h,$l){
        for ($i=1; $i<=(int)$l; $i++) {
            $h = base64_decode($h);
        }
        $h = json_decode($h, true);
        if (is_array($h) && isset($h['license'])) {
            return $h['license'];
        } else {
            return '';
        }
    }
}
