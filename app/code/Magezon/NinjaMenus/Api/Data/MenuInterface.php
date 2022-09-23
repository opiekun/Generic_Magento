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

namespace Magezon\NinjaMenus\Api\Data;

interface MenuInterface
{
    const FIELD_MENU_ID           = 'menu_id';
    const FIELD_NAME              = 'name';
    const FIELD_IDENTIFIER        = 'identifier';
    const FIELD_TYPE              = 'type';
    const FIELD_MOBILE_TYPE       = 'mobile_type';
    const FIELD_IS_ACTIVE         = 'is_active';
    const FIELD_PROFILE           = 'profile';
    const FIELD_STICKY            = 'sticky';
    const FIELD_MOBILE_BREAKPOINT = 'mobile_breakpoint';
    const FIELD_HAMBURGER         = 'hamburger';
    const FIELD_HAMBURGER_TITLE   = 'hamburger_title';
    const FIELD_CSS_CLASSES       = 'css_classes';
    const FIELD_CUSTOM_CSS        = 'custom_css';
    const FIELD_CREATION_TIME     = 'creation_time';
    const FIELD_UPDATED_TIME      = 'update_time';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setId($id);

    /**
     * Get menu name
     *
     * @return string
     */
    public function getName();

    /**
     * Set menu name
     *
     * @param string $name
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setName($name);

    /**
     * Get menu identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Set menu identifier
     *
     * @param string $identifier
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setIdentifier($identifier);

    /**
     * Get menu type
     *
     * @return string
     */
    public function getType();

    /**
     * Set menu type
     *
     * @param string $type
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setType($type);

    /**
     * Get menu mobile type
     *
     * @return string
     */
    public function getMobileType();

    /**
     * Set menu mobile type
     *
     * @param string $mobileType
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setMobileType($mobileType);

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setIsActive($isActive);

    /**
     * Get profile
     *
     * @return string|null
     */
    public function getProfile();

    /**
     * Set profile
     *
     * @param string $profile
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setProfile($profile);

    /**
     * Get Sticky
     *
     * @return bool|null
     */
    public function getSticky();

    /**
     * Set sticky
     *
     * @param int|bool $sticky
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setSticky($sticky);

    /**
     * Get mobile breakpoint
     *
     * @return int|null
     */
    public function getMobileBreakpoint();

    /**
     * Set mobile breakpoint
     *
     * @param int $mobileBreakpoint
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setMobileBreakpoint($mobileBreakpoint);

    /**
     * Get hamburger
     *
     * @return bool|null
     */
    public function getHamburger();

    /**
     * Set hamburger
     *
     * @param int|bool $hamburger
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setHamburger($hamburger);

    /**
     * Get hamburger title
     *
     * @return string
     */
    public function getHamburgerTitle();

    /**
     * Set hamburger title
     *
     * @param string $hamburgerTitle
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setHamburgerTitle($hamburgerTitle);

    /**
     * Get css classes
     *
     * @return string
     */
    public function getCssClasses();

    /**
     * Set css classes
     *
     * @param string $cssClasses
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setCssClasses($cssClasses);

    /**
     * Get custom css
     *
     * @return string
     */
    public function getCustomCss();

    /**
     * Set custom css
     *
     * @param string $customCss
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setCustomCss($customCss);

    /**
     * @return string|null
     */
    public function getCreationTime();

    /**
     * @param string $creationTime
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setCreationTime($creationTime);

    /**
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * @param string $updateTime
     * @return \Magezon\NinjaMenus\Api\Data\MenuInterface
     */
    public function setUpdateTime($updateTime);
}
