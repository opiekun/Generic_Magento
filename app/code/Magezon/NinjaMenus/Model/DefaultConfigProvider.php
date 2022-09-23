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

class DefaultConfigProvider extends \Magezon\Builder\Model\DefaultConfigProvider
{
	/**
	 * @var string
	 */
	protected $_builderArea = 'menu';

	/**
	 * @return array
	 */
	public function getConfig()
	{
		$config = parent::getConfig();
		$config['profile'] = [
			'builder'          => 'Magezon\NinjaMenus\Block\Builder',
			'home'             => 'https://magezon.com/magento-2-mega-menu.html?utm_campaign=mgzbuilder&utm_source=mgz_user&utm_medium=backend',
			'templateUrl'      => 'https://www.magezon.com/productfile/ninjamenus/templates.php',
			'importCategories' => [
				'class' => 'Magezon\NinjaMenus\Data\Modal\ImportCategories'
			]
		];
		$config['historyLabels']['imported_categories'] = __('Imported Categories');
		return $config;
	}
}