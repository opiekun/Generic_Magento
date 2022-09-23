<?php
/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
namespace PluginCompany\ProductPdf\Block\Pdf\Content;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use PluginCompany\ProductPdf\Block\Pdf\Content;

class Media extends Content
{
    protected $_template = 'PluginCompany_ProductPdf::pdf/content/media/wrapper.phtml';

    /**
     * @var Collection
     */
    private $mediaGalleryImages;

    private $mediaGalleryRangeCollections = [];

    public function getGalleryImagesHtml()
    {
        $template = $this->getConfig('gallery_style');
        if(!$this->getImageCount()){
            $template = '1-col-grid';
        }
        return $this->setTemplate(
            'PluginCompany_ProductPdf::pdf/content/media/' . $template . '.phtml'
        )->toHtml();
    }

    public function getMediaGalleryImagesWithMainImage()
    {
        $collection = $this->getDataCollectionFactory()->create();
        if($this->getMainImage()){
            $collection->addItem($this->getMainImage());
        }
        foreach($this->getMediaGalleryImages() as $image){
            $collection->addItem($image);
        }
        return $collection;
    }

    public function getMediaGalleryImages()
    {
        if(!$this->mediaGalleryImages)
        {
            $this->initMediaGalleryImages();
        }
        return $this->mediaGalleryImages;
    }

    private function initMediaGalleryImages()
    {
        if(!$this->getMaxAllowedGalleryImages()) {
            $this->mediaGalleryImages = $this->getDataCollectionFactory()->create();
            return $this;
        }
        $images =  $this->getProduct()->getMediaGalleryImages();
        $mediaGallery = $this->getDataCollectionFactory()->create();
        foreach($images as $image){
            if(stristr($image->getFile(), 'swatch_')){
               continue;
            }
            if($this->isMainProductImage($image->getFile())){
                continue;
            }
            $this->replaceImageWithPlaceholderIfNotExists($image);
            $mediaGallery->addItem($image);
        }
        $this->mediaGalleryImages = $mediaGallery;
        if($this->getConfig('add_child_images')){
            $this->addConfigurableChildImages();
        }
        if($this->getConfig('add_parent_images')){
            $this->addConfigurableParentImages();
        }
        $this->mediaGalleryImages = new \LimitIterator($this->mediaGalleryImages->getIterator(),0, $this->getMaxAllowedGalleryImages());
        return $this;
    }

    private function getMaxAllowedGalleryImages()
    {
        return $this->getConfig('gallery_image_count');
    }

    private function isMainProductImage($imagePath)
    {
        return $imagePath == $this->getProduct()->getImage();
    }

    private function replaceImageWithPlaceholderIfNotExists(DataObject $image)
    {
        if(!file_exists($this->getFullImagePath($image->getFile()))) {
            $image->setUrl($this->getPlaceHolderUrl());
            $image->setFile(false);
        }
        return $image;

    }

    private function addConfigurableChildImages()
    {
        if(!$this->isProductConfigurable())
            return $this;

        foreach($this->getConfigurableProductChildren() as $child) {
            $this->addProductImagesToCurrentMediaGallery($child);
        }
        return $this;
    }

    private function addConfigurableParentImages()
    {
        if(!$this->productHasParent())
            return $this;

        $this->addProductImagesToCurrentMediaGallery(
            $this->getParentProduct()
        );
        return $this;
    }

    private function addProductImagesToCurrentMediaGallery($product)
    {
        $this
            ->addMainImageToCurrentGallery($product)
            ->addGalleryImagesToCurrentGallery($product)
        ;
        return $this;
    }

    private function addMainImageToCurrentGallery($product)
    {
        if($this->getMainImage($product)) {
            $this->addImageToGalleryIfNotDuplicate(
                $this->getMainImage($product)
            );
        }
        return $this;
    }

    private function addGalleryImagesToCurrentGallery(Product $product)
    {
        if(!$product->getMediaGalleryImages()){
            return $this;
        }
        foreach($product->getMediaGalleryImages() as $image) {
            $this->replaceImageWithPlaceholderIfNotExists($image);
            $this->addImageToGalleryIfNotDuplicate($image);
        }
        return $this;
    }

