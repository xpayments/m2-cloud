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

namespace CDev\XPaymentsCloud\Observer\Payment;

/**
 * Data assign observer for X-Payments Cloud payment method
 */
class DataAssignObserver extends \Magento\Payment\Observer\AbstractDataAssignObserver
{
    /**
     * Assign data
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(\Magento\Quote\Api\Data\PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!empty($additionalData['xpayments_token'])) {
            $paymentInfo = $method->getInfoInstance();
            $paymentInfo->setAdditionalInformation('xpayments_token', $additionalData['xpayments_token']);
        }
    }
}
