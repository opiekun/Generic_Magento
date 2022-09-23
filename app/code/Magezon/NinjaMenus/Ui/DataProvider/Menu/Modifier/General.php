<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Ui\DataProvider\Menu\Modifier;

use Magento\Ui\Component\Form\Fieldset;
use Magezon\UiBuilder\Data\Form\Element\Factory;
use Magezon\UiBuilder\Data\Form\Element\CollectionFactory;

class General extends AbstractModifier
{
    const GROUP_GENERAL                    = 'general';
    const GROUP_GENERAL_DEFAULT_SORT_ORDER = 0;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Cms\Ui\Component\Listing\Column\Cms\Options
     */
    protected $stores;

    /**
     * @var array
     */
    protected $menuType;

    /**
     * @var array
     */
    protected $mobileMenuType;

    /**
     * @param Factory                                              $factoryElement    
     * @param CollectionFactory                                    $factoryCollection 
     * @param \Magento\Framework\Registry                          $registry          
     * @param \Magento\Framework\App\RequestInterface              $request           
     * @param \Magento\Cms\Ui\Component\Listing\Column\Cms\Options $stores            
     * @param \Magezon\NinjaMenus\Model\Source\MenuType            $menuType          
     * @param \Magezon\NinjaMenus\Model\Source\MobileMenuType      $mobileMenuType    
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Cms\Ui\Component\Listing\Column\Cms\Options $stores,
        \Magezon\NinjaMenus\Model\Source\MenuType $menuType,
        \Magezon\NinjaMenus\Model\Source\MobileMenuType $mobileMenuType
    ) {
        parent::__construct($factoryElement, $factoryCollection, $registry);
        $this->request        = $request;
        $this->stores         = $stores;
        $this->menuType       = $menuType;
        $this->mobileMenuType = $mobileMenuType;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->prepareChildren();

        $this->createPanel();

        return $this->meta;
    }

    /**
     * @return \Magezon\UiBuilder\Data\Form\Element\Fieldset
     */
    public function prepareChildren()
    {
        $this->addChildren('store_id', 'hidden');

        $this->addChildren(
            'is_active',
            'boolean',
            [
                'label'     => __('Enable Menu'),
                'sortOrder' => 0,
                'default'   => 1
            ]
        );

        $this->addChildren(
            'name',
            'text',
            [
                'label'     => __('Menu Name'),
                'sortOrder' => 10,
                'required'  => true
            ]
        );

        $this->addChildren(
            'identifier',
            'text',
            [
                'label'          => __('Identifier'),
                'sortOrder'      => 20,
                'required'       => true,
                'additionalInfo' => __('Use <b>top-menu</b> to replace top navigation.')
            ]
        );

        $this->addChildren(
            'type',
            'select',
            [
                'label'     => __('Desktop Type'),
                'sortOrder' => 30,
                'options'   => $this->menuType->toOptionArray(),
                'value'     => 'horizontal'
            ]
        );

        $this->addChildren(
            'mobile_type',
            'select',
            [
                'label'     => __('Mobile Type'),
                'sortOrder' => 40,
                'options'   => $this->mobileMenuType->toOptionArray(),
                'value'     => 'accordion'
            ]
        );

        $this->addChildren(
            'store_id',
            'multiselect',
            [
                'sortOrder'    => 50,
                'options'      => $this->stores->toOptionArray(),
                'defaultValue' => 0,
                'label'        => __('Store View'),
                'validation'   => [
                    'required-entry' => true
                ],
                'additionalInfo' => '<a href="https://blog.magezon.com/how-to-create-multi-language-menus-with-ninja-menus?utm_campaign=ninjamenus&utm_source=userguide&utm_medium=backend" target="_blank">How to create multi-language menus with Ninja Menus</a>'
            ]
        );

        $this->buildAdvancedFieldset();
        $this->buildStylingFieldset();
    }