    private function addImageToGalleryIfNotDuplicate(DataObject $image)
    {
        if(!$this->getConfig('filter_duplicate_images')){
            return $this->addImageToMediaGallery($image);
        }
        if($this->isImageAlreadyAddedToGallery($image->getFile())){
            return $this;
        }
        if($this->isMainProductImage($image->getFile())){
            return $this;
        }
        return $this->addImageToMediaGallery($image);
    }

    private function isImageAlreadyAddedToGallery($imagePath)
    {
        if(!is_null($this->mediaGalleryImages->getItemByColumnValue('file', $imagePath))){
            return true;
        }
        $path1 = $this->getFullImagePath($imagePath);
        $mainImagePath = $this->getFullImagePath($this->getMainImage()->getFile());
        if($this->areImagesTheSame($path1, $mainImagePath)){
            return true;
        }
        foreach($this->mediaGalleryImages as $image) {
            $path2 = $this->getFullImagePath($image->getFile());
            if($this->areImagesTheSame($path1, $path2)){
                return true;
            }
        }
        return false;
    }

    private function getFullImagePath($imagePath)
    {
        $path = $this->getProductMediaBaseDir() . $this->addDsToPath($imagePath);
        return $path;
    }

    private function getProductMediaBaseDir()
    {
        return $this->getDirectoryList()->getPath('media')
            . DIRECTORY_SEPARATOR . 'catalog'
            . DIRECTORY_SEPARATOR . 'product'
            ;
    }

    private function areImagesTheSame($a, $b)
    {
        if(!file_exists($a) || !file_exists($b)) {
            return false;
        }
        $result = $this->getImageCompare()
            ->compare($a, $b);
        return $result < 3;
    }

    private function addImageToMediaGallery($image)
    {
        $this->mediaGalleryImages->addItem(
            new DataObject(
                [
                    'file' => $image->getFile(),
                    'url'  => $image->getUrl()
                ]
            )
        ) ;
        return $this;
    }

    public function getMainImage($product = false)
    {
        if(!$product){
            $product = $this->getProduct();
        }

        if(!$product->getImage() || $product->getImage() == 'no_selection'){
            return false;
        }

        $url = $this->getPlaceHolderUrl();
        if(file_exists($this->getFullImagePath($product->getImage()))) {
            $url = $this->getFullProductImageUrl($product->getImage());
        }

        return new DataObject(['url' => $url, 'file' => $product->getImage()]);
    }

    public function getImageCount()
    {
        $count = $this->getMediaGalleryImages()->count();
        if($count > $this->getMaxAllowedGalleryImages()) {
            return $this->getMaxAllowedGalleryImages();
        }
        return $count;
    }

    public function getMediaGalleryImagesSelection($start, $end)
    {
        if(!isset($this->mediaGalleryRangeCollections[$start . '-' . $end])){
            $this->initMediaGalleryImagesSelection($start, $end);
        }
        return $this->mediaGalleryRangeCollections[$start . '-' .$end];
    }

    private function initMediaGalleryImagesSelection($start, $end)
    {
        $collection = $this->getDataCollectionFactory()->create();
        $i = 1;
        foreach($this->getMediaGalleryImages() as $image)
        {
            if($i >= $start && $i <= $end)
            {
                $collection->addItem($image);
            }
            $i++;
        }
        $this->mediaGalleryRangeCollections[$start . '-' .$end] = $collection;
        return $this;
    }

    public function getSelectionImageCount($start, $end)
    {
        return $this->getMediaGalleryImagesSelection($start, $end)->count();
    }

    public function shouldAddPageBreakBefore()
    {
        if($this->isTitleFirstAndMediaGallerySecond()){
            return false;
        }
        if($this->isImageGalleryFirstSection()){
            return false;
        }
        return true;
    }

    public function isTitleFirstAndMediaGallerySecond()
    {
        $sections = $this->getSortedContentElementsKeys();
        return $sections[0] == 'title-price' && $sections[1] == 'image-gallery';
    }

    public function isImageGalleryFirstSection()
    {
        return $this->getSortedContentElementsKeys()[0] == 'image-gallery';
    }

    public function getConfig($field)
    {
        return $this->getGalleryConfig($field);
    }

    public function getConfigFlag($field)
    {
        return $this->getGalleryConfigFlag($field);
    }

    public function getMainImageUrl()
    {
        if($this->getMainImage()){
            return $this->getMainImage()->getUrl();
        }
        return $this->getPlaceHolderUrl();
    }

    private function getPlaceHolderUrl()
    {
        return $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/image.jpg');
    }

}