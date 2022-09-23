<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Photogallery
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Photogallery\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [];
        if ($this->getBlockId()) {
            $data = [
                     'label'      => __('Delete Block'),
                     'class'      => 'delete',
                    'on_click'   => 'deleteConfirm(\''.__(
                        'Are you sure you want to do this?'
                    ).'\', \''.$this->getDeleteUrl().'\')',
                     'sort_order' => 20,
                    ];
        }

        return $data;
    }//end getButtonData()

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['photogallery_id' => $this->getBlockId()]);
    }//end getDeleteUrl()
}//end class
