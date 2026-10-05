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
 * https://opensource.org/license/OSL-3.0
 *
 * @author     X-Cart Holdings LLC <info@x-cart.com>
 * @category   CDev
 * @package    CDev_XPaymentsCloud
 * @copyright  (c) 2010-present X-Cart Holdings LLC <info@x-cart.com>. All rights reserved
 * @license    https://opensource.org/license/OSL-3.0  Open Software License (OSL 3.0)
 */

namespace CDev\XPaymentsCloud\Model\Order;

/**
 * Auth/Capture/Sale logic
 */
class Payment extends \Magento\Sales\Model\Order\Payment
{
    /**
     * Check if it was a Sale action
     *
     * @return bool
     */
    protected function isSaleAction()
    {
        $transId = $this->getOrder()->getPayment()->getLastTransId();

        return 'sale' == substr($transId, -4);
    }

    /**
     * Implement Auth/Capture/Sale logic
     * This method is called for all actions for authorize_capture Payment Action:
     *  - authorize only
     *  - authorize and capture (sale)
     *  - capture (after initial auth)
     *
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     *
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface
     */
    public function capture($invoice = null)
    {
        $order = $this->getOrder();

        if ('xpayments_cloud' !== $order->getPayment()->getMethodInstance()->getCode()) {
            return parent::capture($invoice);
        }

        if (!$order->getState()) {

            // Order is just created
            // Execute 'pay' API action

            $this->authorize(true, $order->getBaseTotalDue());
            $this->setAmountAuthorized($order->getTotalDue());

            if ($this->isSaleAction()) {

                // This was a Sale action
                // So create Invoice and mark it as Paid

                $invoice = $this->getOrder()->prepareInvoice();
                $invoice->register();
                $invoice->setTransactionId($this->getOrder()->getPayment()->getLastTransId());
                $invoice->pay();

                $this->getOrder()->addRelatedObject($invoice);
            }

        } elseif (null === $invoice) {

            // Order State is 'processing' or similar
            // Execute 'capture online' operation via Invoice creation

            $invoice = $this->_invoice();
            $this->setCreatedInvoice($invoice);

        } else {

            // Execute 'capture' API action

            parent::capture($invoice);
        }

        return $this;
    }
}
