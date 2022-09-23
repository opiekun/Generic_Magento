<?php

namespace IWD\AddressValidation\Model\XML;

use Magento\Framework\Exception\LocalizedException;

/**
 * XML2Array: A class to convert XML to array in PHP
 * It returns the array which can be converted back to XML using the Array2XML script
 * It takes an XML string or a DOMDocument object as an input.
 *
 * See Array2XML: http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
 *
 * Author : Lalit Patel
 * Website: http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array
 * License: Apache License 2.0
 *          http://www.apache.org/licenses/LICENSE-2.0
 */
class XML2Array
{
    /**
     * @var null
     */
    private static $xml = null;

    /**
     * @var string
     */
    private static $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true)
    {
        self::$xml = new \DOMDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /**
     * Convert an XML to Array
     * @param $inputXml
     * @return mixed
     * @throws LocalizedException
     */
    public static function &createArray($inputXml)
    {
        $xml = self::getXMLRoot();
        if (is_string($inputXml)) {
            $parsed = $xml->loadXML($inputXml);
            if (!$parsed) {
                throw new LocalizedException(__('[XML2Array] Error parsing the XML string.'));
            }
        } else {
            if (get_class($inputXml) != 'DOMDocument') {
                throw new LocalizedException(__('[XML2Array] The input XML object should be of type: DOMDocument.'));
            }
            $xml = self::$xml = $inputXml;
        }
        $array[$xml->documentElement->tagName] = self::convert($xml->documentElement);
        self::$xml = null; // clear the xml node in the class for 2nd time use.
        return $array;
    }

    /**
     * Convert an Array to XML
     * @param mixed $node - XML as a string or as an object of DOMDocument
     * @return mixed
     */
    private static function &convert($node)
    {
        $output = [];

        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
                $output['@cdata'] = trim($node->textContent);
                break;

            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;

            case XML_ELEMENT_NODE:
                // for each child node, call the covert function recursively
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = self::convert($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;

                        // assume more nodes of same kind are coming
                        if (!isset($output[$t])) {
                            $output[$t] = [];
                        }
                        $output[$t][] = $v;
                    } else {
                        //check if it is not an empty text node
                        if ($v !== '') {
                            $output = $v;
                        }
                    }
                }

                if (is_array($output)) {
                    // if only one node of its kind, assign it directly instead if array($value);
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1) {
                            $output[$t] = $v[0];
                        }
                    }
                    if (empty($output)) {
                        //for empty nodes
                        $output = '';
                    }
                }

                // loop through the attributes and collect them
                if ($node->attributes->length) {
                    $a = [];
                    foreach ($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    // if its an leaf node, store the value in @value instead of directly storing it.
                    if (!is_array($output)) {
                        $output = ['@value' => $output];
                    }
                    $output['@attributes'] = $a;
                }
                break;
        }
        return $output;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot()
    {
        if (empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }
}
