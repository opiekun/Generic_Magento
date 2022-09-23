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

class Icon extends AbstractElement
{
    public function _construct()
    {
        $this->setType('icon');
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $_config = (array) $this->getData('config');
        if (isset($_config['className'])) {
            $_config['className'] .= ' mgz-form-element-icon';
        } else {
            $_config['className'] = 'mgz-form-element-icon';
        }

        $config = array_replace_recursive([
            'templateOptions' => [
                'element'            => 'Magezon_Builder/js/form/element/icon',
                'templateUrl'        => 'Magezon_Builder/js/templates/form/element/icon.html',
                'groupBy'            => 'group',
                'label'              => __('Icon'),
                'iconLibraryLabel'   => __('Icon Library'),
                'defaultFont'        => 'awesome'
            ]
        ], (array) $_config);

        return [
            'config' => $config
        ];
    }
}