    private function buildAdvancedFieldset()
    {
        $expand = false;
        if ($this->request->getParam('advanced')) {
            $expand = true;
        }
        $advanced = $this->addFieldset(
            'advanced',
            [
                'label'                           => __('Advanced Settings'),
                'sortOrder'                       => 1000,
                'collapsible'                     => true,
                'initializeFieldsetDataByDefault' => $expand,
                'opened'                          => $expand
            ]
        );

            $advanced->addChildren(
                'mobile_breakpoint',
                'text',
                [
                    'label'      => __('Mobile Breakpoint(px)'),
                    'sortOrder'  => 10,
                    'default'    => '768',
                    'validation' => [
                        'validate-not-negative-number' => true,
                        'validate-number'              => true
                    ]
                ]
            );

            $advanced->addChildren(
                'sticky',
                'boolean',
                [
                    'label'     => __('Enable Sticky Menu'),
                    'sortOrder' => 20
                ]
            );

            $advanced->addChildren(
                'overlay',
                'boolean',
                [
                    'label'          => __('Overlay'),
                    'sortOrder'      => 30,
                    'additionalInfo' => __('If enable, there will be a dark background when you hover over the menu. This option is only applied on the Top Navigation.')
                ]
            );

            $advanced->addChildren(
                'overlay_opacity',
                'number',
                [
                    'label'     => __('Overlay Opacity'),
                    'sortOrder' => 35,
                    'notice'    => __('Enter value in range 0-1'),
                    'validation' => [
                        'validate-number' => true,
                        'validation-number-0-1' => true,
                        'validation-number-separated' => true,
                    ],
                    'imports'   => [
                        'visible' => '${ $.provider }:${ $.parentScope }.overlay',
                        '__disableTmpl' => ['visible' => false]
                    ]
                ]
            );

            $advanced->addChildren(
                'hamburger',
                'boolean',
                [
                    'label'     => __('Hamburger on Mobile'),
                    'sortOrder' => 40
                ]
            );

            $advanced->addChildren(
                'hamburger_title',
                'text',
                [
                    'label'     => __('Hamburger Title'),
                    'default'   => 'MENU',
                    'sortOrder' => 50,
                    'imports'   => [
                        'visible' => '${ $.provider }:${ $.parentScope }.hamburger',
                        '__disableTmpl' => ['visible' => false]
                    ]
                ]
            );

            $advanced->addChildren(
                'hover_delay_timeout',
                'number',
                [
                    'label'     => __('Hover Delay Timeout'),
                    'sortOrder' => 55,
                    'notice'    => __('After X milliseconds, the submenus will be displayed')
                ]
            );

            $advanced->addChildren(
                'css_classes',
                'text',
                [
                    'label'     => __('CSS Classes'),
                    'sortOrder' => 60
                ]
            );

            $advanced->addChildren(
                'custom_css',
                'code',
                [
                    'label'     => __('Custom CSS'),
                    'sortOrder' => 70
                ]
            );

        return $advanced;
    }

    private function buildStylingFieldset()
    {
        $expand = false;
        if ($this->request->getParam('styling')) {
            $expand = true;
        }
        $styling = $this->addFieldset(
            'styling',
            [
                'label'                           => __('Styling'),
                'sortOrder'                       => 900,
                'collapsible'                     => true,
                'initializeFieldsetDataByDefault' => $expand,
                'opened'                          => $expand
            ]
        );

            $styling->addChildren(
                'main_font_size',
                'text',
                [
                    'label'     => __('Main Font Size'),
                    'sortOrder' => 10
                ]
            );

            $styling->addChildren(
                'main_font_weight',
                'text',
                [
                    'label'     => __('Main Font Weight'),
                    'sortOrder' => 20
                ]
            );

            $styling->addChildren(
                'main_color',
                'color',
                [
                    'label'     => __('Main Color'),
                    'sortOrder' => 30
                ]
            );

            $styling->addChildren(
                'main_background_color',
                'color',
                [
                    'label'     => __('Main Background Color'),
                    'sortOrder' => 40
                ]
            );

            $styling->addChildren(
                'main_hover_color',
                'color',
                [
                    'label'     => __('Main Hover Color'),
                    'sortOrder' => 50
                ]
            );

            $styling->addChildren(
                'main_hover_background_color',
                'color',
                [
                    'label'     => __('Main Hover Background Color'),
                    'sortOrder' => 60
                ]
            );

            $styling->addChildren(
                'secondary_color',
                'color',
                [
                    'label'     => __('Secondary Color'),
                    'sortOrder' => 70
                ]
            );

            $styling->addChildren(
                'secondary_background_color',
                'color',
                [
                    'label'     => __('Secondary Background Color'),
                    'sortOrder' => 80
                ]
            );

            $styling->addChildren(
                'secondary_hover_color',
                'color',
                [
                    'label'     => __('Secondary Hover Color'),
                    'sortOrder' => 90
                ]
            );

            $styling->addChildren(
                'secondary_hover_background_color',
                'color',
                [
                    'label'     => __('Secondary Hover Background Color'),
                    'sortOrder' => 100
                ]
            );

        return $styling;
    }

    /**
     * Create Editor panel
     *
     * @return $this
     */
    protected function createPanel()
    {
        $expand = false;
        if ($this->request->getParam('general') || $this->request->getParam('advanced')) {
            $expand = true;
        }
        if (!$this->getCurrentMenu()->getId()) {
            $expand = true;
        }
        $children = $this->getChildren();
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_GENERAL => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'                           => __('General'),
                                'componentType'                   => Fieldset::NAME,
                                'collapsible'                     => true,
                                'initializeFieldsetDataByDefault' => $expand,
                                'opened'                          => $expand,
                                'sortOrder'                       => static::GROUP_GENERAL_DEFAULT_SORT_ORDER,
                                'dataScope'                       => 'data'
                            ]
                        ]
                    ],
                    'children' => $children
                ]
            ]
        );
        return $this;
    }
}
