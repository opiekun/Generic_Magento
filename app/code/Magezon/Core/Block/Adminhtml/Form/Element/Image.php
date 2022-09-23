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
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Block\Adminhtml\Form\Element;

class Image extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory           $factoryElement    
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection 
     * @param \Magento\Framework\Escaper                             $escaper           
     * @param \Magento\Backend\Model\UrlInterface                    $backendUrl        
     * @param \Magento\Store\Model\StoreManagerInterface             $_storeManager     
     * @param array                                                  $data              
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('text');
        $this->setExtType('text');
        $this->_backendUrl   = $backendUrl;
        $this->_storeManager = $_storeManager;
    }

    /**
     * Get the Html for the element.
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $htmlId = $this->getHtmlId();

        $html .= '<div class="mgz-form_image">';

        $beforeElementHtml = $this->getBeforeElementHtml();
        if ($beforeElementHtml) {
            $html .= '<label class="addbefore" for="' . $htmlId . '">' . $beforeElementHtml . '</label>';
        }

        $html .= '<input id="' . $htmlId . '" name="' . $this->getName() . '" ' . $this->_getUiId() . ' value="' .
            $this->getEscapedValue() . '" ' . $this->serialize($this->getHtmlAttributes()) . '/>';
        $visible = true;
        if ($this->getDisabled()) {
            $visible = false;
        }
        $html .= $this->_getButtonHtml(
            [
                'title' => __('Insert Image'),
                'onclick' => "MgzMediabrowserUtility.openDialog('" . $this->_backendUrl->getUrl('mgzcore/wysiwyg_images/index', ['target_element_id'=> $this->getName()]) . "', false, false, 'Insert Image', {closed: function() { jQuery('#mceModalBlocker').show()}})",
                'class' => 'action-add-image plugin',
                'style' => $visible ? '' : 'display:none',
            ]
        );

        $afterElementJs = $this->getAfterElementJs();
        if ($afterElementJs) {
            $html .= $afterElementJs;
        }

        $afterElementHtml = $this->getAfterElementHtml();
        if ($afterElementHtml) {
            $html .= '<label class="addafter" for="' . $htmlId . '">' . $afterElementHtml . '</label>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Return custom button HTML
     *
     * @param array $data Button params
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getButtonHtml($data)
    {
        $htmlId = $this->getHtmlId();
        $html = '<button type="button"';
        $html .= ' class="scalable ' . (isset($data['class']) ? $data['class'] : '') . '"';
        $html .= isset($data['onclick']) ? ' onclick="' . $data['onclick'] . '"' : '';
        $html .= isset($data['style']) ? ' style="' . $data['style'] . '"' : '';
        $html .= isset($data['id']) ? ' id="' . $data['id'] . '"' : '';
        $html .= '>';
        $html .= isset($data['title']) ? '<span><span><span>' . $data['title'] . '</span></span></span>' : '';
        $html .= '</button>';

        $imgSrc = $this->getValue();
        if ($imgSrc && !preg_match("/^http\:\/\/|https\:\/\//", $imgSrc)) {
            $imgSrc = $this->getMediaUrl() . $imgSrc;
        }

        $html .= '<div id="' . $htmlId . '-preview" class="image-preview" ' . ($imgSrc ? 'style="display: block"' : '') .'><a href="' . $imgSrc . '" id="' . $htmlId . '-preview_image"><img  src="' . $imgSrc . '"/></a></div>';

        $html .= '<script>
            require(["jquery", "Magezon_Core/js/mage/browser"], function($) {
                $("#' . $htmlId . '-preview_image").click(function(e) {
                    var win = window.open(\'\', \'preview\', \'width=500,height=500,resizable=1,scrollbars=1\');
                    win.document.open();
                    win.document.write(\'<body style="padding:0;margin:0"><img src="\'+$(this).find("img").eq(0).attr("src")+\'" id="image_preview"/></body>\');
                    win.document.close();
                    Event.observe(win, \'load\', function(){
                        var img = win.document.getElementById(\'image_preview\');
                        win.resizeTo(img.width+40, img.height+80)
                    });
                    return false;
                });
                $(document).on("keyup, change", "#' . $htmlId . '", function() {
                    var val = $(this).val();
                    if (val) {
                        $("#' . $htmlId . '-preview").show();
                        if (((val.indexOf("wysiwyg") !== -1) && (val.indexOf("http") === -1)) || ((val.indexOf("wysiwyg") === -1) && (val.indexOf("http") === -1))) {
                            $("#' . $htmlId . '-preview img").attr("src", "' . $this->getMediaUrl() . '" + val);
                            $("#' . $htmlId . '").attr("data-link", "' . $this->getMediaUrl() . '" + val);
                        } else {
                            $("#' . $htmlId . '-preview img").attr("src", val);
                            $("#' . $htmlId . '").attr("data-link", val);
                        }
                    } else {
                        $("#' . $htmlId . '-preview").hide();
                    }
                }).change();
            });
        </script>';

        return $html;
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        return $mediaUrl;
    }
}
