<?php
namespace WeltPixel\Backend\Plugin\Category;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Request\Http as HttpRequest;

class DataProvider
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * DataProvider constructor.
     * @param Config $eavConfig
     * @param HttpRequest $request
     */
    public function __construct(
        Config $eavConfig,
        HttpRequest $request
    ) {
        $this->eavConfig = $eavConfig;
        $this->request = $request;
    }

    /**
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterPrepareMeta(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {
        $fullActionName = $this->request->getFullActionName();
        if ($fullActionName == 'catalog_category_edit') {
            $result = array_merge_recursive($result, $this->_prepareFieldsMeta(
                $result,
                $this->_getFieldsMap(),
                $subject->getAttributesMeta($this->eavConfig->getEntityType('catalog_category'))
            ));
        }

        return $result;
    }

    /**
     * Prepare fields meta based on xml declaration of form and fields metadata
     *
     * @param array $originalResult
     * @param array $fieldsMap
     * @param array $fieldsMeta
     * @return array
     */
    protected function _prepareFieldsMeta($originalResult, $fieldsMap, $fieldsMeta)
    {
        $result = [];
        foreach ($fieldsMap as $fieldSet => $fields) {
            foreach ($fields as $field) {
                if (isset($fieldsMeta[$field]) && (!isset($originalResult[$fieldSet]['children'][$field])))  {
                    $result[$fieldSet]['children'][$field]['arguments']['data']['config'] = $fieldsMeta[$field];
                }
            }
        }
        return $result;
    }

    /**
     * Rewrite this in all subclassess, provide the list with category attributes
     * @return array
     */
    protected function _getFieldsMap()
    {
        return [];
    }
}
