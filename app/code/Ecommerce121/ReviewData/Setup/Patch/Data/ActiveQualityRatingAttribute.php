<?php

declare(strict_types=1);

namespace Ecommerce121\ReviewData\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Review\Model\ResourceModel\Rating as RatingResource;
use Magento\Review\Model\RatingFactory;
use Magento\Store\Model\StoreManagerInterface;

class ActiveQualityRatingAttribute implements DataPatchInterface
{
    /**
     * @var RatingResource
     */
    private $ratingResource;

    /**
     * @var RatingFactory
     */
    private $ratingFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param RatingFactory $ratingFactory
     * @param RatingResource $ratingResource
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RatingFactory $ratingFactory,
        RatingResource $ratingResource,
        StoreManagerInterface $storeManager
    ) {
        $this->ratingFactory = $ratingFactory;
        $this->ratingResource = $ratingResource;
        $this->storeManager = $storeManager;
    }

    /**
     * @return ActiveQualityRatingAttribute
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply(): ActiveQualityRatingAttribute
    {
        $rating = $this->ratingFactory->create();
        $this->ratingResource->load($rating, 'Value', 'rating_code');
        $rating->setStores([$this->storeManager->getStore()->getId()]);
        $this->ratingResource->save($rating);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
