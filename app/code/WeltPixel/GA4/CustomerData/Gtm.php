<?php
namespace WeltPixel\GA4\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * Gtm section
 */
class Gtm extends \Magento\Framework\DataObject implements SectionSourceInterface
{

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Constructor
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \WeltPixel\GA4\Helper\Data $gtmHelper,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->jsonHelper = $jsonHelper;
        $this->_checkoutSession = $_checkoutSession;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {

        $data = [];

        /** AddToCart data verifications */
        if ($this->_checkoutSession->getGA4AddToCartData()) {
            $data[] = $this->_checkoutSession->getGA4AddToCartData();
        }

        $this->_checkoutSession->setGA4AddToCartData(null);

        /** RemoveFromCart data verifications */
        if ($this->_checkoutSession->getGA4RemoveFromCartData()) {
            $data[] = $this->_checkoutSession->getGA4RemoveFromCartData();
        }

        $this->_checkoutSession->setGA4RemoveFromCartData(null);

        /** Checkout Steps data verifications */
        if ($this->_checkoutSession->getGA4CheckoutOptionsData()) {
            $checkoutOptions = $this->_checkoutSession->getGA4CheckoutOptionsData();
            foreach ($checkoutOptions as $options) {
                $data[] = $options;
            }
        }
        $this->_checkoutSession->setGA4CheckoutOptionsData(null);

        /** Add To Wishlist Data */
        if ($this->customerSession->getGA4AddToWishListData()) {
            $data[] = $this->customerSession->getGA4AddToWishListData();
        }
        $this->customerSession->setGA4AddToWishListData(null);

        /** Add To Compare Data */
        if ($this->customerSession->getGA4AddToCompareData()) {
            $data[] = $this->customerSession->getGA4AddToCompareData();
        }
        $this->customerSession->setGA4AddToCompareData(null);

        return [
            'datalayer' => $this->jsonHelper->jsonEncode($data)
        ];
    }
}
