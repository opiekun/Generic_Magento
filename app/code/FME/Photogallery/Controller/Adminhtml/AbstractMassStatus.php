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


namespace FME\Photogallery\Controller\Adminhtml;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;

class AbstractMassStatus extends \Magento\Backend\App\Action
{
    const ID_FIELD = '';
    const REDIRECT_URL = '*/*/';
    protected $collection = 'Magento\Framework\Model\Resource\Db\Collection\AbstractCollection';
    protected $model = 'Magento\Framework\Model\AbstractModel';
    protected $grid = 'media';
    protected $status = 0;
    protected $approved = 0;

    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');
        try {
            if (isset($excluded)) {
                if (is_array($excluded) && !empty($excluded)) {
                    $this->excludedSetStatus($excluded);
                } else {
                    $this->setStatusAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedSetStatus($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }

    protected function setStatusAll()
    {
        $collection = $this->_objectManager->get($this->collection);
        $this->setStatus($collection);
    }

    protected function excludedSetStatus(array $excluded)
    {

        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->setStatus($collection);
    }

    protected function selectedSetStatus(array $selected)
    {
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setStatus($collection);
    }
    
    protected function setStatus(AbstractCollection $collection)
    {
        foreach ($collection->getAllIds() as $id) {

            $model = $this->_objectManager->get($this->model);
            $model->load($id);
            if ($this->grid=='blocks') {
                $model->setBlockStatus($this->status);
            } else {
                $model->setStatus($this->status);
            }
                
            $model->save();
        }
        if ($this->approved !== 0) {
            foreach ($collection->getAllIds() as $id) {
                $model = $this->_objectManager->get($this->model);
                $model->load($id);
                $model->setApproved($this->approved);
                $model->save();
            }
        }
        $this->setSuccessMessage(count($collection));
    }

    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been Updated.', $count));
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
