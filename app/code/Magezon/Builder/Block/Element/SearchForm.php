<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Block\Element;

class SearchForm extends \Magezon\Builder\Block\Element
{
    /**
     * @return string
     */
    public function getAdditionalStyleHtml()
    {
        $styleHtml    = '';
        $element = $this->getElement();
        $styles['width'] = $this->getStyleProperty($element->getData('form_width'), true);
        if ($this->getStyles('.mgz-element-inner', $styles)) {
            $styleHtml .= '@media (min-width: 768px) {';
            $styleHtml .= $this->getStyles('.block-search', $styles);
            $styleHtml .= '}';

        }

        $styles2 = [
            'background-color' => $this->getStyleColor($element->getData('input_background_color')),
            'color' => $this->getStyleColor($element->getData('input_text_color'))
        ];
        $styleHtml .= $this->getStyles('#search', $styles2);

        return $styleHtml;
    }
}
