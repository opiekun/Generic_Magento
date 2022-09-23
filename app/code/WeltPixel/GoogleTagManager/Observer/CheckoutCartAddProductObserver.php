<?php
namespace WeltPixel\GoogleTagManager\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutCartAddProductObserver implements ObserverInterface
{
    /**
     * @var \WeltPixel\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;


    /**
     * @param \WeltPixel\GoogleTagManager\Helper\Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     */
    public function __construct(\WeltPixel\GoogleTagManager\Helper\Data $helper,
                                \Magento\Framework\ObjectManagerInterface $objectManager,
                                \Magento\Checkout\Model\Session $_checkoutSession)
    {
        $this->helper = $helper;
        $this->_objectManager = $objectManager;
        $this->_checkoutSession = $_checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $product = $observer->getData('product');
        $request = $observer->getData('request');

        $params = $request->getParams();

        if (isset($params['qty'])) {
            $filter = new \Zend_Filter_LocalizedToNormalized(
                ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
            );
            $qty = $filter->filter($params['qty']);
        } else {
            $qty = 1;
        }

        if ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $superGroup = $params['super_group'];
            $superGroup = is_array($superGroup) ? array_filter($superGroup, 'intval') : [];

            $associatedProducts =  $product->getTypeInstance()->getAssociatedProducts($product);
            foreach ($associatedProducts as $associatedProduct) {
                if (isset($superGroup[$associatedProduct->getId()]) && ($superGroup[$associatedProduct->getId()] > 0) ) {
                    $currentAddToCartData = $this->_checkoutSession->getAddToCartData();
                    $addToCartPushData = $this->helper->addToCartPushData($superGroup[$associatedProduct->getId()], $associatedProduct);
                    $newAddToCartPushData = $this->helper->mergeAddToCartPushData($currentAddToCartData, $addToCartPushData);
                    $this->_checkoutSession->setAddToCartData($newAddToCartPushData);

                }
            }
        } else {
            $this->_checkoutSession->setAddToCartData($this->helper->addToCartPushData($qty, $product));
        }

        return $this;
    }
}
