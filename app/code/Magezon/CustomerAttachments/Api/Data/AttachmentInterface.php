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

namespace Magezon\CustomerAttachments\Api\Data;

interface AttachmentInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ATTACHMENT_ID           = 'attachment_id';
    const NAME                    = 'name';
    const DESCRIPTION             = 'description';
    const NUMBER_OF_DOWNLOADS     = 'number_of_downloads';
    const ATTACHMENT_URL          = 'attachment_url';
    const ATTACHMENT_FILE         = 'attachment_file';
    const ATTACHMENT_FILE_CONTENT = 'attachment_file_content';
    const ATTACHMENT_TYPE         = 'attachment_type';
    const ATTACHMENT_HASH         = 'attachment_hash';
    const FROM_DATE               = 'from_date';
    const TO_DATE                 = 'to_date';
    const IS_ACTIVE               = 'is_active';
    const CREATION_TIME           = 'creation_time';
    const UPDATE_TIME             = 'update_time';
	/**#@-*/

	/**
	 * Get ID
	 * 
	 * @return int|null
	 */
	public function getId();

	/**
	 * Set ID
	 * 
	 * @param int $id
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
	 */
	public function setId($id);

	/**
	 * Get attachment name
	 * 
	 * @return string
	 */
	public function getName();

	/**
	 * Set attachment name
	 * 
	 * @param string $name
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
	 */
	public function setName($name);

    /**
     * Get decription
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setDescription($description);

    /**
     * Get number of downloads
     *
     * @return int|null
     */
    public function getNumberOfDownloads();

    /**
     * Set number of downloads
     *
     * @param string $numberOfDownloads
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setNumberOfDownloads($numberOfDownloads);

    /**
     * Get attachment url
     *
     * @return string|null
     */
    public function getAttachmentUrl();

    /**
     * Set attachment url
     *
     * @param string $url
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setAttachmentUrl($url);

    /**
     * Get attachment file
     *
     * @return string|null
     */
    public function getAttachmentFile();

    /**
     * Set attachment file
     *
     * @param string $file
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setAttachmentFile($file);

    /**
     * Get attachment file content
     * 
     * @return mixed|string
     */
    public function getAttachmentFileContent();

    /**
     * Set attachment file content
     * 
     * @param mixed|string $fileContent
     * @return $this
     */
    public function setAttachmentFileContent($fileContent);

    /**
     * Get attachment type
     *
     * @return string|null
     */
    public function getAttachmentType();

    /**
     * Set attachment type
     *
     * @param string $type
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setAttachmentType($type);

    /**
     * Get attachment hash
     *
     * @return string|null
     */
    public function getAttachmentHash();

    /**
     * Set attachment hash
     *
     * @param string $hash
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setAttachmentHash($hash);

    /**
     * Get from date
     *
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set from date
     *
     * @param string $fromDate
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setFromDate($fromDate);

    /**
     * Get to date
     *
     * @return string|null
     */
    public function getToDate();

    /**
     * Set to date
     *
     * @param string $toDate
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setToDate($toDate);

    /**
     * Returns rule condition
     *
     * @return \Magezon\CustomerAttachments\Api\Data\ConditionInterface|null
     */
    public function getRuleCondition();

    /**
     * @param \Magezon\CustomerAttachments\Api\Data\ConditionInterface $condition
     * @return $this
     */
    public function setRuleCondition($condition);

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setIsActive($isActive);

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     */
    public function setUpdateTime($updateTime);
}
