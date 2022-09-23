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
namespace FME\Photogallery\Model\Config\Zoom;

class Zoomspeed implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 'effect-speed-very-slow', 'label' => __('1s')],
        ['value' => 'effect-speed-slow', 'label' => __('0.5s')],
        ['value' => 'effect-speed-medium', 'label' => __('0.35s')],
        ['value' => 'effect-speed-fast', 'label' => __('0.2s')],
        ['value' => 'effect-speed-very-fast', 'label' => __('0.1s')] ];
    }
}
