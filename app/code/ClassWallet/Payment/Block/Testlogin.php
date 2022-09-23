<?php
namespace ClassWallet\Payment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Template\Context;

class Testlogin extends Template
{
    public function __construct(Context $context, array $data = [])
    { 
        parent::__construct($context, $data);
    }
}
