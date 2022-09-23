<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more inmenuation.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Block\Adminhtml;

class TopMenu extends \Magezon\Core\Block\Adminhtml\TopMenu
{
    /**
     * Init menu items
     *
     * @return array
     */
    public function intLinks()
    {
        $links = [
            [
                [
                    'title'    => __('Add New Menu'),
                    'link'     => $this->getUrl('ninjamenus/menu/new'),
                    'resource' => 'Magezon_NinjaMenus::menu_save'
                ],
                [
                    'title'    => __('Manage Menus'),
                    'link'     => $this->getUrl('ninjamenus/menu'),
                    'resource' => 'Magezon_NinjaMenus::menu'
                ],
                [
                    'title'    => __('Settings'),
                    'link'     => $this->getUrl('adminhtml/system_config/edit/section/ninjamenus'),
                    'resource' => 'Magezon_NinjaMenus::settings'
                ]
            ],
            [
                'class' => 'separator'
            ],
            [
                'title'  => __('User Guide'),
                'link'   => 'https://magezon.com/pub/media/productfile/ninjamenus2-installation-guides.pdf',
                'target' => '_blank'
            ],
            [
                'title'  => __('Change Log'),
                'link'   => 'https://www.magezon.com/magento-2-mega-menu.html#release_notes',
                'target' => '_blank'
            ],
            [
                'title'  => __('Get Support'),
                'link'   => $this->getSupportLink(),
                'target' => '_blank'
            ]
        ];
        return $links;
    }
}
