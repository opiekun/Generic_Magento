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

namespace Magezon\CustomerAttachments\Model\Attachment;

use Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var array
     */
    protected $meta;

    /**
     * @var Filesystem
     */
    private $fileInfo;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @param string                                   $name                        
     * @param string                                   $primaryFieldName            
     * @param string                                   $requestFieldName            
     * @param CollectionFactory                        $attachmentCollectionFactory 
     * @param DateTime                                 $dateTime                    
     * @param DataPersistorInterface                   $dataPersistor               
     * @param \Magento\Framework\UrlInterface          $urlBuilder                  
     * @param \Magezon\CustomerAttachments\Helper\File $fileHelper                  
     * @param array                                    $meta                        
     * @param array                                    $data                        
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $attachmentCollectionFactory,
        DateTime $dateTime,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->collection    = $attachmentCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta       = $this->prepareMeta($this->meta);
        $this->dateTime   = $dateTime;
        $this->urlBuilder = $urlBuilder;
        $this->fileHelper = $fileHelper;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $attachment \Magezon\CustomerAttachments\Model\Attachment */
        foreach ($items as $attachment) {
            $this->loadedData[$attachment->getId()] = $attachment->getData();
        }

        $data = $this->dataPersistor->get('customerattachments_attachment');
        if (!empty($data)) {
            $attachment = $this->collection->getNewEmptyItem();
            $attachment->setData($data);
            foreach (['from_date', 'to_date'] as $field) {
                $value        = !isset($data[$field]) ? null : $this->dateTime->formatDate($data[$field]);
                $data[$field] = $value;
            }
            $data = $this->convertValues($data);
            $this->loadedData[$attachment->getId()] = $data;
            $this->dataPersistor->clear('customerattachments_attachment');
        } else if (!empty($this->loadedData)) {
            $data = $this->convertValues($this->loadedData[$attachment->getId()]);
            $this->loadedData[$attachment->getId()] = $data;
            if ($attachment->getData('enable_condition')=='') {
                $this->loadedData[$attachment->getId()]['enable_condition'] = 0;
            }
        }
        
        return $this->loadedData;
    }

    /**
     * Converts category image data to acceptable for rendering format
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param array $attachmentData
     * @return array
     */
    private function convertValues($attachmentData)
    {
        if (isset($attachmentData['attachment_file'])) {
            $fileName = $attachmentData['attachment_file'];
            if (is_array($fileName)) {
                $fileName = $this->getFileName($fileName);
            }

            if ($fileName && $this->getFileInfo()->isExist($fileName)) {
                $stat = $this->getFileInfo()->getStat($fileName);
                $mime = $this->getFileInfo()->getMimeType($fileName);

                $attachmentData['attachment_file'] = [
                    [
                        'file' => $fileName,
                        'name' => $this->fileHelper->getFileFromPathFile($fileName),
                        'url'  => $this->urlBuilder->getUrl(
                            'customerattachments/attachment/download',
                            [
                                'attachment_id' => $attachmentData['attachment_id'],
                                '_secure'       => true
                            ]
                        ),
                        'size' => isset($stat) ? $stat['size'] : 0,
                        'type' => $mime
                    ]
                ];
            } else {
                $attachmentData['attachment_file'] = '';
            }
        }
        return $attachmentData;
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in a case when $value is not an array
     *
     * @param array $value Attribute value
     * @return string
     */
    private function getFileName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }
        return '';
    }

    /**
     * Get FileInfo instance
     *
     * @return FileInfo
     *
     * @deprecated 101.1.0
     */
    private function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(FileInfo::class);
        }
        return $this->fileInfo;
    }
}
