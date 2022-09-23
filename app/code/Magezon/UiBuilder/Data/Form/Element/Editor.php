<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_UiBuilder
 * @copyright Copyright (C) 2018 Magezon (https://www.magezon.com)
 */

namespace Magezon\UiBuilder\Data\Form\Element;

class Editor extends AbstractElement
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param Factory                             $factoryElement    
     * @param CollectionFactory                   $factoryCollection 
     * @param \Magento\Backend\Model\UrlInterface $backendUrl        
     * @param \Magezon\UiBuilder\Helper\Data        $builderHelper     
     * @param \Magezon\Core\Helper\Data           $coreHelper        
     * @param \Magento\Cms\Model\Wysiwyg\Config   $wysiwygConfig     
     * @param array                               $data              
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magezon\UiBuilder\Helper\Data $builderHelper,
        \Magezon\Core\Helper\Data $coreHelper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $backendUrl, $builderHelper, $coreHelper);
        $this->_wysiwygConfig = $wysiwygConfig;
    }

    public function _construct()
    {
        $this->setType('editor');
    }

    public function getElementConfig()
    {
        $config = $this->_wysiwygConfig->getConfig();
        $config = $config->getData();
        $editorConfig = array_replace_recursive($config, [
            'settings' => [
                'theme_advanced_buttons1'           => 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
                'theme_advanced_buttons2'           => false,
                'theme_advanced_buttons3'           => false,
                'theme_advanced_buttons4'           => false,
                'theme_advanced_statusbar_location' => 'bottom'
            ]
        ]);

        if (isset($this->getData('config')['editorConfig'])) {
            $editorConfig = array_replace_recursive($editorConfig, $this->getData('config')['editorConfig']);
        }

        $config = array_replace_recursive([
            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
            'formElement'   => \Magento\Ui\Component\Form\Element\Input::NAME,
            'component'     => 'Magezon_UiBuilder/js/form/element/editor',
            'editorUrl'     => $this->_backendUrl->getUrl('uibuilder/builder/wysiwyg'),
            'elementTmpl'   => 'Magezon_UiBuilder/form/element/editor',
            'editorConfig'  => $editorConfig,
        ], (array) $this->getData('config'));

        return [
            'arguments' => [
                'data' => [
                    'config' => $config
                ]
            ]
        ];
    }
}
