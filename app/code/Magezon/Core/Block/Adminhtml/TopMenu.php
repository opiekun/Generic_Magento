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
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Block\Adminhtml;

use Magento\Framework\App\ObjectManager;

class TopMenu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Own page title to display on the page
     *
     * @var string
     */
    protected $pageTitle;

    /**
     * @return \Magento\Framework\AuthorizationInterface
     */
    protected function getAuthorization()
    {
        if ($this->_authorization == null) {
            $this->_authorization = ObjectManager::getInstance()->get(\Magento\Framework\AuthorizationInterface::class);
        }
        return $this->_authorization;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->getAuthorization()->isAllowed($resourceId);
    }

    /**
     * @return bool|void
     */
    public function getLinks()
    {
        $links = $this->intLinks();
        if ($links) {
            foreach ($links as $z => &$_columnLinks) {
                if (!isset($_columnLinks['title'])) {
                    foreach ($_columnLinks as $k => $_link) {
                        if (isset($_link['resource']) && !$this->_isAllowedAction($_link['resource'])) {
                            unset($_columnLinks[$k]);
                        }
                    }
                } elseif (isset($_columnLinks['resource']) && !$this->_isAllowedAction($_columnLinks['resource'])) {
                    unset($links[$z]);
                }
            }
            return $links;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getSupportLink()
    {
        return 'https://magezon.ticksy.com';
    }

    /**
     * @return void
     */
    public function intLinks()
    {
        return;
    }

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        }
        return __($this->pageConfig->getTitle()->getShort());
    }

    /**
     * Provide own page content heading
     *
     * @return string
     */
    public function getPageHeading()
    {
        if (!empty($this->pageTitle)) {
            return __($this->pageTitle);
        }
        return __($this->pageConfig->getTitle()->getShortHeading());
    }

    /**
     * Set own page title
     *
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }
}
