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

namespace Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Button;

class SaveAndApplyButton extends Generic
{
    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getButtonData()
    {
        if (!$this->_isAllowedAction('Magezon_CustomerAttachments::attachment_save')) {
            return [];
        }
        $data = [];
            $data = [
                'label'          => __('Save and Apply'),
                'class'          => 'save action-secondary',
                'on_click'       => '',
                'sort_order'     => 80,
                'data_attribute' => $this->getButtonAttribute([ true, ['save_and_apply' => 1]])
            ];
        return $data;
    }
}
