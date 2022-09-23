<?php

declare(strict_types=1);

namespace Ecommerce121\RemoveProductAttribute\Model;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

/**
 * Class Remover.
 */
class Remover
{
    /**
     * @var AttributeRepositoryInterface
     */
    private AttributeRepositoryInterface $attributeRepository;

    /**
     * Constructor.
     *
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(AttributeRepositoryInterface $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param string $attributeCode
     * @return bool
     */
    public function remove(string $attributeCode) : bool
    {
        try {
            $attributeData = $this->attributeRepository->get(Product::ENTITY, $attributeCode);
            $this->attributeRepository->delete($attributeData);

            return true;
        } catch (NoSuchEntityException | StateException $e) {
            return false;
        }
    }
}
