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

namespace Magezon\CustomerAttachments\Model\Config\Source\EmailTemplate;

class NewAttachment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Email\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templatesFactory;

    /**
     * @var \Magento\Email\Model\Template\Config
     */
    protected $_emailConfig;

    /**
     * @param \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory 
     * @param \Magento\Email\Model\Template\Config                          $emailConfig      
     */
    public function  __construct(
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig
    ) {
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig      = $emailConfig;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection    = $this->_templatesFactory->create();
        $collection->load();
        $options       = $collection->toOptionArray();
        $templateId    = 'mca_new_attachment';
        $templateLabel = $this->_emailConfig->getTemplateLabel($templateId);
        $templateLabel = __('%1 (Default)', $templateLabel);
        array_unshift($options, ['value' => $templateId, 'label' => $templateLabel]);
        $emailTemplates = [];
        foreach ($options as $k => $v) {
            $emailTemplates[$v['value']] = $v['label'];
        }
        return $emailTemplates;
    }
}
