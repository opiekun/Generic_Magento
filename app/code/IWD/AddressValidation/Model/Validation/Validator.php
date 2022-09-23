<?php

namespace IWD\AddressValidation\Model\Validation;

use IWD\AddressValidation\Helper\Data;
use IWD\AddressValidation\Model\Ups\Validation as UpsValidation;
use IWD\AddressValidation\Model\Usps\Validation as UspsValidation;
use IWD\AddressValidation\Model\Google\Validation as GoogleValidation;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Validator
 * @package IWD\AddressValidation\Model\Validation
 */
class Validator extends AbstractModel
{
    /**
     * @var UpsValidation
     */
    private $upsValidator;

    /**
     * @var UspsValidation
     */
    private $uspsValidator;

    /**
     * @var GoogleValidation
     */
    private $googleValidator;

    /**
     * @var mixed
     */
    private $mode;

    /**
     * Validator constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param UpsValidation $upsValidator
     * @param UspsValidation $uspsValidator
     * @param GoogleValidation $googleValidator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        UpsValidation $upsValidator,
        UspsValidation $uspsValidator,
        GoogleValidation $googleValidator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->googleValidator = $googleValidator;
        $this->upsValidator = $upsValidator;
        $this->uspsValidator = $uspsValidator;
        $this->mode = $helper->getValidationMode();
    }

    /**
     * @return mixed
     */
    public function getValidationMode()
    {
        return $this->mode;
    }

    /**
     * @param $mode
     */
    public function setValidationMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return GoogleValidation|UpsValidation|UspsValidation
     * @throws LocalizedException
     */
    public function getValidator()
    {
        $mode = $this->getValidationMode();

        switch ($mode) {
            case 'ups':
                return $this->upsValidator;
            case 'usps':
                return $this->uspsValidator;
            case 'google':
                return $this->googleValidator;
            default:
                throw new LocalizedException(__('Validation mode <' . $mode . '> is not supported'));
        }
    }
}
