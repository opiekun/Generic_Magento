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

use FME\Photogallery\Controller\Adminhtml\AbstractMassStatus;

class MassEnable extends AbstractMassStatus
{
    const ID_FIELD = 'photogallery_id';
    protected $collection = 'FME\Photogallery\Model\ResourceModel\Photogallery\Collection';
    protected $model = 'FME\Photogallery\Model\Photogallery';
    protected $status = 1;
}
