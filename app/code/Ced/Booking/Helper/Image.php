<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Image
 * @package Ced\Booking\Helper
 */
class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Image constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $StoreManager
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $StoreManager,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        parent::__construct($context);
        $this->_imageFactory = $imageFactory;
        $this->_filesystem = $filesystem;
        $this->_storeManager = $StoreManager;
        $this->_assetRepo = $assetRepo;


    }

    public function resize($image, $width = null, $height = null,$placeholder)
    {
        $this->_mediaDirectory = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        if ($image) {
            $absolutePath = $this->_mediaDirectory->getAbsolutePath('booking/store/banner/') . $image;
            $finalPathToWrite = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                    ->getAbsolutePath('booking/store/banner/resized/'.$width.'x'.$height.'/').$image;
        } else {
            $image = 'Ced_Booking::images/banner.jpg';
            return $this->getViewFileUrl($image);
        }

        if (isset($finalPathToWrite)) {
            $imageFactory = $this->_imageFactory->create();
            $imageFactory->open($absolutePath);
            $imageFactory->quality(100);
            $imageFactory->resize($width, $height);
            $imageFactory->save($finalPathToWrite);
        }
        return $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .'booking/store/banner/resized/'.$width.'x'.$height.'/'. $image;
    }

    public function getViewFileUrl($fileId)
    {
        try {
            $params = ['_secure' => $this->_request->isSecure()];
            return $this->_assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
            return False;
        }
    }
}
