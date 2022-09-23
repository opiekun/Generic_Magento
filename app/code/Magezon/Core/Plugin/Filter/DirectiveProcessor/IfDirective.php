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

namespace Magezon\Core\Plugin\Filter\DirectiveProcessor;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filter\VariableResolverInterface;

class IfDirective
{
    /**
     * @var \Magento\Framework\Filter\VariableResolverInterface
     */
    private $_variableResolver;

	public function aroundProcess(
		$subject,
		callable $proceed,
		$construction, 
		$filter, 
		$templateVariables
	) {
		if (!empty($templateVariables)) {
			$variables = explode('==', $construction[1]);
			if (count($variables) >= 2) {
				$variable = trim($variables[0]);
				$value    = trim($variables[1]);
				if ($this->getVariableResolver()->resolve($variable, $filter, $templateVariables) == $value) {
					return $filter->filter($construction[2]);
				} else {
					if (isset($construction[3]) && isset($construction[4])) {
						return $filter->filter($construction[4]);
					}
					return '';
				}
			} else {
				return $proceed($construction, $filter, $templateVariables);
			}
        } else {
			return $proceed($construction, $filter, $templateVariables);
		}
	}

    private function getVariableResolver()
    {
        if (!$this->_variableResolver) {
            $this->_variableResolver = ObjectManager::getInstance()->get(VariableResolverInterface::class);
        }
        return $this->_variableResolver;
    }
}