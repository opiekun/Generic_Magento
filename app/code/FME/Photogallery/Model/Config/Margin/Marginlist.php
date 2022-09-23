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
namespace FME\Photogallery\Model\Config\Margin;

class Marginlist implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => '0', 'label' => __('0 px')],
        ['value' => '2', 'label' => __('2 px')],
        ['value' => '5', 'label' => __('5 px')],
        ['value' => '10', 'label' => __('10 px')],
        ['value' => '20', 'label' => __('20 px')],
        ['value' => '40', 'label' => __('40 px')] ];
    }
}
