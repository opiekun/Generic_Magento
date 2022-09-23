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

class ResetButtonplus extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
                'label'      => __('Reset'),
                'on_click'   => sprintf("location.href = '%s';", "window.location.href"),
                'class'      => 'reset',
                'sort_order' => 10,
               ];
    }
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
