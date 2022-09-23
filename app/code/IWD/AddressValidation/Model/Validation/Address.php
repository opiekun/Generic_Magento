<?php

namespace IWD\AddressValidation\Model\Validation;

use Magento\Framework\DataObject;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Address
 *
 * @method string getStreet()
 * @method string getCity()
 * @method string getPostcode()
 * @method string getCountryId()
 * @method string getPostalTown()
 * @method string getStreetNumber()
 *
 * @method Address setCity($city)
 * @method Address setRegionId($regionId)
 * @method Address setRegion($region)
 * @method Address setPostcode($postcode)
 * @method Address setCountryId($countryId)
 * @method Address setPostalTown($postalTown)
 * @method Address setStreetNumber($streetNumber)
 */
class Address extends DataObject
{
    /**
     * @var CollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * Address constructor.
     * @param CollectionFactory $regCollectionFactory
     * @param CountryFactory $countryFactory
     * @param array $data
     */
    public function __construct(
        CollectionFactory $regCollectionFactory,
        CountryFactory $countryFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->regionCollectionFactory = $regCollectionFactory;
        $this->countryFactory = $countryFactory;
    }

    /**
     * @return mixed|string
     */
    public function getRegionId()
    {
        $regionId = $this->getData('region_id', null);
        if (!empty($regionId)) {
            return $regionId;
        }

        $regionCode = $this->getData('region_code', null);
        $countryId = $this->getData('country_id', null);
        if (!empty($regionCode) && !empty($countryId)) {
            $regionId = $this->countryFactory->create()->loadByCode($countryId)
                ->getRegionCollection()
                ->addFieldToFilter('code', $regionCode)
                ->getFirstItem()
                ->getId();
            $this->setData('region_id', $regionId);
            return $regionId;
        }

        return '';
    }

    /**
     * @return mixed|string
     */
    public function getRegion()
    {
        $region = $this->getData('region', null);
        if (!empty($region)) {
            return $region;
        }

        $regionId = $this->getData('region_id', null);
        if (!empty($regionId)) {
            $region = $this->loadRegion($regionId)->getDefaultName();
            $this->setData('region', $region);
            return $region;
        }

        $regionCode = $this->getData('region_code', null);
        $countryId = $this->getData('country_id', null);
        if (!empty($regionCode) && !empty($countryId)) {
            $region = $this->countryFactory->create()->loadByCode($countryId)
                ->getRegionCollection()
                ->addFieldToFilter('code', $regionCode)
                ->getFirstItem()
                ->getDefaultName();
            $this->setData('region', $region);
            return $region;
        }

        return '';
    }

    /**
     * @return mixed|null
     */
    public function getRegionCode()
    {

        $regionId = $this->getData('region_id', null);
        if (!empty($regionId)) {
            $regionCode = $this->loadRegion($regionId)->getCode();
            $this->setData('region_code', $regionCode);
            return $regionCode;
        }

        return null;
    }

    /**
     * @param $regionId
     * @return \Magento\Directory\Model\Region
     */
    private function loadRegion($regionId)
    {
        $regionsCollection = $this->regionCollectionFactory->create();
        /** @var $region \Magento\Directory\Model\Region */
        $region = $regionsCollection->addFieldToFilter('main_table.region_id', $regionId)->getFirstItem();
        return $region;
    }

    /**
     * @return string
     */
    public function getZip4()
    {
        return $this->getZipPart(1);
    }

    /**
     * @return string
     */
    public function getZip5()
    {
        return $this->getZipPart(0);
    }

    /**
     * @param $part
     * @return string
     */
    private function getZipPart($part)
    {
        $zip = $this->getPostcode();
        if (!empty($zip)) {
            $zip = str_replace(' ', '-', $zip);
            $zip = explode('-', $zip);
            if (isset($zip[$part]) && !empty($zip[$part])) {
                return $zip[$part];
            }
        }
        return '';
    }

