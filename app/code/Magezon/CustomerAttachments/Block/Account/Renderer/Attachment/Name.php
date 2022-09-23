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

class Name extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
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
        $html = '<p class="mca-attachment_name">' . $row->getName() . '</p>';
        $html .= '<p class="mca-attachment_description">' . $row->getDescription() . '</p>';
        return $html;
	}

    /**
     * Replace placeholders in the string with values
     *
     * @param DataObject $row
     * @return string
     */
    private function getFormattedValue(\Magento\Framework\DataObject $row)
    {
        $value = $this->getColumn()->getFormat() ?: null;
        if (true === $this->getColumn()->getTranslate()) {
            $value = __($value);
        }
        if (preg_match_all($this->_variablePattern, $value, $matches)) {
            foreach ($matches[0] as $index => $match) {
                $replacement = $row->getData($matches[1][$index]);
                $value = str_replace($match, $replacement, $value);
            }
        }
        return $this->escapeHtml($value);
    }
}
