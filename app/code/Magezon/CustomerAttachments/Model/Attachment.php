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

namespace Magezon\CustomerAttachments\Model;

use Magezon\CustomerAttachments\Api\Data\AttachmentInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;

class Attachment extends \Magento\Rule\Model\AbstractModel implements AttachmentInterface
{
    const TYPE_FIXED     = 'fixed';
    const TYPE_AUTO      = 'auto';
    
    const FILE_TYPE_URL  = 'url';
    const FILE_TYPE_FILE = 'file';

    /**
     * ATTACHMENT page cache tag
     */
    const CACHE_TAG = 'customerattachments_s';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customerattachments_attachment';

    /**
     * @var Data\Condition\Converter
     */
    protected $ruleConditionConverter;

    /**
     * @var \Magezon\CustomerAttachments\Model\Attachment\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context                                       $context                 
     * @param \Magento\Framework\Registry                                            $registry                
     * @param \Magento\Framework\Data\FormFactory                                    $formFactory             
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                   $localeDate              
     * @param \Magezon\CustomerAttachments\Model\Attachment\Condition\CombineFactory $combineFactory          
     * @param \Magento\CatalogRule\Model\Rule\Action\CollectionFactory               $actionCollectionFactory 
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null           $resource                
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                     $resourceCollection      
     * @param array                                                                  $data                    
     * @param ExtensionAttributesFactory|null                                        $extensionFactory        
     * @param AttributeValueFactory|null                                             $customAttributeFactory  
     * @param \Magento\Framework\Serialize\Serializer\Json|null                      $serializer              
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magezon\CustomerAttachments\Model\Attachment\Condition\CombineFactory $combineFactory,
        \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory $customAttributeFactory = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_combineFactory           = $combineFactory;
        $this->_actionCollectionFactory  = $actionCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magezon\CustomerAttachments\Model\ResourceModel\Attachment::class);
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * Getter for rule actions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * Get ID
     * 
     * @return int|null
     */
    public function getId()
    {
        return parent::getData(self::ATTACHMENT_ID);
    }

    /**
     * Set ID
     * 
     * @param int $id
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ATTACHMENT_ID, $id);
    }

    /**
     * Get attachment name
     * 
     * @return string
     */
    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * Set attachment name
     * 
     * @param string $name
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get decription
     *
     * @return string|null
     */
    public function getDescription()
    {
        return parent::getData(self::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get number of downloads
     *
     * @return int|null
     */
    public function getNumberOfDownloads()
    {
        return parent::getData(self::NUMBER_OF_DOWNLOADS);
    }

    /**
     * Set number of downloads
     *
     * @param string $numberOfDownloads
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setNumberOfDownloads($numberOfDownloads)
    {
        return $this->setData(self::NUMBER_OF_DOWNLOADS, $numberOfDownloads);
    }

    /**
     * Get attachment url
     *
     * @return string|null
     */
    public function getAttachmentUrl()
    {
        return parent::getData(self::ATTACHMENT_URL);
    }

    /**
     * Set attachment url
     *
     * @param string $url
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setAttachmentUrl($url)
    {
        return $this->setData(self::ATTACHMENT_URL, $url);
    }

    /**
     * Get attachment file
     *
     * @return string|null
     */
    public function getAttachmentFile()
    {
        return parent::getData(self::ATTACHMENT_FILE);
    }

    /**
     * Set attachment file
     *
     * @param string $file
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setAttachmentFile($file)
    {
        return $this->setData(self::ATTACHMENT_FILE, $file);
    }

    /**
     * Get attachment file content
     * 
     * @return mixed|string
     */
    public function getAttachmentFileContent()
    {
        return parent::getData(self::ATTACHMENT_FILE_CONTENT);
    }

    /**
     * Set attachment file content
     * 
     * @param mixed|string $fileContent
     * @return $this
     */
    public function setAttachmentFileContent($fileContent)
    {
        return $this->setData(self::ATTACHMENT_FILE_CONTENT, $fileContent);
    }

    /**
     * Get attachment type
     *
     * @return string|null
     */
    public function getAttachmentType()
    {
        return parent::getData(self::ATTACHMENT_TYPE);
    }

    /**
     * Set attachment type
     *
     * @param string $type
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setAttachmentType($type)
    {
        return $this->setData(self::ATTACHMENT_TYPE, $type);
    }

    /**
     * Get attachment hash
     *
     * @return string|null
     */
    public function getAttachmentHash()
    {
        return parent::getData(self::ATTACHMENT_HASH);
    }

    /**
     * Set attachment hash
     *
     * @param string $hash
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setAttachmentHash($hash)
    {
        return $this->setData(self::ATTACHMENT_HASH, $hash);
    }

    /**
     * Get from date
     *
     * @return string|null
     */
    public function getFromDate()
    {
        return parent::getData(self::FROM_DATE);
    }

    /**
     * Set from date
     *
     * @param string $fromDate
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    /**
     * Get to date
     *
     * @return string|null
     */
    public function getToDate()
    {
        return parent::getData(self::TO_DATE);
    }

    /**
     * Set to date
     *
     * @param string $toDate
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::TO_DATE, $toDate);
    }

    /**
     * Returns rule condition
     *
     * @return \Magezon\CustomerAttachments\Api\Data\ConditionInterface|null
     */
    public function getRuleCondition()
    {
        return $this->getRuleConditionConverter()->arrayToDataModel($this->getConditions()->asArray());
    }

    /**
     * @param \Magezon\CustomerAttachments\Api\Data\ConditionInterface $condition
     * @return $this
     */
    public function setRuleCondition($condition)
    {
        $this->getConditions()
            ->setConditions([])
            ->loadArray($this->getRuleConditionConverter()->dataModelToArray($condition));
        return $this;
    }

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getIsActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime()
    {
        return parent::getData(self::CREATION_TIME);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime()
    {
        return parent::getData(self::UPDATE_TIME);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * @return Data\Condition\Converter
     * @deprecated
     */
    private function getRuleConditionConverter()
    {
        if (null === $this->ruleConditionConverter) {
            $this->ruleConditionConverter = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magezon\CustomerAttachments\Model\Data\Condition\Converter::class);
        }
        return $this->ruleConditionConverter;
    }

    /**
     * @return array
     */
    public function getCustomerIds()
    {
        if (!$this->hasData('customer_ids')) {
            $ids = $this->_getResource()->getCustomerIds($this->getId());
            $this->setData('customer_ids', $ids);
        } 
        return (array) $this->_getData('customer_ids');
    }

    /**
     * The array returned has the following format:
     * array($customerId => $position)
     *
     * @return array
     */
    public function getCustomersPosition($type = '')
    {
        $array = $this->getData('customers_position' . $type);
        if ($array === null) {
            $array = $this->_getResource()->getCustomersPosition($this->getId(), $type);
            $this->setData('customers_position' . $type, $array);
        }
        return $array;
    }

    /**
     * @return string|null
     */
    public function getAttachmentKey()
    {
        $key = '';
        if ((($this->getAttachmentType() == self::FILE_TYPE_FILE && $this->getAttachmentFile())
            || ($this->getAttachmentType() == self::FILE_TYPE_URL && $this->getAttachmentUrl()))
            && ($attachmentHash = $this->getAttachmentHash())
        ) {
            $key = $attachmentHash;
        }
        return $key;
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }
}
