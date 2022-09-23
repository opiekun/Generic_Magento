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

class Conditions extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\CatalogWidget\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @param \Magento\Backend\App\Action\Context      $context       
     * @param \Magento\Framework\View\LayoutFactory    $layoutFactory 
     * @param \Magento\CatalogWidget\Model\RuleFactory $ruleFactory   
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\CatalogWidget\Model\RuleFactory $ruleFactory
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
        $this->ruleFactory   = $ruleFactory;
    }

    public function execute()
    {
        $result['status'] = false;
        try {
            $post = $this->getRequest()->getPostValue();
            $data = [];
            $this->getRequest()->setParam('mgz_builder', true);
            if (isset($post['conditions'])) {
                $data['conditions'] = $post['conditions'];
            }
            $result['html']   = $this->getConditions($data, $post['id']);
            $result['status'] = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result['message'] = $e->getMessage();
            $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
        } catch (\Exception $e) {
            $result['message'] = __('Something went wrong while process the request.');
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while processing the request.'));
        }
        $this->getResponse()->setBody($this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result));
    }

    /**
     * @param  array  $parameters 
     * @param  string $htmlId     
     * @return string             
     */
    public function getConditions($parameters, $htmlId) {
        $block = $this->layoutFactory->create()->createBlock('\Magezon\Core\Block\Adminhtml\Product\Widget\Conditions');
        $block->setTemplate('Magezon_Builder::product/widget/conditions.phtml');
        $block->setData('parameters', $parameters);
        $block->setData('htmlid', $htmlId);
        $rule = $this->ruleFactory->create();
        $block->setRule($rule);
        return $block->toHtml();
    }
}