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

namespace CDev\XPaymentsCloud\Block\Payment;

/**
 * Payment info block
 */
class Info extends \Magento\Payment\Block\ConfigurableInfo
{
    /**
     * Name of the X-Payments card field
     */
    protected const XPAYMENTS_CARD = 'xpayments_card';

    /**
     * Placeholder for hidden numbers in card number
     */
    protected const PLACEHOLDER = '&#8226;';

    /**
     * Returns label
     *
     * @param string $field
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getLabel($field)
    {
        if (self::XPAYMENTS_CARD == $field) {
            $field = 'Payment Card';
        }

        return parent::getLabel($field);
    }

    /**
     * Prepare payment information
     *
     * @param \Magento\Framework\DataObject|array|null $transport
     *
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);

        $payment = $this->getInfo();

        $first6 = $payment->getCcFirst6() ?: str_repeat(self::PLACEHOLDER, 6);

        $middleLength = ('AMEX' === $payment->getCcType()) ? 5 : 6;
        $middle = str_repeat(self::PLACEHOLDER, $middleLength);

        $card = sprintf(
            '[%s]&nbsp;%s%s%s (%s/%s)',
            $payment->getCcType(),
            $first6, $middle, $payment->getCcLast4(),
            $payment->getCcExpMonth(), $payment->getCcExpYear()
        );

        $this->setDataToTransfer(
            $transport,
            self::XPAYMENTS_CARD,
            $card
        );

        return $transport;
    }
}
