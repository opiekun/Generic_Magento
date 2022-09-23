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

namespace Magezon\Core\Plugin\Block;

class Menu
{
	public function aroundRenderNavigation(
		\Magento\Backend\Block\Menu $menuBlock,
		callable $proceed,
		$menu, $level = 0, $limit = 0, $colBrakes = []
	) {

		foreach ($menu as $item) {
			if ($item->getId() == 'Magezon_Core::extensions' && (!$item->hasChildren() || !$item->getChildren()->getFirstAvailable())) {
				$menu->remove('Magezon_Core::extensions');
				break;
			}
		}
		$result = $proceed($menu, $level, $limit, $colBrakes);
		return $result;
	}
}