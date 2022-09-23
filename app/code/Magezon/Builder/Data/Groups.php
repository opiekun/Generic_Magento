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
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Data;

class Groups
{
    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var array
     */
    protected $sortableElements;

    /**
     * @param array $groups        
     */
    public function __construct(
        array $groups = []
    ) {
        $this->groups = array_merge($this->groups, $groups);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        if ($this->sortableElements == null && $this->groups) {
            $sortableElements = [];
            $groups           = [];
            foreach ($this->groups as $type => $group) {
                if (!isset($group['name'])) continue;
                if (!isset($group['sortOrder'])) $group['sortOrder'] = 0;
                $sortableElements[] = [
                    'name'      => $group['name'],
                    'type'      => $type,
                    'sortOrder' => $group['sortOrder']
                ];
            }
            usort($sortableElements, function($firstLink, $secondLink) {
                return ($firstLink['sortOrder'] > $secondLink['sortOrder']);
            });
            $this->sortableElements = $sortableElements;
        }
        return $this->sortableElements;
    }
}
