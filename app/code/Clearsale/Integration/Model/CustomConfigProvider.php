<?php

namespace Clearsale\Integration\Model;

use Magento\Checkout\Model\ConfigProviderInterface;


class CustomConfigProvider implements ConfigProviderInterface
{

    protected $storeManager;
    protected $scopeConfig;
    protected $sessionManager;
    
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Framework\Session\SessionManager $sessionManager
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->sessionManager = $sessionManager;
        
    }

    public function getConfig()
    {
        $user = $this->scopeConfig->getValue('clearsale_configuration/cs_config/clientid',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sessionID = $this->sessionManager->getSessionId();

        $isActive = $this->scopeConfig->getValue('clearsale_configuration/cs_config/active',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $script = '';
        $tmTags = '';
        if ($isActive) {

            if(strlen($user) > 10) {
                $user =  substr($user, 0, 10);
            }

            $script =   "<script>"."\n";
            $script .=  "(function (a, b, c, d, e, f, g)"."\n";
            $script .=  "{ a['CsdpObject'] = e; a[e] = a[e] || function () { (a[e].q = a[e].q || []).push(arguments) },"."\n";
            $script .=  "a[e].l = 1 * new Date(); f = b.createElement(c), g = b.getElementsByTagName(c)[0];"."\n";
            $script .=  "f.async = 1; f.src = d; g.parentNode.insertBefore(f, g) })"."\n";
            $script .=  "(window, document, 'script', '//device.clearsale.com.br/p/fp.js', 'csdp');"."\n";
            $script .=  "csdp('app', '".$user."');"."\n";
            $script .=  "csdp('sessionid', '".$sessionID."');"."\n";
            $script .=  "</script>";
            
            
            
            $tmTags  = "<!-- Begin CS Tags-->";
            $tmTags .= "<p style=\"background:url(https://h.online-metrix.net/fp/clear.png?org_id=k6dvnkdk&amp;session_id=".$sessionID."&amp;m=1)\">";
            $tmTags .= "</p>";
            $tmTags .= "<img src=\"https://h.online-metrix.net/fp/clear.png?org_id=k6dvnkdk&amp;session_id=".$sessionID."&amp;m=2\" alt=\"\" >";
            $tmTags .= "<script src=\"https://h.online-metrix.net/fp/check.js?org_id=k6dvnkdk&amp;session_id=".$sessionID."\" type=\"text/javascript\">";
            $tmTags .= "</script>";
            //$tmTags .= "<object type=\"application/x-shockwave-flash\" data=\"https://h.online-metrix.net/fp/fp.swf?org_id=k6dvnkdk&amp;session_id=".$//sessionID."\" width=\"1\" height=\"1\" id=\"obj_id\">";//
            //$tmTags .= "<param name=\"movie\" value=\"https://h.online-metrix.net/fp/fp.swf?org_id=k6dvnkdk&amp;session_id=".$sessionID."\" />";
            //$tmTags .= "</object>";
            $tmTags .= "<!-- End CS Tags -->";
        }  


        $finger = [];
        $finger['script'] = $script;
        $finger['tmtags'] = $tmTags;

        return $finger;
    }
}



                