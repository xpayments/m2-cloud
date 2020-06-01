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

namespace CDev\XPaymentsCloud\Model\Payment\Method;

/**
 * X-Payments Cloud payment method
 */
class XPaymentsCloud extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment method code
     */
    const CODE = 'xpayments_cloud';

    /**
     * Payment method code
     */
    protected $_code = self::CODE;

    /**
     * Payment Info block
     */
    protected $_infoBlockType = \CDev\XPaymentsCloud\Block\Payment\Info::class;

    /**
     * Payment method flags
     */
    protected $_isGateway               = false;

    protected $_canUseCheckout          = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;

    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;

    protected $_canVoid                 = true;

    protected $_canReviewPayment        = true;    

    /**
     * Customer repository 
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository = null;

    /**
     * Customer model
     *
     * @var \Magento\Sales\Api\Data\CustomerInterface
     */
    protected $customer = null;

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder = null;

    /**
     * Cart helper
     *
     * @var \CDev\XPaymentsCloud\Helper\Cart
     */
    protected $cartHelper = null;

    /** 
     * SDK Client helper    
     *
     * @var \CDev\XPaymentsCloud\Helper\Client
     */
    protected $clientHelper = null;

    /**
     * Logger
     *
     * @var \CDev\XPaymentsCloud\Logger\Logger
     */
    protected $xpaymentsLogger = null;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \CDev\XPaymentsCloud\Helper\Cart $cartHelper
     * @param \CDev\XPaymentsCloud\Helper\Client $clientHelper
     * @param \CDev\XPaymentsCloud\Logger\Logger $xpaymentsLogger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param Magento\Directory\Helper\Data $directory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\UrlInterface $urlBuilder,
        \CDev\XPaymentsCloud\Helper\Cart $cartHelper,
        \CDev\XPaymentsCloud\Helper\Client $clientHelper,
        \CDev\XPaymentsCloud\Logger\Logger $xpaymentsLogger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Directory\Helper\Data $directory = null
    ) {

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );

        $this->customerRepository = $customerRepository;

        $this->urlBuilder = $urlBuilder;

        $this->cartHelper = $cartHelper;
        $this->clientHelper = $clientHelper;

        $this->xpaymentsLogger = $xpaymentsLogger;
    }

    /**
     * Get xpid from parent transaction
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return string
     */
    protected function getParentXpid(\Magento\Payment\Model\InfoInterface $payment)
    {
        return substr($payment->getParentTransactionId(), 0, 32);
    }

    /**
     * Get xpid from last transaction
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return string
     */
    protected function getLastXpid(\Magento\Payment\Model\InfoInterface $payment)
    {
        return substr($payment->getLastTransId(), 0, 32);
    }

    /**
     * Get customer
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return \Magento\Sales\Api\Data\CustomerInterface
     */
    protected function getCustomer(\Magento\Payment\Model\InfoInterface $payment)
    {
        if (
            null === $this->customer
            && $payment->getOrder()->getCustomerId()
        ) {
            $this->customer = $this->customerRepository
                ->getById($payment->getOrder()->getCustomerId());
        }

        return $this->customer;
    }

    /**
     * Get X-Payments customer ID
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return string 
     */
    protected function getXpaymentsCustomerId(\Magento\Payment\Model\InfoInterface $payment)
    {
        $xpaymentsCustomerId = '';

        if ($this->getcustomer($payment)) {

            $attribute = $this->getCustomer($payment)
                ->getCustomAttribute('xpayments_customer_id');

            if ($attribute) {
                $xpaymentsCustomerId = $attribute->getValue();
            }
        }

        return $xpaymentsCustomerId;
    }

    /**
     * Set X-Payments customer ID
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param string $xpaymentsCustomerId
     *
     * @return void 
     */
    protected function setXpaymentsCustomerId(\Magento\Payment\Model\InfoInterface $payment, $xpaymentsCustomerId)
    {
        if ($this->getcustomer($payment)) {

            $payment->setXpaymentsCustomerId($xpaymentsCustomerId);

            // TODO: Rework via fieldsets
            $this->getCustomer($payment)
                ->setCustomAttribute('xpayments_customer_id', $xpaymentsCustomerId);

            $this->customerRepository->save($this->getCustomer($payment));
        }

        return $xpaymentsCustomerId;
    }

    /**
     * Compose URL for action
     *
     * @param string $action
     *
     * @return string
     */
    protected function composeUrl($action)
    {
        return $this->urlBuilder->getUrl('xpayments_cloud/processing/' . $action);
    }

    /**
     * Authorize payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return $this
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try {

            $cart = $this->cartHelper
                ->prepareCart($payment->getOrder());

            $refId = $payment->getOrder()->getIncrementId();

            $token = $payment->getAdditionalInformation()['xpayments_token'];

            $response = $this->clientHelper
                ->getClient()
                ->doPay(
                    $token,
                    $refId,
                    $this->getXpaymentsCustomerId($payment),
                    $cart,
                    $this->composeUrl('return'),
                    $this->composeUrl('callback')
                );

            $this->processResponse($response, $payment);

        } catch (\Exception $exception) {

            $message = $this->xpaymentsLogger->processException($exception);

            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        return $this;
    }

    /**
     * Capture specified amount for payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try {

            $response = $this->clientHelper
                ->getClient()
                ->doCapture(
                    $this->getParentXpid($payment),
                    $amount
                );

            $this->processResponse($response, $payment);

        } catch (\Exception $exception) {

            $message = $this->xpaymentsLogger->processException($exception);

            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        return $this;
    }

    /**
     * Refund specified amount for payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return $this
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        try {

            $response = $this->clientHelper
                ->getClient()
                ->doRefund(
                    $this->getParentXpid($payment),
                    $amount
                );

            $this->processResponse($response, $payment);

        } catch (\Exception $exception) {

            $message = $this->xpaymentsLogger->processException($exception);

            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        return $this;
    }

    /**
     * Void payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return $this
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        try {

            $response = $this->clientHelper
                ->getClient()
                ->doVoid($this->getParentXpid($payment));

            $this->processResponse($response, $payment);

        } catch (\Exception $exception) {

            $message = $this->xpaymentsLogger->processException($exception);

            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        return $this;
    }

    /**
     * Cancel payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return $this
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->void($payment);
    }

    /**
     * Accept payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return bool
     */
    public function acceptPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        try {

            $xpid = $this->getLastXpid($payment);

            $response = $this->clientHelper
                ->getClient()
                ->doAccept($xpid);

            $result = (bool)$response->result;

        } catch (\Exception $exception) {

            $message = $this->xpaymentsLogger->processException($exception);

            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        return $result;
    }

    /**
     * Decline payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return bool
     */
    public function denyPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        try {

            $xpid = $this->getLastXpid($payment);

            $response = $this->clientHelper
                ->getClient()
                ->doDecline($xpid);

            $result = (bool)$response->result;

        } catch (\Exception $exception) {

            $message = $this->xpaymentsLogger->processException($exception);

            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        return $result;
    }

    /**
     * Process X-Payments Cloud payment response
     *
     * @param \XPaymentsCloud\Response $response
     * @param \Magento\Payment\Model\InfoInterface $payment 
     *
     * @return Cdev_XPaymentsCloud_Model_Payment_Cloud
     */
    protected function processResponse(
        \XPaymentsCloud\Response $response,
        \Magento\Payment\Model\InfoInterface $payment
    ) {
        $info = $response->getPayment();

        // Compose transaction ID preventing duplicates
        $transactionId = sprintf('%s-%s', $info->xpid, $info->lastTransaction->action);

        // Set some basic information about the payment
        $payment->setStatus(self::STATUS_APPROVED)
            ->setCcTransId($info->lastTransaction->txnId)
            ->setLastTransId($info->lastTransaction->txnId)
            ->setTransactionId($transactionId)
            ->setIsTransactionClosed(false)
            ->setAmount($info->amount)
            ->setShouldCloseParentTransaction(false);

        // Set information about the card
        $payment->setCcLast4($info->card->last4)
            ->setCcFirst6($info->card->first6)
            ->setCcType($info->card->type)
            ->setCcExpMonth($info->card->expireMonth)
            ->setCcExpYear($info->card->expireYear)
            ->setCcOwner($info->card->cardholderName);

        // Set transaction details
        if (!empty($info->details)) {

            $details = array_filter(
                get_object_vars($info->details)
            );

            $payment->setTransactionAdditionalInfo(
                \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                $details
            );
        }

        // Set fraudulent flag
        $payment->setIsTransactionPending(
            (bool)$info->isFraudulent
        );

        // Set X-Payments Cloud customerId
        $this->setXpaymentsCustomerId($payment, $info->customerId);
        
        return $this;
    }
}
