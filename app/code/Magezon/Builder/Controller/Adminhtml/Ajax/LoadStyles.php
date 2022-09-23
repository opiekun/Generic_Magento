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

namespace Magezon\Builder\Controller\Adminhtml\Ajax;

class LoadStyles extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Magezon\Builder\Helper\Data
     */
    protected $builderHelper;

    /**
     * @param \Magento\Backend\App\Action\Context   $context       
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory 
     * @param \Magezon\Core\Helper\Data             $coreHelper    
     * @param \Magezon\Builder\Helper\Data          $builderHelper 
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magezon\Core\Helper\Data $coreHelper,
        \Magezon\Builder\Helper\Data $builderHelper
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
        $this->coreHelper    = $coreHelper;
        $this->builderHelper = $builderHelper;
    }

    public function execute()
    {
        $result = [];
        try {
            $html    = '';
            $profile = str_replace('"disable_element":true', '"disable_element":false', $this->getRequest()->getPost('profile'));
            $profile = $this->coreHelper->unserialize($profile);
            if (is_array($profile) && isset($profile['elements']) && is_array($profile['elements'])) {
                $block = $this->layoutFactory->create()->createBlock(\Magezon\Builder\Block\Profile::class);
                $block->setElements($profile['elements']);
                $html = $block->getStylesHtml();
            }
            if (isset($profile['custom_css'])) {
                $html .= '<style>' . $profile['custom_css'] . '</style>';
            }
            $html .= $this->builderHelper->getConfig('customization/css');
            $result['html']   = $html;
            $result['status'] = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
        } catch (\Exception $e) {
            $result['status']  = false;
            $result['message'] = __('Something went wrong while process preview styles.');
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the page.'));
        }
        $this->getResponse()->setBody($this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result));
    }
}