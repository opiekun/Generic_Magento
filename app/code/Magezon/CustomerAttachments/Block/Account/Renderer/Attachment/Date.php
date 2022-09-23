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

namespace Magezon\CustomerAttachments\Block\Account\Renderer\Attachment;

use Magento\Framework\UrlInterface;

class Date extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	protected $_localeDate;

	/**
	 * @param \Magento\Backend\Block\Context                       $context    
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate 
	 */
	public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        parent::__construct($context);
		$this->_localeDate = $localeDate;
	}

	/**
	 * @param  \Magento\Framework\DataObject $row
	 * @return string
	 */
	public function _getValue(\Magento\Framework\DataObject $row)
	{
		$date = $row->getCreationTime();
		return $this->formatDate($date);
	}

    /**
     * Retrieve formatting date
     *
     * @param null|string|\DateTimeInterface $date
     * @param int $format
     * @param bool $showTime
     * @param null|string $timezone
     * @return string
     */
    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }
}
