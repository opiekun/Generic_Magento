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
namespace PluginCompany\ProductPdf\Adapter\PdfGenerator;

use Magento\Framework\View\Element\AbstractBlock;

interface PdfGeneratorInterface
{
    /**
     * @param AbstractBlock $block
     * @return string
     */
    public function generate(AbstractBlock $block);

    /**
     * @param $value
     * @return $this
     */
    public function setDefaultFont($value);

    /**
     * @param $value
     * @return $this
     */
    public function setMarginLeft($value);

    /**
     * @param $value
     * @return $this
     */
    public function setMarginRight($value);

    /**
     * @param $value
     * @return $this
     */
    public function setMarginTop($value);

    /**
     * @param $value
     * @return $this
     */
    public function setMarginBottom($value);

    /**
     * @param $value
     * @return $this
     */
    public function setMarginHeader($value);

    /**
     * @param $value
     * @return $this
     */
    public function setMarginFooter($value);

    /**
     * @param $value
     * @return $this
     */
    public function setOrientation($value);

    /**
     * @param $value
     * @return $this
     */
    public function setFileName($value);

    /**
     * @param $value
     * @return $this
     */
    public function setHtmlFooter($value);

    /**
     * @param $js
     * @return $this
     */
    public function setJs($js);


}