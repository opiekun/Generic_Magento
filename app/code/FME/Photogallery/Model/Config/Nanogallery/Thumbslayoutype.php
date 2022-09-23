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

class Thumbslayoutype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 'wfl', 'label' => __('Water Fall layout')],
        ['value' => 'jl', 'label' => __('Simple Justified Layout')],
        ['value' => 'bml', 'label' => __('Borken Mirror Layout')],
        ['value' => 'il', 'label' => __('Instagram Layout')],
        ['value' => 'fc', 'label' => __('Full Content')],
        ['value' => 'cwmb', 'label' => __('Content with More Button')],
        ['value' => 'pbn', 'label' => __('Pagination By Number')],
        ['value' => 'pbd', 'label' => __('Pagination By Dots')],
        ['value' => 'pbr', 'label' => __('Gallery on 2 Rows')],
        ['value' => 'cl', 'label' => __('Custom Layout')] ];
    }
}
