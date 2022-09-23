<?php
namespace WeltPixel\Backend\Plugin\CategoryStaging;

class DataProvider
{
    /**
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterPrepareMeta(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {
        unset($result['weltpixel_options']);
        unset($result['weltpixel_megamenu']);
        return $result;
    }
}
