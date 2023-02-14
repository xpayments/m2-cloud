<?php
// vim: set ts=4 sw=4 sts=4 et:
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @author     Qualiteam Software <info@x-cart.com>
 * @category   CDev
 * @package    CDev_XPaymentsCloud
 * @copyright  (c) 2010-present Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace CDev\XPaymentsCloud\Ui\XPaymentsCloud;

/**
 * Configuration provider for XPC method at checkout
 */
class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * Payment method object
     *
     * @var \Magento\Payment\Model\MethodInterface
     */
    protected $method = null;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig = null;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager = null;

    /**
     * X-Payments cart helper
     *
     * @var \CDev\XPaymentsCloud\Helper\Cart
     */
    protected $cartHelper = null;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession = null;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession = null;

    /**
     * Constructor
     *
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \CDev\XPaymentsCloud\Helper\Cart $cartHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     *
     * @return void
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \CDev\XPaymentsCloud\Helper\Cart $cartHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {

        $this->method = $paymentHelper->getMethodInstance(
            \CDev\XPaymentsCloud\Model\Payment\Method\XPaymentsCloud::CODE
        );

        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;

        $this->cartHelper = $cartHelper;

        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Get X-Payments Customer ID
     *
     * @return string
     */
    protected function getXpaymentsCustomerId()
    {
        $xpaymentsCustomerId = '';

        if ($this->customerSession->isLoggedIn()) {

            $xpaymentsCustomerId = (string)$this->customerSession
                ->getCustomer()
                ->getXpaymentsCustomerId();
        }

        return $xpaymentsCustomerId;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $total = $this->checkoutSession->getQuote()
            ->getGrandTotal();

        $showSaveCard = $this->customerSession->isLoggedIn();

        $settings = new \CDev\XPaymentsCloud\Transport\WidgetSettings(
            $this->scopeConfig,
            $this->storeManager,
            $this->cartHelper
        );

        $settings->setShowSaveCard($showSaveCard)
            ->setTokenizeCard(false)
            ->setAutoSubmit(false)
            ->setCustomerId($this->getXpaymentsCustomerId())
            ->setForm('#co-payment-form')
            ->setTokenInputName('payment[xpayments_token]')
            ->setTotal($total);

        return array(
            'payment' => array(
                $this->method::CODE => $settings->getData()
            ),
        );
    }
}
