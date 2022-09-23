<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_CustomerAttachments
 * @copyright Copyright (C) 2018 Magezon (https://magezon.com)
 */

namespace Magezon\CustomerAttachments\Block\Adminhtml\Attachment;

use \Magezon\CustomerAttachments\Model\Attachment;

class AssignedCustomers extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magezon_Core::assign_items.phtml';

    /**
     * @var \Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Tab\Customer
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry    = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Tab\Customer::class,
                'attachment.customer.grid'
            )->setId('attachment_customersfixed')->setData('filter_type', Attachment::TYPE_FIXED);
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getJson()
    {
        $customers = $this->getAttachment()->getCustomersPosition(Attachment::TYPE_FIXED);
        if (!empty($customers)) {
            return $this->jsonEncoder->encode($customers);
        }
        return '{}';
    }

    /**
     * Retrieve current attachment
     *
     * @return Attachment
     */
    public function getAttachment()
    {
        return $this->registry->registry('customerattachments_attachment');
    }

    public function getElementName()
    {
        return 'attachment_customers';
    }

    public function getFormPart()
    {
        return 'customerattachments_attachment_form';
    }

    public function getAjaxParam()
    {
        return 'selected_customers';
    }
}
