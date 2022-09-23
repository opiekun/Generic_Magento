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

namespace Magezon\CustomerAttachments\Helper;

use Magento\Framework\UrlInterface;
use Magezon\CustomerAttachments\Model\Attachment;

class File extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Downloadable\Helper\Download
     */
    protected $downloadHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var array
     */
    protected $customerAttachments = [];

    /**
     * @param \Magento\Framework\App\Helper\Context                                         $context                     
     * @param \Magento\Store\Model\StoreManagerInterface                                    $storeManager                
     * @param \Magento\Framework\Filesystem                                                 $filesystem                  
     * @param \Magento\MediaStorage\Helper\File\Storage\Database                            $coreFileStorageDatabase     
     * @param \Magento\Framework\App\ResourceConnection                                     $resource                    
     * @param \Magento\Downloadable\Helper\Download                                         $downloadHelper              
     * @param \Magento\Customer\Model\Session                                               $customerSession             
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                             $customerRepository          
     * @param \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Downloadable\Helper\Download $downloadHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory
    ) {
        $this->storeManager                = $storeManager;
        $this->mediaDirectory              = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->coreFileStorageDatabase     = $coreFileStorageDatabase;
        $this->resource                    = $resource;
        $this->downloadHelper              = $downloadHelper;
        $this->customerSession             = $customerSession;
        $this->customerRepository          = $customerRepository;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param string $file
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @param string $file
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_MEDIA
        ) . $this->getBaseTmpMediaPath();
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return 'customerattachments/attachment';
    }

    /**
     * Filesystem directory path of temporary product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return 'customerattachments/tmp';
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseMediaPath();
    }

    /**
     * Return file name form file path
     *
     * @param string $pathFile
     * @return string
     */
    public function getFileFromPathFile($pathFile)
    {
        $file = substr($pathFile, strrpos($pathFile, '/') + 1);
        return $file;
    }

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Checking file for moving and move it
     *
     * @param string $fileName
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveFileFromTmp($fileName)
    {
		$baseTmpPath      = $this->getBaseTmpMediaPath();
		$basePath         = $this->getBaseMediaPath();
		$baseImagePath    = $this->getFilePath($basePath, $fileName);
		$baseTmpImagePath = $this->getFilePath($baseTmpPath, $fileName);

        try {
        	$fileAbsPath = $this->mediaDirectory->getAbsolutePath($baseTmpImagePath);
        	if (file_exists($fileAbsPath)) {
	            $this->coreFileStorageDatabase->copyFile(
	                $baseTmpImagePath,
	                $baseImagePath
	            );
	            $this->mediaDirectory->renameFile(
	                $baseTmpImagePath,
	                $baseImagePath
	            );
	        }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        return $fileName;
    }

    /**
     * @param  string $fileName
     * @return boolean
     */
    public function deleteFile($fileName)
    {
    	$fileAbsPath = $this->mediaDirectory->getAbsolutePath($this->getBaseMediaPath() . $fileName);
    	if (file_exists($fileAbsPath)) {
    		unlink($fileAbsPath);
    	}
    	return true;
    }

    /**
     * @return string
     */
    public function getFileHash()
    {
        $fileHash = strtr(
                base64_encode(
                    microtime()
                    ),
                '+/=',
                '-_,'
            );
        return $fileHash;
    }

    /**
     * @param  int $attachmentId 
     * @param  int $customerId   
     * @return int               
     */
    public function getNumberOfDownloadsUsed($attachmentId, $customerId)
    {
        $connection  = $this->resource->getConnection();
        $reportTable = $this->resource->getTableName('customerattachments_customer_attachment_report');
        $select      = $connection->select()->from($reportTable, 'COUNT(*)')
        ->where('attachment_id=?', $attachmentId)
        ->where('customer_id=?', $customerId);
        return (int)$connection->fetchOne($select);
    }

    /**
     * Update attachments reports
     * 
     * @param  int $attachmentId          
     * @param  int $customerId            
     * @param  int $numberOfDownloadsUsed 
     * @return this                        
     */
    public function saveReportDownload($attachmentId, $customerId, $numberOfDownloadsUsed)
    {
        $reportTable = $this->resource->getTableName('customerattachments_customer_attachment_report');
        $connection = $this->resource->getConnection();
        $data = [
            'attachment_id' => $attachmentId,
            'customer_id'   => $customerId,
            'website_id'    => $this->storeManager->getWebsite()->getId()
        ];
        $connection->insert($reportTable, $data);

        $table = $this->resource->getTableName('customerattachments_customer_attachment');
        $connection->update($table, ['number_of_downloads_used' => $numberOfDownloadsUsed],
            ['attachment_id=?' => $attachmentId, 'customer_id=?' => $customerId]
        );
        return $this;
    }

    /**
     * Get file size
     * @param  Attachment $attachment
     * @return int
     */
    public function getFileSize(Attachment $attachment)
    {
        $resource = $resourceType = '';
        if ($attachment->getAttachmentType() == Attachment::FILE_TYPE_URL) {
            $resource     = $attachment->getAttachmentUrl();
            $resourceType = Attachment::FILE_TYPE_URL;
        } else if ($attachment->getAttachmentType() == Attachment::FILE_TYPE_FILE) {
            $resourceType = Attachment::FILE_TYPE_FILE;
            $resource     = $this->getFilePath(
                $this->getBaseMediaPath(),
                $attachment->getAttachmentFile()
            );
        }
        $this->downloadHelper->setResource($resource, $resourceType);
        return $helper->getFileSize();
    }

    /**
     * @param  int|null $customerId 
     * @return \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection
     */
    public function getCustomerAttachments($customerId = null, $websiteId = null)
    {
        if (!$customerId) {
            $customerId = $this->customerSession->getCustomerId();
        }

        if (!$websiteId) {
            $websiteId = $this->customerRepository->getById($customerId)->getWebsiteId();
        }

        if (!isset($this->customerAttachments[$customerId])) {
            $collection = $this->attachmentCollectionFactory->create();
            $collection->addWebsiteFilter($websiteId)
            ->addCustomersFilter((int)$customerId)
            ->addDateToFilter()
            ->addIsActiveFilter()
            ->addFieldToFilter('attachment_hash', ['neq' => 'NULL']);
            $this->customerAttachments[$customerId] = $collection;
        }

        return $this->customerAttachments[$customerId];
    }

    /**
     * @return boolean
     */
    public function isHideTitle()
    {
        if (!$this->getCustomerAttachments()->count()) {
            return false;
        }
        return true;
    }
}
