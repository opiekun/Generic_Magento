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

namespace Magezon\CustomerAttachments\Block\Adminhtml\Customer\Edit;

class Attachments extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'customer/attachments.phtml';

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $fileCollectionFactory;

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @var \Magezon\CustomerAttachments\Model\Attachment\FileInfo
     */
    protected $fileInfo;

    /**
     * Block config data
     *
     * @var \Magento\Framework\DataObject
     */
    protected $_config;

    /**
     * @param \Magento\Backend\Block\Template\Context                                       $context               
     * @param \Magento\Framework\Registry                                                   $registry              
     * @param \Magento\Framework\Data\FormFactory                                           $formFactory           
     * @param \Magento\Framework\Json\EncoderInterface                                      $jsonEncoder           
     * @param \Magento\Framework\Json\DecoderInterface                                      $jsonDecoder           
     * @param \Magento\Framework\UrlInterface                                               $urlBuilder            
     * @param \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $fileCollectionFactory 
     * @param \Magezon\CustomerAttachments\Helper\File                                      $fileHelper            
     * @param \Magezon\CustomerAttachments\Model\Attachment\FileInfo                        $fileInfo              
     * @param array                                                                         $data                  
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\ImportExport\Helper\Data $importHelper,
        \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $fileCollectionFactory,
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        \Magezon\CustomerAttachments\Model\Attachment\FileInfo $fileInfo,
        array $data = []
        ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_jsonEncoder          = $jsonEncoder;
        $this->_jsonDecoder          = $jsonDecoder;
        $this->urlBuilder            = $urlBuilder;
        $this->importHelper          = $importHelper;
        $this->fileCollectionFactory = $fileCollectionFactory;
        $this->fileHelper            = $fileHelper;
        $this->fileInfo              = $fileInfo;
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Attachments');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        $total = count($this->getAttachmentData());
        return __('Attachments (%1)', $total);
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Return array of attachments
     *
     * @return array
     */
    public function getAttachmentData()
    {
        $attachments = [];
        $customerId  = (int) $this->getRequest()->getParam('id');
        $collection  = $this->fileCollectionFactory->create();
        $collection->addCustomersFilter($customerId)->setOrder('position', 'DESC');

        $i = 0;
        foreach ($collection as $item) {
            $_attachment = [
                'attachment_id'   => $item->getId(),
                'name'            => $this->escapeHtml($item->getName()),
                'description'     => $this->escapeHtml($item->getDescription()),
                'attachment_url'  => $item->getAttachmentUrl(),
                'attachment_type' => $item->getAttachmentType(),
                'attachment_file' => $item->getAttachmentFile(),
                'attachment_hash' => $item->getAttachmentHash(),
                'sort_order'      => $i,
                'edit_link'       => $this->urlBuilder->getUrl(
                    'customerattachments/attachment/edit',
                    [
                        'attachment_id' => $item->getId(),
                        '_secure'       => true
                    ]
                )
            ];

            if ($file = $item->getAttachmentFile()) {
                if ($this->isJSON($file)) {
                    $file = $this->_jsonDecoder->decode($file);
                }
                if (is_array($file)) {
                    $file = $file[0]['file'];
                }
                if ($this->fileInfo->isExist($file)) {
                    $stat = $this->fileInfo->getStat($file);
                    $_attachment['file_save'] = [
                        [
                            'file'   => $file,
                            'name'   => $this->fileHelper->getFileFromPathFile($file),
                            'size'   => isset($stat) ? $stat['size'] : 0,
                            'status' => 'old',
                            'url'    => $this->urlBuilder->getUrl(
                                'customerattachments/attachment/download',
                                [
                                    'attachment_id' => $item->getId(),
                                    '_secure'       => true
                                ]
                            )
                        ]
                    ];
                }
            }

            $attachments[] = new \Magento\Framework\DataObject($_attachment);
            $i++;
        }
        return $attachments;
    }

    /**
     * Retrieve Add button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
            )
        ->setData(
            [
                'label'          => __('Add New Attachment'),
                'id'             => 'add_attachment',
                'class'          => 'action-secondary mca-button-right',
                'data_attribute' => [
                    'action' => 'add-link'
                ]
            ]
        );
        return $addButton->toHtml();
    }

    /**
     * @param  string $string
     * @return boolean
     */
    public function isJSON($string) {
       return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

    /**
     * Retrieve Upload button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChildBlock('upload_button')->toHtml();
    }

    /**
     * Retrieve Upload URL
     *
     * @param string $type
     * @return string
     */
    public function getUploadUrl($type)
    {
        return $this->urlBuilder->getUrl(
            'customerattachments/attachment_file/upload',
            [
                'type'     => $type,
                '_secure'  => true,
                'form_key' => $this->getFormKey()
            ]
        );
    }

    /**
     * Retrieve Upload URL
     *
     * @param string $type
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->urlBuilder->getUrl(
            'customerattachments/customer/save',
            ['_secure' => true, 'form_key' => $this->getFormKey()]
        );
    }

    /**
     * Retrieve config json
     *
     * @param string $type
     * @return string
     */
    public function getConfigJson($type = 'attachments')
    {
        $this->getConfig()->setUrl($this->getUploadUrl($type));
        $this->getConfig()->setParams(['form_key' => $this->getFormKey()]);
        $this->getConfig()->setFileField($type);
        $this->getConfig()->setFilters(['all' => ['label' => __('All Attachment'), 'files' => ['*.*']]]);
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);
        return $this->_jsonEncoder->encode($this->getConfig()->getData());
    }

    /**
     * Retrieve config object
     *
     * @return \Magento\Framework\DataObject
     */
    public function getConfig()
    {
        if ($this->_config === null) {
            $this->_config = new \Magento\Framework\DataObject();
        }
        return $this->_config;
    }

    public function getMaxUploadSizeMessage()
    {
        return $this->importHelper->getMaxUploadSizeMessage();
    }
}
