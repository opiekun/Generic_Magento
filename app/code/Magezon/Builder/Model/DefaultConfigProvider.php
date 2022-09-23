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

namespace Magezon\Builder\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;

class DefaultConfigProvider implements ConfigProviderInterface
{
	/**
	 * @var string
	 */
	protected $_builderArea = 'all';

	/**
	 * @var \Magezon\Builder\Model\WysiwygConfigProvider
	 */
	protected $wysiwygConfig;

	/**
	 * @var \Magezon\Builder\Model\CacheManager
	 */
	protected $builderCacheManager;

	/**
	 * @var \Magezon\Builder\Data\Groups
	 */
	protected $builderGroups;

    /**
     * @var \Magezon\Builder\Data\Elements
     */
    protected $builderElements;

	/**
	 * @var \Magezon\Builder\Helper\Data
	 */
	protected $builderHelper;

	/**
	 * @param \Magezon\Builder\Model\WysiwygConfigProvider $wysiwygConfig       
	 * @param \Magezon\Builder\Model\CacheManager          $builderCacheManager 
	 * @param \Magezon\Builder\Data\Groups                 $builderGroups       
	 * @param \Magezon\Builder\Data\Elements               $builderElements     
	 * @param \Magezon\Builder\Helper\Data                 $builderHelper       
	 */
	public function __construct(
        \Magezon\Builder\Model\WysiwygConfigProvider $wysiwygConfig,
        \Magezon\Builder\Model\CacheManager $builderCacheManager,
        \Magezon\Builder\Data\Groups $builderGroups,
		\Magezon\Builder\Data\Elements $builderElements,
        \Magezon\Builder\Helper\Data $builderHelper
	) {
		$this->wysiwygConfig       = $wysiwygConfig;
		$this->builderCacheManager = $builderCacheManager;
		$this->builderElements     = $builderElements;
		$this->builderGroups       = $builderGroups;
		$this->builderHelper       = $builderHelper;
	}

	/**
	 * @return string
	 */
	public function getBaseUrl()
	{
		$backendHelper     = ObjectManager::getInstance()->get(\Magento\Backend\Helper\Data::class);
		$frontNameResolver = ObjectManager::getInstance()->get(\Magento\Backend\App\Area\FrontNameResolver::class);
		$adminHompePageUrl = $backendHelper->getHomePageUrl();
		$frontName         = $frontNameResolver->getFrontName();
		$urls              = explode('/' . $frontName . '/', $adminHompePageUrl);
		$baseUrl           = $urls[0] . '/' . $frontName . '/';
		return $baseUrl;
	}

