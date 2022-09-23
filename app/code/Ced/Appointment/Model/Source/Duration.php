<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Appointment
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Appointment\Model\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;

/**
 * Class Duration
 * @package Ced\Appointment\Model\Source
 */
class Duration extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource

{

    /**
     * @var OptionFactory
     */

    protected $optionFactory;


    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()

    {
        $this->_options = [];
        for ($i = 5; $i <= 240; $i = $i + 5) {
            $this->_options[] = ['label' => $i . __(' min'), 'value' => $i];
        }
        return $this->_options;

    }

}