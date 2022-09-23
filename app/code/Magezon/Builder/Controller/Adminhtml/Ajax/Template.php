<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Controller\Adminhtml\Ajax;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\ClientInterface;

class Template extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @param \Magento\Backend\App\Action\Context   $context    
     * @param \Magento\Framework\Filesystem         $filesystem 
     * @param ClientInterface                       $client     
     * @param \Magento\Framework\Filesystem\Io\File $file       
     * @param \Magezon\Core\Helper\Data             $coreHelper 
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        ClientInterface $client,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magezon\Core\Helper\Data $coreHelper
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->client     = $client;
        $this->file       = $file;
        $this->coreHelper = $coreHelper;
    }

    private function proccessImages($item, $result)
    {
        if (isset($result['images']) && is_array($result['images']) && isset($result['target']) && isset($result['mediaUrl'])) {
            $this->file->mkdir('wysiwyg/' . $result['target']);
            $mediaDir   = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $exportPath = $mediaDir->getAbsolutePath('wysiwyg/' . $result['target'] . '/' . $item['id'])  . '/';
            $this->file->mkdir($exportPath);
            $result['mediaUrl'] = str_replace('https://magezon.com', 'https://www.magezon.com', $result['mediaUrl']);
            foreach ($result['images'] as $image) {
                $path = $exportPath . $image;
                $this->client->get($result['mediaUrl'] . $image);
                $this->file->write($path, $this->client->getBody(), 0777);
            }
        }
    }

    public function execute()
    {
        $result = [];
        try {
            $post = $this->getRequest()->getPostValue();
            if (isset($post['item']) && $post['item']) {
                $item = $post['item'];
                $this->client->get($item['file']);
                $content = $this->client->getBody();
                if ($content) {
                    $result = $this->coreHelper->unserialize($content);
                    $this->proccessImages($item, $result);
                    if (!isset($result['elements']) && isset($result['profile']) && isset($result['profile']['elements'])) {
                        $result['elements'] = $result['profile']['elements'];
                        unset($result['profile']);
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
        } catch (\Exception $e) {
            $result['status']  = false;
            $result['message'] = __('Something went wrong while process preview template.');
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the template.'));
        }
        $this->getResponse()->setBody($this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result));
    }
}