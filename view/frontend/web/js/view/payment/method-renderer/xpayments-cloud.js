// vim: set ts=2 sw=2 sts=2 et:
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
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/checkout-data',
        'xpayments/base',
    ],
    function (Component, checkoutData) {
        'use strict';

        return Component.extend({

            /**
             * Component defaults
             */
            defaults: {
                template: 'CDev_XPaymentsCloud/payment/form',
            },

            /**
             * Get payment method code
             *
             * @return string
             */
            getCode: function() {
                return 'xpayments_cloud';
            },

            /**
             * Check if payment method is active
             *
             * @return bool
             */
            isActive: function() {

                var active = (this.getCode() === this.isChecked());

                window.xpayments.onSwitchPaymentMethod();

                return active;
            },

            /**
             * Initialize payment method
             *
             * @return void
             */
            initialize: function()
            {
                this._super();

                var self = this;

                /**
                 * Check if X-Payments Cloud is currently selected payment method
                 *
                 * @return bool
                 */
                XPayments.prototype.isCurrent = function () {
                    return 'undefined' != typeof checkoutData
                        && 'xpayments_cloud' == checkoutData.getSelectedPaymentMethod();
                }

                /**
                 * Toggle Apple Pay button
                 *
                 * @return bool
                 */
                XPayments.prototype.toggleApplePayButton = function (isApple) {
                    $('xpayments-place-order').toggleClassName('apple-pay-button', isApple);
                }

                /**
                 * On success event
                 *
                 * @return void
                 */
                XPayments.prototype.onSuccess = XPayments.prototype.onSuccess.wrap(
                    function (parentMethod, params) {

                        self.xpaymentsToken = params.token;

                        parentMethod(params);
                    }
                ); 

                window.xpayments = new XPayments(window.checkoutConfig.payment.xpayments_cloud);

                window.xpayments.load();
            },

            /**
             * Get payment method data which is send to controller after order is submitted
             *
             * @return JSON
             */
            getData: function()
            {
                return {
                    method: this.item.method,
                    additional_data: {
                        xpayments_token: this.xpaymentsToken,
                    }
                };
            },

            /**
             * Place order
             *
             * @return bool
             */
            placeOrder: function ()
            {
                window.xpayments.onSubmitPayment(this._super.bind(this));
            },
        });
    }
);
