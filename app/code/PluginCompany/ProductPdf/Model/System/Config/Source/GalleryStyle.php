<?php
/**
 *
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
 *
 */
namespace PluginCompany\ProductPdf\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class GalleryStyle extends AbstractOption implements ArrayInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'medium-main-1c-side-2c-bottom' => 'Medium main image, 1 column side, 2 colum below',
            'medium-main-2c-side-3c-bottom' => 'Medium main image, 2 column side, 3 colum below',
            '1-col-grid' => 'Single column large images',
            '2-col-grid' => 'Two column grid',
            '3-col-grid' => 'Three column grid',
            'large-main-image-2-col-grid' => 'Large main image, 2 column grid below',
            'large-main-image-3-col-grid' => 'Large main image, 3 column grid below'
       );
    }
}