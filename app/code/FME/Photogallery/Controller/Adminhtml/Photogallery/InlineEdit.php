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
use FME\Photogallery\Model\Photogallery as Photogallery;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    protected $dataProcessor;
    protected $photogallery;
    protected $jsonFactory;
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Photogallery $photogallery,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->photogallery = $photogallery;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
                ]
            );
        }
        foreach (array_keys($postItems) as $photogalleryId) {
           $photogallery = $this->photogallery->load($photogalleryId);
            try {
                $photogalleryData = $this->dataProcessor->filter($postItems[$photogalleryId]);
                $photogallery->setData($photogalleryData);
                $photogallery->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithPhotogalleryId($photogallery, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithPhotogalleryId($photogallery, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPhotogalleryId(
                    $photogallery,
                    __('Something went wrong while saving the photogallery.')
                );
                $error = true;
            }
        }
        return $resultJson->setData(
            [
            'messages' => $messages,
            'error' => $error
            ]
        );
    }
    protected function getErrorWithPhotogalleryId(Photogallery $photogallery, $errorText)
    {
        return '[Photogallery ID: ' . $photogallery->getPhotogalleryId() . '] ' . $errorText;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
