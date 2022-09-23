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
namespace FME\Photogallery\Model\Config\Source;

class Galpositions implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $gals[0] = [
                    'value' => 1,
                    'label' => __('Photogallery Page only'),
                    ];
        $gals[1] = [
                    'value' => 2,
                    'label' => __('Product Page Only'),
                    ];
        $gals[2] = [
                    'value' => 3,
                    'label' => __('Both in Product and Photogallery pages'),
                    ];
        return $gals;
    }//end toOptionArray()
}//end class
