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
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Booking\Model;

class RentOrders extends \Magento\Framework\Model\AbstractModel

{

    /**

     *@var Magento\Framework\Registry

     */



    protected $_coreRegistry;



    /**

     *@var Magento\Framework\Model\Context

     */



    protected $_context;



    /**

     *@var Magento\Framework\UrlInterface

     */



    protected $urlBuilder;


    /**
     * @var string
     */
    protected $_eventPrefix = 'booking_rent_order';


    /**
     * RentOrders constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     */
    public function __construct(

        \Magento\Framework\Model\Context $context,

        \Magento\Framework\UrlInterface  $urlBuilder,

        \Magento\Framework\Registry $registry,

        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,

        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null

    )

    {
        $this->_context = $context;

        $this->urlBuilder = $urlBuilder;

        $this->resource = $resource;

        $this->resourceCollection = $resourceCollection;

        $this->_coreRegistry = $registry;

        parent::__construct($context,$registry,$resource,$resourceCollection);

    }



    /**

     *@var construct

     */

    protected function _construct()
    {
        $this->_init('Ced\Booking\Model\ResourceModel\RentOrders');
    }
}