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

namespace Magezon\Builder\Ui\Component\Form\Element;

use \Magento\Framework\App\ObjectManager;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Exception\NoSuchEntityException;

class Builder extends \Magento\Ui\Component\Form\Element\AbstractElement
{
    const NAME = 'wysiwyg';

    /**
     * @param ContextInterface                      $context       
     * @param FormFactory                           $formFactory   
     * @param ConfigInterface                       $wysiwygConfig 
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory 
     * @param \Magento\Framework\Registry           $registry      
     * @param array                                 $components    
     * @param array                                 $data          
     * @param array                                 $config        
     */
    public function __construct(
        ContextInterface $context,
        FormFactory $formFactory,
        ConfigInterface $wysiwygConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $registry,
        array $components = [],
        array $data = [],
        array $config = []
    ) {
        if (!isset($config['disableMagezonBuilder']) || !$config['disableMagezonBuilder']) {
            $htmlId                        = $context->getNamespace() . '_' . $data['name'];
            $data['config']['htmlId']      = $htmlId;
            $data['config']['component']   = 'Magezon_Builder/js/ui/form/element/builder';
            $data['config']['elementTmpl'] = 'Magezon_Builder/ui/form/element/builder';
            $data['config']['template']    = 'ui/form/field';
            $block  = $layoutFactory->create()->createBlock(\Magento\Backend\Block\Template::class)
            ->addData($config)
            ->setTemplate('Magezon_Builder::ajax.phtml')
            ->setTargetId($htmlId);
            if (isset($config['ajax_data'])) {
                $block->setAjaxData($config['ajax_data']);
                $data['config']['content'] = $block->toHtml();
            }
        } else {
            if ($this->getMagentoVersion() >= '2.4.3') {
                $pageBuilderConfig = false;
                $overrideSnapshot = true;
                $attrRepository = $this->getAttrRepository();
                $stageConfig = $this->getStateConfig();
                $pageBuilderState = $this->getPageBuilderState();
                $this->assetRepo = $this->getAssetRepo();
                $wysiwygConfigData = isset($config['wysiwygConfigData']) ? $config['wysiwygConfigData'] : [];

                // If a dataType is present we're dealing with an attribute
                if (isset($config['dataType'])) {
                    try {
                        $attribute = $attrRepository->get($data['name']);

                        if ($attribute) {
                            $config['wysiwyg'] = (bool)$attribute->getIsWysiwygEnabled();
                        }
                    } catch (NoSuchEntityException $e) {
                        $config['wysiwyg'] = true;
                    }
                }

                $isEnablePageBuilder = isset($wysiwygConfigData['is_pagebuilder_enabled'])
                && !$wysiwygConfigData['is_pagebuilder_enabled']
                || false;
                if (!$pageBuilderState->isPageBuilderInUse($isEnablePageBuilder)) {
                    // This is not done using definition.xml due to https://github.com/magento/magento2/issues/5647
                    $data['config']['component'] = 'Magento_PageBuilder/js/form/element/wysiwyg';

                    // Override the templates to include our KnockoutJS code
                    $data['config']['template'] = 'ui/form/field';
                    $data['config']['elementTmpl'] = 'Magento_PageBuilder/form/element/wysiwyg';
                    $wysiwygConfigData = $stageConfig->getConfig();
                    $wysiwygConfigData['pagebuilder_button'] = true;
                    $wysiwygConfigData['pagebuilder_content_snapshot'] = true;
                    $wysiwygConfigData = $this->processBreakpointsIcons($wysiwygConfigData);

                    if ($overrideSnapshot) {
                        $pageBuilderConfig = $pageBuilderConfig ?: ObjectManager::getInstance()->get('Magento\PageBuilder\Model\Config');
                        $wysiwygConfigData['pagebuilder_content_snapshot'] = $pageBuilderConfig->isContentPreviewEnabled();
                    }

                    // Add Classes for Page Builder Stage
                    if (isset($wysiwygConfigData['pagebuilder_content_snapshot'])
                        && $wysiwygConfigData['pagebuilder_content_snapshot']) {
                        $data['config']['additionalClasses'] = [
                            'admin__field-wide admin__field-page-builder' => true
                        ];
                    }

                    $data['config']['wysiwygConfigData'] = isset($config['wysiwygConfigData']) ?
                    array_replace_recursive($config['wysiwygConfigData'], $wysiwygConfigData) :
                    $wysiwygConfigData;
                    $wysiwygConfigData['activeEditorPath'] = 'Magento_PageBuilder/pageBuilderAdapter';

                    $config['wysiwygConfigData'] = $wysiwygConfigData;
                    $data['config']['content'] = '';
                } else {
                    $wysiwygConfigData = isset($config['wysiwygConfigData']) ? $config['wysiwygConfigData'] : [];
                    $this->form = $formFactory->create();
                    $wysiwygId = $context->getNamespace() . '_' . $data['name'];
                    $this->editor = $this->form->addField(
                        $wysiwygId,
                        \Magento\Framework\Data\Form\Element\Editor::class,
                        [
                            'force_load' => true,
                            'rows'       => isset($config['rows']) ? $config['rows'] : 20,
                            'name'       => $data['name'],
                            'config'     => $wysiwygConfig->getConfig($wysiwygConfigData),
                            'wysiwyg'    => isset($config['wysiwyg']) ? $config['wysiwyg'] : null
                        ]
                    );
                    $data['config']['content'] = $this->editor->getElementHtml();
                    $data['config']['wysiwygId'] = $wysiwygId;
                }
            } else {
                $wysiwygConfigData = isset($config['wysiwygConfigData']) ? $config['wysiwygConfigData'] : [];
                $this->form = $formFactory->create();
                $wysiwygId = $context->getNamespace() . '_' . $data['name'];
                $this->editor = $this->form->addField(
                    $wysiwygId,
                    \Magento\Framework\Data\Form\Element\Editor::class,
                    [
                        'force_load' => true,
                        'rows'       => isset($config['rows']) ? $config['rows'] : 20,
                        'name'       => $data['name'],
                        'config'     => $wysiwygConfig->getConfig($wysiwygConfigData),
                        'wysiwyg'    => isset($config['wysiwyg']) ? $config['wysiwyg'] : null
                    ]
                );
                $data['config']['content'] = $this->editor->getElementHtml();
                $data['config']['wysiwygId'] = $wysiwygId;
            }
            
        }

        parent::__construct($context, $components, $data);
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }

