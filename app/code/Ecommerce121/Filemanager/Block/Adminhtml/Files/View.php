<?php

declare(strict_types=1);

namespace Ecommerce121\Filemanager\Block\Adminhtml\Files;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class View
 * @package Ecommerce121\Filemanager\Block\Adminhtml\Files
 */
class View extends \Magento\Framework\View\Element\Template
{
    const PLUGIN_NAME = 'filemanager';

    /**
     * @var SessionManagerInterface
     */
    protected $_coreSession;

    /**
     * View constructor.
     * @param Context $context
     * @param array $data
     * @param SessionManagerInterface $coreSession
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $coreSession,
        array $data = []
    ){
        $this->_coreSession = $coreSession;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $this->_coreSession->start();

        return $this->_coreSession->getMessage();
    }

    /**
     * @return string
     */
    public function getUrlSource() : string
    {
        $this->getSignature();

        return sprintf('%spub/%s/dialog.php?type=0&akey=%s',
            $this->getBaseUrl(),
            self::PLUGIN_NAME,
            $this->getSignature()
        );
    }

    /**
     * @return string
     */
    public function getSignature() : string
    {
        $this->_coreSession->start();
        $sessionId = md5($this->_coreSession->getSessionId());
        $signature = hash('sha256', self::PLUGIN_NAME . $sessionId);
        setcookie(self::PLUGIN_NAME, $sessionId, time() + 3600 * 24,'/');
        $this->_coreSession->setSignature($signature);

        return $signature;
    }
}
