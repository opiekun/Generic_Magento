<?php

namespace IWD\AddressValidation\Model\Google;

use Magento\Framework\Exception\LocalizedException;
use IWD\AddressValidation\Model\AbstractValidation;

/**
 * Class Validation
 * @package IWD\AddressValidation\Model\Google
 */
class Validation extends AbstractValidation
{

    const GOOGLE_VALIDATION_URL_FORMAT = '%s://maps.google.com/maps/api/geocode/json?address=%s&key=%s';

    /**
     * {@inheritdoc}
     */
    public function validateAddress()
    {
        $response = $this->apiRequest();
        return $this->getGoogleCandidates($response);
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    private function apiRequest()
    {
        $address = $this->getAddressAsUrlParam();
        $protocol = 'https';
        $key = $this->helper->getGoogleApiKey();

        if (empty($key)) {
            throw new LocalizedException(__("Empty Google API key."));
        }

        $url = sprintf(self::GOOGLE_VALIDATION_URL_FORMAT, $protocol, $address, $key);

        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $responseJson = @file_get_contents($url, false, stream_context_create($arrContextOptions));

        $result = json_decode($responseJson, true);

        if (!count($result['results'])) {
            // In some countries, regions are not correctly loaded from Google. Try again with omitting.
            $url = sprintf(self::GOOGLE_VALIDATION_URL_FORMAT, $protocol, $this->getAddressAsUrlParam(true), $key);

            $responseJson = @file_get_contents($url, false, stream_context_create($arrContextOptions));

            $result = json_decode($responseJson, true);

            if ($result && is_array($result) && isset($result['results']) && count($result['results'])) {
                $result['iwd_validated_without_region'] = true;
            }
        }

        return $result;
    }

    /**
     * @param bool $omitRegion Some countries require to omit region for API search
     * @return string
     */
    private function getAddressAsUrlParam($omitRegion = false)
    {
        $map = [
            'street',
            'city',
            'postcode',
            'region',
            'country_id'
        ];

        if ($omitRegion){
            unset($map[array_search('region', $map)]);
        }

        $address = $this->getAddressForValidation()->toArray($map);

        $address = implode(',', $address);
        return urlencode($address);
    }

    /**
     * @param $response
     */
    private function getGoogleCandidates($response)
    {
        if (isset($response['results']) && $response['status'] == 'OK') {
            $validatedWithoutRegion = isset($response['iwd_validated_without_region']) && $response['iwd_validated_without_region'];

            foreach ($response['results'] as $candidate) {
                if (!isset($candidate['address_components'])) {
                    continue;
                }

                $address = $this->getAddressCandidate($candidate['address_components'], $candidate['formatted_address'], $validatedWithoutRegion);
                if (empty($address)) {
                    continue;
                }

                $this->addSuggestedAddress($address);

                if ($this->response->getIsValid()) {
                    break;
                }
            }
        }
    }

    /**
     * @param array $candidate
     * @param string $formattedAddress
     * @param bool $validatedWithoutRegion
     * @return \IWD\AddressValidation\Model\Validation\Address|null
     */
    private function getAddressCandidate($candidate, $formattedAddress, $validatedWithoutRegion = false)
    {
        $address = clone $this->getAddressForValidation();

        $street_number = '';
        $route = '';
        foreach ($candidate as $component) {
            if (isset($component['types'][0]) && isset($component['long_name']) && isset($component['short_name'])) {
                $id = $component['types'][0];
                $valueLong = trim($component['long_name']);
                $valueShort = trim($component['short_name']);

                switch ($id) {
                    case "postal_code":
                        if (empty($valueLong)) {
                            return null;
                        }
                        $address->setPostcode($valueLong);
                        break;
                    case "country":
                        if (empty($valueLong)) {
                            return null;
                        }
                        $address->setCountryId($valueShort);
                        break;
                    case "administrative_area_level_1":
                        if ($validatedWithoutRegion) {
                            break;
                        }
                        if (empty($valueLong) && empty($valueShort)) {
                            return null;
                        }
                        $address->setRegion($valueLong);
                        $address->setRegionCode($valueShort);
                        break;
                    case "locality":
                        if (empty($valueLong)) {
                            return null;
                        }
                        $address->setCity($valueLong);
                        break;
                    case 'postal_town':
                        if (empty($valueLong)) {
                            break;
                        }
                        $address->setPostalTown($valueLong);
                        break;
                    case "street_number":
                        $street_number = $valueLong;
                        break;
                    case "route":
                        $route = $valueLong;
                        break;
                }
            }
        }

        if (empty($street_number) && empty($route)) {
            return null;
        }

        $street = $street_number . ' ' . $route;

        if (strpos($formattedAddress, $street) === false) {
            $street2 = $route . ' ' . $street_number;

            if (strpos($formattedAddress, $street2) !== false) {
                $street = $street2;
            }
        }

        $address->setStreet($street);

        return $address;
    }

    /**
     * @return bool
     */
    public function getEnable()
    {
        $key = $this->helper->getGoogleApiKey();
        return !empty($key);
    }
}
