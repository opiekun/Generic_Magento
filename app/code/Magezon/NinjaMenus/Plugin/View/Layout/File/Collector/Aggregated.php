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

namespace Magezon\NinjaMenus\Plugin\View\Layout\File\Collector;

class Aggregated
{
	/**
	 * @var \Magezon\NinjaMenus\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magezon\NinjaMenus\Helper\Data $dataHelper
	 */
	public function __construct(
		\Magezon\NinjaMenus\Helper\Data $dataHelper
	) {
		$this->dataHelper = $dataHelper;
	}

	public function afterGetFiles(
		$subject,
		$result
	) {
		if (!$this->dataHelper->isEnabled()) {
			foreach ($result as $k => $file) {
				if (strpos($file->getFilename(), 'Magezon/NinjaMenus') !== FALSE) {
					unset($result[$k]);
				}
			}
		}
		return $result;
    }
}