    /**
     * Return configuration array
     *
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
		$config                            = [];
		$config['baseUrl']                 = $this->getBaseUrl();
		$config['viewFileUrl']             = $this->builderHelper->getViewFileUrl();
		$config['mediaUrl']                = $this->builderHelper->getMediaUrl();
		$config['resizableSizes']['class'] = '\Magezon\Builder\Model\Source\ResizableSizes';
		$config['animationIn']['class']    = '\Magezon\Builder\Model\Source\AnimateIn';
		$config['animationOut']['class']   = '\Magezon\Builder\Model\Source\AnimateOut';
		$config['groups']                  = $this->builderGroups->getGroups();
		$config['loadStylesUrl']           = 'mgzbuilder/ajax/loadStyles';
		$config['loadElementUrl']          = 'mgzbuilder/ajax/loadElement';
		$config['elements']                = $this->getElementList();
		$config['mainElement']             = 'row';
		$config['googleApi']               = $this->builderHelper->getGoogleMapApi();
		$config['shortcode']['class']      = '\Magezon\Builder\Data\Modal\Shortcode';
		$config['wysiwyg']                 = $this->getWysiwygConfig();
		$config['fileManagerUrl']          = $this->builderHelper->getUrl('mgzcore/wysiwyg_images/index', [
            'target_element_id' => 'UID'
        ]);
		$config['area']         = $this->getBuilderArea();
		$config['frontend_url'] = $this->getFrontendUrl();
		$config['pageLayouts']  = $this->getPageLayouts();

        $config['historyLabels'] = [
			'added'             => __('Added'),
			'inserted_before'   => __('Inserted Before'),
			'inserted_after'    => __('Inserted After'),
			'removed'           => __('Removed'),
			'edited'            => __('Edited'),
			'moved'             => __('Moved'),
			'replaced'          => __('Replaced'),
			'duplicated'        => __('Duplicated'),
			'changed'           => __('Changed'),
			'pasted'            => __('Pasted'),
			'editing_started'   => __('Editing Started'),
			'imported_template' => __('Imported Template'),
			'move_up'           => __('Move Up'),
			'move_down'         => __('Move Down'),
			'cleared_layout'    => __('Cleared Layout')
        ];

		$scopeConfig = ObjectManager::getInstance()->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);

		$resourceUrlWhitelist = [];
		if ($scopeConfig->getValue(Store::XML_PATH_SECURE_BASE_STATIC_URL)) $resourceUrlWhitelist[] = $scopeConfig->getValue(Store::XML_PATH_SECURE_BASE_STATIC_URL) . '**';
		if ($scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_STATIC_URL)) $resourceUrlWhitelist[] = $scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_STATIC_URL) . '**';
		if ($scopeConfig->getValue(Store::XML_PATH_SECURE_BASE_MEDIA_URL)) $resourceUrlWhitelist[] = $scopeConfig->getValue(Store::XML_PATH_SECURE_BASE_MEDIA_URL) . '**';
		if ($scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_MEDIA_URL)) $resourceUrlWhitelist[] = $scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_MEDIA_URL) . '**';
		$config['resourceUrlWhitelist'] = $resourceUrlWhitelist;
		$config['timezone'] = '\Magezon\Builder\Model\Source\Timezone';
		return $config;
    }

    public function getFrontendUrl() {
    	$urlBuilder = ObjectManager::getInstance()->get(\Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder::class);
    	return $urlBuilder->getUrl('', null, null);
    }

    public function getWysiwygConfig() {
		$config = $this->wysiwygConfig->getConfig();
		$config['widget_types'] = $this->getAvailableWidgets(new \Magento\Framework\DataObject());
		$config['error_image_url'] = $this->getErrorImageUrl();
		$config['placeholders'] = $config['widget_placeholders'] = $this->getWidgetPlaceholderImageUrls();
		return $config;
    }

    public function getCacheKey()
    {
    	return 'MAGEZON_BUILDER_CONFIG' . $this->getBuilderArea();
    }

	/**
	 * @param  array $elements 
	 * @return array           
	 */
	public function getElementList()
	{
		$key = $this->getCacheKey();
		if ($cacheData = $this->builderCacheManager->getFromCache($key)) {
			$list = $cacheData;
        } else {
			$builderArea = $this->getBuilderArea();
			$list        = [];
			$elements    = $this->builderElements->getElements();
			if ($elements) {
				foreach ($elements as $element) {
					$config = $element->getConfig();

					if (!isset($config['modalVisible'])) $config['modalVisible'] = true;

					if (isset($_element['children'])) {
						$_element['is_collection'] = true;
					}

					if (isset($config['area'])) {
						if (is_string($config['area'])) {
							$area             = $config['area'];
							$config['area']   = [];
							$config['area'][] = $area;
						}
					} else {
						$config['area'] = [];
					}

					if (in_array($builderArea, $config['area']) || empty($config['area'])) {
						unset($config['form']);
						unset($config['fields']);
						$config['defaultValues']   = $element->getFormDefaultValues();
						$list[$element->getType()] = $config;
					}
				}
			}

			foreach ($list as &$_element) {
				if (isset($_element['children']) && $_element['children']) {
					foreach ($list as $k => &$v) {
						if ($v['type'] == $_element['children']) {
							$v['parent'] = $_element['type'];
							break;
						}
					}
				}
			}
			$list['profile'] = [
				'type'          => 'profile',
				'element'       => 'Magezon_Builder/js/builder/element/profile',
				'name'          => __('Profile'),
				'modalVisible'  => false,
				'allowed_types' => 'row'
			];
    		$cacheData = $this->builderCacheManager->saveToCache($key, $list);
		}
        return $list;
	}

    /**
     * @param string $builderArea
     * @return $this
     */
    protected function setBuilderArea($builderArea)
    {
        $this->_builderArea = $builderArea;
        return $this;
    }

    /**
     * Id field name getter
     *
     * @return string
     */
    public function getBuilderArea()
    {
        return $this->_builderArea;
    }

    /**
     * Get available widgets.
     *
     * @param \Magento\Framework\DataObject $config Editor element config
     * @return array
     */
    public function getAvailableWidgets($config)
    {
        $result = [];

        if (!$config->hasData('widget_types')) {
        	$widget = ObjectManager::getInstance()->get(\Magento\Widget\Model\Widget::class);
            $allWidgets = $widget->getWidgetsArray();
            $skipped = $this->_getSkippedWidgets();
            foreach ($allWidgets as $widget) {
                if (is_array($skipped) && in_array($widget['type'], $skipped)) {
                    continue;
                }
                $result[$widget['type']] = $widget['name']->getText();
            }
        }

        return $result;
    }

    /**
     * Return array of widgets disabled for selection
     *
     * @return string[]
     */
    protected function _getSkippedWidgets()
    {
    	$registry = ObjectManager::getInstance()->get(\Magento\Framework\Registry::class);
        return $registry->registry('skip_widgets');
    }

    /**
     * Return url to error image
     *
     * @return string
     */
    public function getErrorImageUrl()
    {
    	$assetRepo = ObjectManager::getInstance()->get(\Magento\Framework\View\Asset\Repository::class);
        return $assetRepo->getUrl('Magento_Widget::error.png');
    }

    /**
     * Return list of available placeholders for widget
     *
     * @return array
     */
    public function getWidgetPlaceholderImageUrls()
    {
    	$widget = ObjectManager::getInstance()->get(\Magento\Widget\Model\Widget::class);
        return $widget->getPlaceholderImageUrls();
    }

    public function getPageLayouts()
    {
    	$pageLayout = ObjectManager::getInstance()->get(\Magento\Cms\Model\Page\Source\PageLayout::class);
        return $pageLayout->toOptionArray();
    }
}