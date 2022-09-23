<?php

namespace WeltPixel\GoogleTagManager\Plugin;

class Utility extends \WeltPixel\Backend\Plugin\Utility
{
    /**
     * @return string
     */
    protected function getModuleName()
    {
        return $this->convertToString(
            [
                '87', '101', '108', '116', '80', '105', '120', '101', '108', '95', '71', '111', '111', '103', '108',
                '101', '84', '97', '103', '77', '97', '110', '97', '103', '101', '114'
            ]
        );
    }

    /**
     * @return array
     */
    protected function _getAdminPaths()
    {
        return [
            $this->convertToString(
                [
                    '115', '121', '115', '116', '101', '109', '95', '99', '111', '110', '102', '105', '103', '47',
                    '101', '100', '105', '116', '47', '115', '101', '99', '116', '105', '111', '110', '47', '119',
                    '101', '108', '116', '112', '105', '120', '101', '108', '95', '103', '111', '111', '103', '108',
                    '101', '116', '97', '103', '109', '97', '110', '97', '103', '101', '114'
                ]
            )
        ];
    }
}
