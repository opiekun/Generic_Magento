<?php

/**
 * Used in creating options for category config value selection
 *
 */
namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
    protected $_categoryHelper;
    protected $categoryFlatConfig;
    /**
     * @param \Magento\Framework\View\Element\Template\Context    $context
     * @param \Magento\Catalog\Helper\Category                    $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
    ) {
        $this->_categoryHelper    = $categoryHelper;
        $this->categoryFlatConfig = $categoryFlatState;
    }

    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $categories = $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }

    /**
     * Retrieve child store categories
     *
     * @param $category
     * @param null $arr
     * @return null|array
     */
    public function getChildCategories($category, $arr=null)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array)$category->getChildrenNodes();
        } else {
            $subcategories = $category->getChildren();
        }

        foreach ($subcategories as $subcategory) {
            $arr[$subcategory->getEntityId()] = $subcategory;
            $subcat = $this->getChildCategories($subcategory, $arr);

            foreach ($subcat as $cat) {
                $arr[$cat->getEntityId()] = $cat;
            }
        }

        return $arr;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $categories = $this->getStoreCategories();
        $options = [];

        foreach ($categories as $category) {
            $childCategories = $this->getChildCategories($category);

            $options[] = [
                'label' => $category->getName(),
                'value' => $category->getEntityId(),
            ];
            
            if ($childCategories) {
                foreach ($childCategories as $id=>$childX) {
                    $options[] = [
                        'label' => str_repeat("_", $childX->getLevel()-2) . $childX->getName(),
                        'value' => $childX->getEntityId(),
                    ];
                }
            }
        }

        return $options;
    }
}
