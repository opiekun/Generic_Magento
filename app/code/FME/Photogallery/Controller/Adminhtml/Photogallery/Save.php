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

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use FME\Prodfaqs\Model\Faqs;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;
    protected $scopeConfig;
    protected $_escaper;
    protected $inlineTranslation;
    protected $_dateFactory;
    public function __construct(
        \Magento\Framework\App\ResourceConnection $coreresource,
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
    ) {

        $this->dataPersistor = $dataPersistor;
        $this->scopeConfig = $scopeConfig;
        $this->_escaper = $escaper;
        $this->_dateFactory = $dateFactory;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
        $this->_coreresource = $coreresource;
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('FME\Photogallery\Helper\Data');
        if ($data = $this->getRequest()->getPostValue()) {
            if (empty($data['photogallery_categories']))
            {
                $data['category_ids']=null;
            }
            $gallery = isset($data['gallery']) ? $data['gallery'] : [];
            $_photos_info = isset($gallery['images']) ? $gallery['images'] : [];
            $id = $this->getRequest()->getParam('photogallery_id');
            if (empty($data['photogallery_id'])) {
                $data['photogallery_id'] = null;
            }
            $model = $this->_objectManager->create('FME\Photogallery\Model\Photogallery')->load($id);
            if ($id) {
                $model->load($id);
            }
            if (!empty($data["photogallery_categories"])) {
                $arr = $data["photogallery_categories"];
                $str = implode(",", $arr);
                $data["category_ids"] = $str;
            }
            if (isset($data["category_products"])) {
                $cat_array = json_decode($data['category_products'], true);
                $pro_array = array_values($cat_array);
                $c=0;
                foreach ($cat_array as $key => $value) {
                    $pro_array[$c] = $key;
                    $c++;
                }
                unset($data['category_products']);
                $data['product_id'] = $pro_array;
            }
            $model->setData($data);
            if ($id) {
                $model->setId($id);
            }
            $this->inlineTranslation->suspend();
            try {
                if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                    $model->setCreatedTime(date('y-m-d h:i:s'))
                        ->setUpdateTime(date('y-m-d h:i:s'));
                } else {
                    $model->setUpdateTime(date('y-m-d h:i:s'));
                }
                $model->save();
                $_conn_read = $this->_coreresource->getConnection('core_read');
                $_conn = $this->_coreresource->getConnection('core_write');
                $photogallery_images_table = $this->_coreresource->getTableName('photogallery_images');
                if (!empty($_photos_info)) {
                    foreach ($_photos_info as $_photo_info) {
                        //Do update if we have gallery id (menaing photo is already saved)
                        if ($_photo_info['photogallery_id'] != null) {
                            $data = [
                                "img_name" => str_replace(".tmp", "", $_photo_info['file']),
                                "img_label" => $_photo_info['label'],
                                "tags" => $_photo_info['tags'],
                                "img_description" => $_photo_info['video_description'],
                                "photogallery_id" => $_photo_info['photogallery_id'],
                                "img_order" => $_photo_info['position'],
                                "disabled" => $_photo_info['disabled'],
                            ];

                            $where = ["img_id = " . (int) $_photo_info['value_id']];
                            $_conn->update($photogallery_images_table, $data, $where);

                            if (isset($_photo_info['removed']) and $_photo_info['removed'] == 1) {
                                $_conn->delete(
                                    $photogallery_images_table,
                                    'img_id = ' . (int) $_photo_info['value_id']
                                );
                            }
                        } else {
                                if (!(isset($_photo_info['removed']) and $_photo_info['removed'] == 1)) {
                                    $_conn->insert(
                                        $photogallery_images_table,
                                        [
                                        'img_name' => str_replace(".tmp", "", $_photo_info['file']),
                                        'img_label' => $_photo_info['label'],
                                        'tags' => $_photo_info['tags'],
                                        'img_description' => $_photo_info['video_description'],
                                        'photogallery_id' => $model->getId(),
                                        'img_order' => $_photo_info['position'],
                                        "disabled" => $_photo_info['disabled'],
                                        ]
                                    );
                                }   
                           
                        }
                    }
                }
                $this->messageManager->addSuccess(__('Photogallery was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                $this->dataPersistor->clear('photogallery');
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('Unable to find Photogallery to save'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Photogallery::manage_items');
    }
}