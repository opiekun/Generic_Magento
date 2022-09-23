<?php

declare(strict_types=1);

namespace Ecommerce121\ReviewData\Model\ResourceModel;

use Magento\Review\Model\ResourceModel\Rating\CollectionFactory;

class RatingDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $ratingOptions;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $code
     * @return array
     */
    public function getRatingOptions(int $code): array
    {
        if ($this->ratingOptions === null) {
            $this->ratingOptions = $this->collectionFactory->create()->getItems();
        }

        $ratingList = [];
        foreach ($this->ratingOptions as $rating) {
            foreach ($rating->getOptions() as $option) {
                if ($option->getCode() == $code) {
                    $ratingList[$option->getRatingId()] = $option->getOptionId();
                }
            }
        }

        return $ratingList;
    }
}
