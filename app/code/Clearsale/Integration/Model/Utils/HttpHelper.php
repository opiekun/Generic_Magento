<?php
namespace Clearsale\Integration\Model\Utils;
use Psr\Log\LoggerInterface;
use Clearsale\Integration\Model\Utils\HttpMessage;

class HttpHelper
{

    protected $scopeConfig;
	protected $logger;

	public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }
    public function postData($data, $url) {
		$return = new HttpMessage();
		$dataString =  $this->json_encode_unicode($data);
		$isLogenabled = $this->scopeConfig->getValue('clearsale_configuration/cs_config/enabled_log',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			//$this->logger->info($data->getData());
			//$this->logger->info($url);
//		$this->logger->info($dataString);

		if($isLogenabled)
		{
			$this->logger->info($dataString);
		}
				
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($dataString))
			);

		$return->Body = curl_exec($ch);
		if(!$return)
		{
			$return->HttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$this->logger->info(curl_error($ch));
		}
		else 
		{
			$return->HttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		curl_close($ch);
		
		$jsonReturn = $return->Body;
		if($isLogenabled)
		{
		 
		 $this->logger->info($jsonReturn);
		}
		if($return->HttpCode != 200)
		{
			$this->logger->info($return->Body);
		}
		return $return;
	}

public function json_encode_unicode($data) {
	if (defined('JSON_UNESCAPED_UNICODE')) {
		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	return preg_replace_callback('/(?<!\\\\)\\\\u([0-9a-f]{4})/i',
		function ($m) {
			$d = pack("H*", $m[1]);
			$r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
			return $r!=="?" && $r!=="" ? $r : $m[0];
		}, json_encode($data)
	);
}

}