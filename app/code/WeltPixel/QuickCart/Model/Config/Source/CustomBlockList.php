<?php


namespace WeltPixel\QuickCart\Model\Config\Source;


class CustomBlockList extends \Magento\Framework\View\Element\Template
{
    protected $blockRepository;
    protected $searchCriteriaBuilder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
    }

    public function toOptionArray()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $cmsBlocks = $this->blockRepository->getList($searchCriteria)->getItems();

        $result = [];
        foreach($cmsBlocks as $cmsBlock) {
            $result[] = [
                'value' => $cmsBlock->getIdentifier(),
                'label' => $cmsBlock->getTitle()
            ];
        }

        return $result;
    }
}

