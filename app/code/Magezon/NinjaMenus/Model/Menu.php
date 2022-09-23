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

namespace Magezon\NinjaMenus\Model;

use Magezon\NinjaMenus\Api\Data\MenuInterface;
use Magento\Framework\Model\AbstractModel;

class Menu extends AbstractModel implements MenuInterface
{
    /**
     * NinjaMenus form cache tag
     */
    const CACHE_TAG = 'ninjamenus_m';

    /**#@+
     * Form's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**#@-*/

    /**#@-*/
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ninjamenus_menu';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magezon\NinjaMenus\Model\ResourceModel\Menu::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return parent::getData(self::FIELD_MENU_ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setId($id)
    {
        return $this->setData(self::FIELD_MENU_ID, $id);
    }

    /**
     * Get menu name
     *
     * @return string
     */
    public function getName()
    {
        return parent::getData(self::FIELD_NAME);
    }

    /**
     * Set menu name
     *
     * @param string $name
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setName($name)
    {
        return $this->setData(self::FIELD_NAME, $name);
    }

    /**
     * Get menu identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return parent::getData(self::FIELD_IDENTIFIER);
    }

    /**
     * Set menu identifier
     *
     * @param string $identifier
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::FIELD_IDENTIFIER, $identifier);
    }

    /**
     * Get menu type
     *
     * @return string
     */
    public function getType()
    {
        return parent::getData(self::FIELD_TYPE);
    }

    /**
     * Set menu type
     *
     * @param string $type
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setType($type)
    {
        return $this->setData(self::FIELD_TYPE, $type);
    }

    /**
     * Get menu mobile type
     *
     * @return string
     */
    public function getMobileType()
    {
        return parent::getData(self::FIELD_MOBILE_TYPE);
    }

    /**
     * Set menu mobile type
     *
     * @param string $type
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setMobileType($mobileType)
    {
        return $this->setData(self::FIELD_MOBILE_TYPE, $mobileType);
    }

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getIsActive()
    {
        return parent::getData(self::FIELD_IS_ACTIVE);
    }

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::FIELD_IS_ACTIVE, $isActive);
    }

    /**
     * Get profile
     *
     * @return string|null
     */
    public function getProfile()
    {
        return parent::getData(self::FIELD_PROFILE);
    }

    /**
     * Set profile
     *
     * @param string $profile
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setProfile($profile)
    {
        return $this->setData(self::FIELD_PROFILE, $profile);
    }

    /**
     * Get Sticky
     *
     * @return bool|null
     */
    public function getSticky()
    {
        return parent::getData(self::FIELD_STICKY);
    }

    /**
     * Set sticky
     *
     * @param int|bool $sticky
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setSticky($sticky)
    {
        return $this->setData(self::FIELD_STICKY, $sticky);
    }

    /**
     * Get mobile breakpoint
     *
     * @return int|null
     */
    public function getMobileBreakpoint()
    {
        return parent::getData(self::FIELD_MOBILE_BREAKPOINT);
    }

    /**
     * Set mobile breakpoint
     *
     * @param int $mobileBreakpoint
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setMobileBreakpoint($mobileBreakpoint)
    {
        return $this->setData(self::FIELD_MOBILE_BREAKPOINT, $mobileBreakpoint);
    }

    /**
     * Get hamburger
     *
     * @return bool|null
     */
    public function getHamburger()
    {
        return parent::getData(self::FIELD_HAMBURGER);
    }

    /**
     * Set hamburger
     *
     * @param int|bool $hamburger
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setHamburger($hamburger)
    {
        return $this->setData(self::FIELD_HAMBURGER, $hamburger);
    }

    /**
     * Get hamburger title
     *
     * @return string
     */
    public function getHamburgerTitle()
    {
        return parent::getData(self::FIELD_HAMBURGER_TITLE);
    }

    /**
     * Set hamburger title
     *
     * @param string $hamburgerTitle
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setHamburgerTitle($hamburgerTitle)
    {
        return $this->setData(self::FIELD_HAMBURGER_TITLE, $hamburgerTitle);
    }

    /**
     * Get css classes
     *
     * @return string
     */
    public function getCssClasses()
    {
        return parent::getData(self::FIELD_CSS_CLASSES);
    }

    /**
     * Set css classes
     *
     * @param string $cssClasses
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setCssClasses($cssClasses)
    {
        return $this->setData(self::FIELD_CSS_CLASSES, $cssClasses);
    }

    /**
     * Get custom css
     *
     * @return string
     */
    public function getCustomCss()
    {
        return parent::getData(self::FIELD_CUSTOM_CSS);
    }

    /**
     * Set custom css
     *
     * @param string $customCss
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setCustomCss($customCss)
    {
        return $this->setData(self::FIELD_CUSTOM_CSS, $customCss);
    }

    /**
     * @return string|null
     */
    public function getCreationTime()
    {
        return parent::getData(self::FIELD_CREATION_TIME);
    }

    /**
     * @param string $creationTime
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::FIELD_CREATION_TIME, $creationTime);
    }

    /**
     * @return string|null
     */
    public function getUpdateTime()
    {
        return parent::getData(self::FIELD_UPDATED_TIME);
    }

    /**
     * @param string $updateTime
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::FIELD_UPDATED_TIME, $updateTime);
    }

    /**
     * @return string
     */
    public function getRandomId()
    {
        return 'ninjamenus' . $this->getId();
    }
}
