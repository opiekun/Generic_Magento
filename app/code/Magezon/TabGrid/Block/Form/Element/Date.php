<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_TabGrid
 * @copyright Copyright (C) 2017 Magezon (https://magezon.com)
 */

namespace Magezon\TabGrid\Block\Form\Element;

class Date extends \Magento\Framework\Data\Form\Element\Date
{
    /**
     * Output the input field and assign calendar instance to it.
     * In order to output the date:
     * - the value must be instantiated (\DateTime)
     * - output format must be set (compatible with \DateTime)
     *
     * @throws \Exception
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('mgz__control-text  input-text');
        $dateFormat = $this->getDateFormat() ?: $this->getFormat();
        $timeFormat = $this->getTimeFormat();
        if (empty($dateFormat)) {
            throw new \Exception(
                'Output format is not specified. ' .
                'Please specify "format" key in constructor, or set it using setFormat().'
            );
        }

        $dataInit = 'data-mage-init="' . $this->_escape(
            json_encode(
                [
                    'mage/calendar' => [
                        'dateFormat'  => $dateFormat,
                        'showsTime'   => !empty($timeFormat),
                        'timeFormat'  => $timeFormat,
                        'buttonImage' => $this->getImage(),
                        'buttonText'  => 'Select Date',
                        'disabled'    => $this->getDisabled()
                    ],
                ]
            )
        ) . '"';


        $dataInit = '
        	<script>
                require([
                    "jquery",
                    "mage/calendar"
                ], function($){
                    $("#' . $this->getHtmlId() . '").calendar({
                        "dateFormat": "' . $dateFormat . '",
                        "showsTime": "' . !empty($timeFormat) . '",
                        "timeFormat": "' . $timeFormat . '",
                        "buttonImage": "' . $this->getImage() . '",
                        "buttonText": "' . __('Select Date'). '",
                        "disabled": ' . ($this->getDisabled() ? 'true' : 'false') . '
                    });
                });
            </script>';

        $html = sprintf(
            '<div class="mgz__tabgrid-date"><input name="%s" id="%s" value="%s" %s  />%s</div>',
            $this->getName(),
            $this->getHtmlId(),
            $this->_escape($this->getValue()),
            $this->serialize($this->getHtmlAttributes()),
            $dataInit
        );
        $html .= $this->getAfterElementHtml();
        return $html;
    }
}
