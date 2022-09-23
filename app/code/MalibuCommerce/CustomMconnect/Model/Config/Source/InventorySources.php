<?php

namespace MalibuCommerce\CustomMconnect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class InventorySources implements OptionSourceInterface
{
    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @param SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $sources = $this->sourceRepository->getList();
        $result = [];
        foreach ($sources->getItems() as $source) {
            $result[] = [
                'value' => $source->getSourceCode(),
                'label' => $source->getName(),
            ];
        }

        return $result;
    }
}
