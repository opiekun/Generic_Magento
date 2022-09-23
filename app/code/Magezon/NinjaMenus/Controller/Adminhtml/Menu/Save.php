<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Controller\Adminhtml\Menu;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_NinjaMenus::menu_save';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magezon\NinjaMenus\Helper\Form
     */
    protected $menuHelper;

    /**
     * @param \Magento\Backend\App\Action\Context                   $context       
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor 
     * @param \Magento\Framework\App\Cache\TypeListInterface        $cacheTypeList 
     * @param \Magezon\NinjaMenus\Helper\Menu                       $menuHelper    
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magezon\NinjaMenus\Helper\Menu $menuHelper
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->cacheTypeList = $cacheTypeList;
        $this->menuHelper    = $menuHelper;
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $redirectBack = $this->getRequest()->getParam('back', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (empty($data['menu_id'])) {
            unset($data['menu_id']);
        }
        if ($data) {

            /** @var \Magezon\NinjaMenus\Model\Menu $model */
            $model = $this->_objectManager->create(\Magezon\NinjaMenus\Model\Menu::class);
            $id    = $this->getRequest()->getParam('menu_id');

            try {
                $model->load($id);
                if ($id && !$model->getId()) {
                    throw new LocalizedException(__('This menu no longer exists.'));
                }
                $model->setData($data);
                $model->save();

                $this->messageManager->addSuccessMessage(__('You saved the menu.'));
                $this->dataPersistor->clear('current_menu');

                $this->_eventManager->dispatch(
                    'ninjamenus_menu_after_save',
                    [
                        'menu'    => $model,
                        'request' => $this->getRequest()
                    ]
                );

                if ($redirectBack === 'save_and_new') {
                    $this->cleanCache();
                    return $resultRedirect->setPath('*/*/new');
                }

                if ($redirectBack === 'save_and_duplicate') {
                    $duplicate = $this->menuHelper->duplicateMenu($model);
                    $this->messageManager->addSuccessMessage(__('You duplicated the menu'));
                    $this->cleanCache();
                    return $resultRedirect->setPath('*/*/edit', ['menu_id' => $duplicate->getId(), '_current' => true]);
                }

                if ($redirectBack === 'save_and_close') {
                    $this->cleanCache();
                    return $resultRedirect->setPath('*/*/*');
                }

                $this->cleanCache();
                return $resultRedirect->setPath('*/*/edit', ['menu_id' => $model->getId(), '_current' => true]);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?:$e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the menu.'));
            }

            $this->dataPersistor->set('current_menu', $data);
            return $resultRedirect->setPath('*/*/edit', ['menu_id' => $this->getRequest()->getParam('menu_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function cleanCache()
    {
        $types = ['full_page', 'block_html'];

        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
    }
}
