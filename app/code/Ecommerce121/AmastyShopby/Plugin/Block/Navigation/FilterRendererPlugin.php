<?php

namespace Ecommerce121\AmastyShopby\Plugin\Block\Navigation;

use Amasty\Shopby\Block\Navigation\Widget\SearchForm;
use Amasty\Shopby\Helper\FilterSetting;
use Amasty\Shopby\Model\Layer\Filter\Category;

class FilterRendererPlugin
{
    /**
     * @var  FilterSetting
     */
    protected $settingHelper;


    public function __construct(
        FilterSetting $settingHelper
    ) {
        $this->settingHelper = $settingHelper;
    }

    /**
     * @return string
     */
    public function aroundGetSearchForm($subject, $result) {
        if ($subject->getFilter() instanceof Category) {
            $filterLabel = 'Category';
        } else {
            $filterLabel = $subject->getFilter()->getAttributeModel()->getStoreLabel();
        }

        return $subject->getLayout()->createBlock(
            SearchForm::class
        )
            ->assign('filterCode', $subject->getFilterSetting()->getFilterCode())
            ->assign('filterLabel', $filterLabel)
            ->setFilter($subject->getFilter())
            ->toHtml();
    }
}
