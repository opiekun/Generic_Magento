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
namespace FME\Photogallery\Model\Config\Nanogallery;

class Thumbstrans implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 'slideUp', 'label' => __('Slide Up')],
        ['value' => 'slideDown', 'label' => __('Slide Down')],
        ['value' => 'scaleUp', 'label' => __('Scale Up')],
        ['value' => 'scaleDown', 'label' => __('scaleDown')],
        ['value' => 'fadeIn', 'label' => __('fadeIn')],
        ['value' => 'randomScale', 'label' => __('randomScale')],
        ['value' => 'flipDown', 'label' => __('flipDown')],
        ['value' => 'flipUp', 'label' => __('flipUp')],
        ['value' => 'slideDown2', 'label' => __('slideDown2')],
        ['value' => 'slideUp2', 'label' => __('slideUp2')],
        ['value' => 'slideRight', 'label' => __('slideRight')],
        ['value' => 'slideLeft', 'label' => __('slideLeft')] ];
    }
}
