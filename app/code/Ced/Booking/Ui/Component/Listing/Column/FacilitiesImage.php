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
 * @category  Ced
 * @package   Ced_booking
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license   https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Booking\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Ced\Booking\Helper\Data;

/**
 * Class FacilitiesImage
 * @package Ced\Booking\Ui\Component\Listing\Column
 */
class FacilitiesImage extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'image_value';

    const ALT_FIELD = 'image_value';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Data $bookingHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->bookingHelper = $bookingHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['image_type'] == 'image') {
                    $item[$fieldName] = '<img width="50px" heigh="50px" src="'.$this->bookingHelper->getImageUrl($item['image_value']).'" alt="'.$item['image_value'].'">';
                } elseif ($item['image_type'] == 'icon')
                {
                    $item[$fieldName] = '<i class="fa-'.Data::FONT_ICON_SIZE.' '.$item['image_value'].'"></i>';
                }
            }
        }

        return $dataSource;
    }

}
