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

namespace Magezon\Core\Framework\Stdlib;

class ArrayManager extends \Magento\Framework\Stdlib\ArrayManager
{
	    /**
     * Finds node in nested array and saves its index and parent node reference
     *
     * @param string $path
     * @param array $data
     * @param string $delimiter
     * @param bool $populate
     * @return bool
     */
    protected function find($path, array &$data, $delimiter, $populate = false)
    {
        if (is_array($path)) {
            $path = implode($delimiter, $path);
        }

        return parent::find($path, $data, $delimiter, $populate);
    }
}