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

class ItemInfo extends \Magento\Backend\App\Action
{
    /**
     * @var \Magezon\Builder\Data\SourcesFactory
     */
    protected $sourcesFactory;

    /**
     * @param \Magento\Backend\App\Action\Context  $context        
     * @param \Magezon\Builder\Data\SourcesFactory $sourcesFactory 
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magezon\Builder\Data\SourcesFactory $sourcesFactory
    ) {
        parent::__construct($context);
        $this->sourcesFactory = $sourcesFactory;
    }

    public function execute()
    {
        $data = [];
        try {
            $post = $this->getRequest()->getPostValue();
            if (isset($post['type']) && $post['type'] && isset($post['q']) && $post['q']) {
                $sources = $this->sourcesFactory->create();
                $source  = $sources->getSource($post['type']);
                $field   = isset($post['field']) ? $post['field'] : '';
                if ($source) {
                    $data = $source->getItem($post['q'], $field);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while processing the request.'));
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($data)
        );
        return;
    }
}