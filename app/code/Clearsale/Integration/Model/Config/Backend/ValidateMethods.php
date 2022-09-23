<?php

namespace Clearsale\Integration\Model\Config\Backend;

class ValidateMethods extends \Magento\Framework\App\Config\Value
{

	const ERROR = 'Statuses to send and Analyzing ClearSale should have unique statuses selected';

    public function beforeSave()
    {
        $methodsToSend = $this->getData('groups/cs_config/fields/pending_clearsale/value');
		$analyzing = $this->getData('groups/cs_config/fields/analyzing_clearsale/value');

		$label = $this->getData('field_config/label');

		foreach($methodsToSend as $method) {
			if ($this->getValue() == $method) {
				throw new \Magento\Framework\Exception\ValidatorException(__(self::ERROR));
			}
		}

		if ($label != 'Analyzing ClearSale' && $analyzing == $this->getValue()) {
			throw new \Magento\Framework\Exception\ValidatorException(__(self::ERROR));			
		}	

        $this->setValue($this->getValue());

        parent::beforeSave();
    }
}