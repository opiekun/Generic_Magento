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
namespace FME\Photogallery\Model\Config\Caption;

class Captionanimation implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 'caption-none', 'label' => __('None')],
        ['value' => 'caption-fixed', 'label' => __('Fixed, always visible')],
        ['value' => 'caption-fixed-bg', 'label' => __('Fixed with background')],
        ['value' => 'caption-fixed-then-hidden', 'label' => __('Fixed with background, hide on mouse hover')],
        ['value' => 'caption-fixed-bottom', 'label' => __('Fixed at bottom with gradient background')],
        ['value' => 'caption-slide-from-top', 'label' => __('Slide from top')],
        ['value' => 'caption-slide-from-bottom', 'label' => __('Slide from bottom')] ];
    }
}
