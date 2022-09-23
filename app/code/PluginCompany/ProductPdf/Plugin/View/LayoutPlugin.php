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
namespace PluginCompany\ProductPdf\Plugin\View;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;

class LayoutPlugin
{
    /**
     * @var Http
     */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    public function afterIsCacheable(\Magento\Framework\View\Layout $subject, $result)
    {
        $action = $this->request->getFullActionName();
        if($action == 'productpdf_download_file') return false;
        return $result;
    }
}
