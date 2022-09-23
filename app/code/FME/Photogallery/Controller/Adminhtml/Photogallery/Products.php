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
namespace FME\Photogallery\Controller\Adminhtml\Photogallery;

class Products extends \FME\Photogallery\Controller\Adminhtml\Photogallery
{
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $this->_initProductPhotogallery();
        $resultLayout->getLayout()->getBlock('photogallery.edit.tab.products')
            ->setRelatedProducts($this->getRequest()->getPost('products_related', null));
        return $resultLayout;
    }
}
