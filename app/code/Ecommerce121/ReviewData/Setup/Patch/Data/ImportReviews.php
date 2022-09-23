<?php

declare(strict_types=1);

namespace Ecommerce121\ReviewData\Setup\Patch\Data;

use Ecommerce121\ReviewData\Model\ResourceModel\ProductDataProvider;
use Ecommerce121\ReviewData\Model\ResourceModel\RatingDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Csv;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ImportReviews implements DataPatchInterface
{
    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var Reader
     */
    private $dirReader;

    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RatingDataProvider
     */
    private $ratingDataProvider;

    /**=
     * @var RatingFactory
     */
    private $ratingFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ProductDataProvider $productDataProvider
     * @param RatingDataProvider $ratingDataProvider
     * @param RatingFactory $ratingFactory
     * @param ReviewFactory $reviewFactory
     * @param Csv $csv
     * @param Reader $dirReader
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductDataProvider $productDataProvider,
        RatingDataProvider $ratingDataProvider,
        RatingFactory $ratingFactory,
        ReviewFactory $reviewFactory,
        Csv $csv,
        Reader $dirReader,
        LoggerInterface $logger
    ) {
        $this->csv = $csv;
        $this->dirReader = $dirReader;
        $this->productDataProvider = $productDataProvider;
        $this->ratingDataProvider = $ratingDataProvider;
        $this->logger = $logger;
        $this->ratingFactory = $ratingFactory;
        $this->reviewFactory = $reviewFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @return ImportReviews
     * @throws NoSuchEntityException
     */
    public function apply(): ImportReviews
    {
        $reviews = $this->getReviewData();
        foreach ($reviews as $reviewData) {
            $productId = $this->productDataProvider->getProductIdBySku($reviewData['product_sku']);
            if (!$productId) {
                $this->logger->info($reviewData['product_sku']);
                continue;
            }

            $review = $this->reviewFactory->create();
            $review->setData([
                'title' => $reviewData['title'],
                'detail' => $reviewData['content'],
                'nickname' => 'Anonymous',
            ]);
            $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                ->setEntityPkValue($productId)
                ->setStatusId(Review::STATUS_APPROVED)
                ->setStoreId($this->storeManager->getStore()->getId())
                ->setStores([$this->storeManager->getStore()->getId()])
                ->save();

            $ratingOptions = $this->ratingDataProvider->getRatingOptions((int)$reviewData['rating']);
            foreach ($ratingOptions as $ratingId => $optionId) {
                $this->ratingFactory->create()
                    ->setRatingId($ratingId)
                    ->setReviewId($review->getId())
                    ->addOptionVote($optionId, $productId);
            }

            $date = (new \DateTime())->createFromFormat('m/d/Y H:i', $reviewData['date']);
            $review->setCreatedAt($date->format('Y-m-d H:i:s'))->save();

            $review->aggregate();
        }

        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getReviewData(): array
    {
        $this->csv->setDelimiter(';');
        $filePath = $this->dirReader->getModuleDir('Setup', 'Ecommerce121_ReviewData')
            . '/Patch/Fixtures/reviews.csv';
        $rows = $this->csv->getData($filePath);
        $keys = array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            $review = array_combine($keys, $row);
            $data[] = $review;
        }

        return $data;
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
        return [
            ActiveQualityRatingAttribute::class,
        ];
    }
}
