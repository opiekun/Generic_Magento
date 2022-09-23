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

class Paginitiontype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 'np', 'label' => __('No pagination')],
        ['value' => 'plr', 'label' => __('pagination with Last Rows Option')],
        ['value' => 'pbtoon', 'label' => __('pagination with Button option')],
        ['value' => 'pdot', 'label' => __('pagination with Dots')],
        ['value' => 'pnum', 'label' => __('pagination with Numbers')],
        ['value' => 'prect', 'label' => __('pagination with Rectangle')] ];
    }
}