    /**
     * @return Magento\Framework\View\Asset\Repository
     */
    public function getAssetRepo() {
        return ObjectManager::getInstance()->get('Magento\Framework\View\Asset\Repository');
    }

    /**
     * @return Magento\PageBuilder\Model\State
     */
    public function getPageBuilderState() {
        return ObjectManager::getInstance()->get('Magento\PageBuilder\Model\State');
    }

    /**
     * @return Magento\PageBuilder\Model\Stage\Config
     */
    public function getStateConfig() {
        return ObjectManager::getInstance()->get('Magento\PageBuilder\Model\Stage\Config');
    }

    /**
     * @return Magento\Catalog\Api\CategoryAttributeRepositoryInterface
     */
    public function getAttrRepository() {
        return ObjectManager::getInstance()->get('Magento\Catalog\Api\CategoryAttributeRepositoryInterface');
    }

    /**
     * @return string
     */
    public function getMagentoVersion() {
        return ObjectManager::getInstance()->get('Magento\Framework\App\ProductMetadataInterface')->getVersion();
    }

    /**
     * Process viewport icon paths
     *
     * @param array $wysiwygConfigData
     * @return array
     */
    private function processBreakpointsIcons(array $wysiwygConfigData): array
    {
        if ($wysiwygConfigData && isset($wysiwygConfigData['viewports'])) {
            foreach ($wysiwygConfigData['viewports'] as $breakpoint => $attributes) {
                if (isset($attributes['icon'])) {
                    $wysiwygConfigData['viewports'][$breakpoint]['icon'] = $this->assetRepo->getUrl(
                        $attributes['icon']
                    );
                }
            }
        }
        return $wysiwygConfigData;
    }
}
