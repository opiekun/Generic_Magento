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
 * @package   Magezon_ProductAttachments
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Controller\Adminhtml\Conditions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magezon\Core\Block\Adminhtml\Conditions\Product;
use Magento\CatalogRule\Model\Rule;

class ProductList extends Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Product
     */
    protected $gridProduct;

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory,
        Registry $registry,
        Product $gridProduct
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_coreRegistry = $registry;
        $this->gridProduct = $gridProduct;
    }

    /**
     * Grid Action
     * Display list of products related to current post
     *
     * @return Raw
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if (isset($data['rule'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
        }
        unset($data['conditions_serialized']);
        unset($data['actions_serialized']);
        $file = $this->_objectManager->create(Rule::class);
        $file->loadPost($data);
        $this->_coreRegistry->unregister('mgz_conditions_model');
        $this->_coreRegistry->register('mgz_conditions_model', $file);
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Magezon\Core\Block\Adminhtml\Conditions\Product::class,
                'product.grid'
            )->toHtml()
        );
    }
}