    /**
     * @return mixed|string
     */
    public function getCountryName()
    {
        $countryName = $this->getData('country_name', null);
        if (!empty($countryName)) {
            return $countryName;
        }

        $countryId = $this->getData('country_id', null);
        if (!empty($countryId)) {
            $countryName = $this->countryFactory->create()->loadByCode($countryId)->getName();
            $this->setData('country_name', $countryName);
            return $countryName;
        }

        return '';
    }

    /**
     * @param $street
     * @return $this
     */
    public function setStreet($street)
    {
        if (is_array($street)) {
            $street = implode(' ', $street);
        }

        $street = strip_tags($street);
        $street = str_replace("\r\n", " ", $street);
        $street = str_replace("\n\r", " ", $street);
        $street = str_replace("\r", " ", $street);
        $street = str_replace("\n", " ", $street);
        $street = str_replace(",", "", $street);

        $this->setData('street', $street);
        return $this;
    }

    /**
     * @param $regionCode
     * @return $this
     */
    public function setRegionCode($regionCode)
    {
        $regionCode = strtoupper($regionCode);
        $this->setData('region_code', $regionCode);
        return $this;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function checkAddressData()
    {
        $map = ["street", "city", "country_id", "postcode"];
        $data = $this->toArray($map);

        foreach ($data as $field) {
            if (empty($field)) {
                throw new LocalizedException(__('Please enter all required address fields'));
            }
        }

        $region = $this->getRegion();
        return empty($region);
    }

    /**
     * @param Address $address
     * @return bool
     */
    public function isEqualWithAddress(Address $address)
    {
        $countyId_1   = trim(strtolower($this->getCountryId()));
        $postcode_1   = str_replace(' ', '', trim(strtolower($this->getPostcode()))); // Remove spaces for compare
        $city_1       = trim(strtolower($this->getCity()));
        $postal_1     =  trim(strtolower($this->getPostalTown()));
        $city_1_mod   = $postal_1 ? ($city_1 . ' ' . $postal_1) : null;
        $street_1     = trim(strtolower($this->getStreetNumber() . ' ' . $this->getStreet()));
        $regionCode_1 = trim(strtolower($this->getRegionCode()));
        $region_1     = trim(strtolower($this->getRegion()));

        $countyId_2   = trim(strtolower($address->getCountryId()));
        $postcode_2   = str_replace(' ', '', trim(strtolower($address->getPostcode()))); // Remove spaces for compare
        $city_2       = trim(strtolower($address->getCity()));
        $city_2_mod   = str_replace(array(', ', ','), ' ', $city_2);
        $street_2     = trim(strtolower($address->getStreet()));
        $regionCode_2 = trim(strtolower($address->getRegionCode()));
        $region_2     = trim(strtolower($address->getRegion()));

        if ($countyId_1 != $countyId_2 ||
            $street_1 != $street_2 ||
            !empty($postcode_1) && !empty($postcode_2) && $postcode_1 != $postcode_2
        ) {
            return false;
        }

        // Check if city match or city with postal town in some countries
        if ($city_1 != $city_2 && $city_1_mod != $city_2_mod) {
            return false;
        }
        if (!empty($regionCode_1) && !empty($regionCode_2)) {
            return strtolower($regionCode_1) == strtolower($regionCode_2);
        }

        if (!empty($regionCode_1) && !empty($regionCode_2)) {
            return strtolower($region_1) == strtolower($region_2);
        }

        return true;
    }

    /**
     * @param string $format
     * @return string
     */
    public function toString($format = '')
    {
        $row = [
            'street'   => trim($this->getStreet()),
            'city'     => trim($this->getCity()),
            'region'   => trim($this->getRegion()),
            'postcode' => trim($this->getPostcode()),
            'country'  => trim($this->getCountryName()),
        ];

        $address = implode(', ', array_filter($row));

        $class = $this->getClassDescription();
        if ($class) {
            $address .= ' (' .  $class . ')';
        }

        return $address;
    }

    /**
     * Update region data
     */
    public function updateRegionData()
    {
        $regionId = $this->getRegionId();
        if (!empty($regionId)) {
            $this->setRegion('')->getRegion();
        }
    }
}
