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

namespace Magezon\Builder\Data\Form\Element;

class Code extends AbstractElement
{
    public function _construct()
    {
        $this->setType('code');
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = array_replace_recursive([
            'templateOptions' => [
                'element'            => 'Magezon_Builder/js/form/element/code',
                'templateUrl'        => 'Magezon_Builder/js/templates/form/element/code.html',
                'wrapperTemplateUrl' => 'Magezon_Builder/js/templates/form/field.html',
                'editorOptions'      => [
                    'lineWrapping'  => true,
                    'lineNumbers'   => true,
                    'mode'          => 'css',
                    'theme'         => 'default'
                ]
            ]
        ], (array) $this->getData('config'));

        return [
            'config' => $config
        ];
    }
}
