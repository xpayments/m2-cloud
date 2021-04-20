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
 * @category   Cdev
 * @package    Cdev_XPaymentsCloud
 * @copyright  (c) 2010-present Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

window.XPaymentsLoader = Class.create();

window.XPaymentsLoader.prototype = {

    /**
     * ID of the loader element
     */
    elementId: 'xpayments-loader',

    /**
     * Constructor. Create loader element
     */
    initialize: function (container, elementId) {

        if ('undefined' != typeof elementId) {
            this.elementId = elementId;
        }

        if ($(this.elementId)) {
            return this;
        }

        container = document.querySelector(container);

        if (container) {

            var loaderIcon = document.createElement('div');
            loaderIcon.id = 'xpayments-loader-icon';

            var loader = document.createElement('div');
            loader.id = 'xpayments-loader';
            loader.appendChild(loaderIcon);

            container.appendChild(loader);
        }

        return this;
    },

    /**
     * Show loader
     */
    show: function () {

        if ($(this.elementId)) {
            $(this.elementId).show();
        }

        return this;
    },

    /**
     * Hide loader
     */
    hide: function () {

        if ($(this.elementId)) {
            $(this.elementId).hide();
        }

        return this;
    }
};

/**
 * Initialize X-Payments widget
 */
window.XPaymentsWidget.prototype.init = XPaymentsWidget.prototype.init.wrap(
    function(parentMethod, settings) {

         Object.extend(
            this.config,
            {
                devUrl: '',
            }
        );

        parentMethod(settings);

        return this;
    }
);

/**
 * Redefine server URL (if necessary)
 */
window.XPaymentsWidget.prototype.getServerUrl = XPaymentsWidget.prototype.getServerUrl.wrap(
    function (parentMethod) {
    
        var devUrl = this.config.devUrl;

        if (devUrl) {

            if ('/' === devUrl.substr(-1)) {
                devUrl = devUrl.substr(0, devUrl.length - 1);
            }

            var url = devUrl;

        } else {

            var url = parentMethod();
        }

        return url;
    }
);

window.XPayments = Class.create();

window.XPayments.prototype = {

    /**
     * Original submit payment form function
     */
    origSubmitPayment: function () {
        return false;
    },

    /**
     * Check if X-Payments Cloud is currently used payment method
     */
    isCurrent: function () {
        return false;
    },

    /**
     * Toggle Apple Pay button
     */
    toggleApplePayButton: function (isApple) {
        return false;
    },

    /**
     * Constructor
     */
    initialize: function (settings) {
    
        this.settings = settings;

        this.loader = new XPaymentsLoader(this.settings.container);
        this.getWidget();
    },

    /**
     * Initialize widget
     */
    getWidget: function () {
    
        try {

            if (!window._widgetInstance) {

                window._widgetInstance = new XPaymentsWidget();
                window._widgetInstance.init(this.settings);

                window._widgetInstance.on(
                    'success', 
                     (function (params) { this.onSuccess(params); }).bind(this)
                );

                window._widgetInstance.on(
                    'fail', 
                    (function () { this.onFail(); }).bind(this)
                );

                window._widgetInstance.on(
                    'loaded', 
                    (function () { this.onLoaded(); }).bind(this)
                );

                window._widgetInstance.on(
                    'paymentmethod.change',
                    (function (params) { this.toggleApplePayButton('apple_pay' === params.newId); }).bind(this)
                );
            }

        } catch (error) {

            console.error(error);
        }

        return window._widgetInstance;
    },

    /**
     * Get input for X-Payments token
     */
    getTokenInput: function () {
    
        var input = $(this.settings.tokenInputId);

        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = this.settings.tokenInputName;
            input.id = this.settings.tokenInputId;
            
            this.getWidget().getFormElm().appendChild(input);
        }

        return input;
    },

    /**
     * Load X-Payments Cloud widget
     */
    load: function () {
        this.loader.show();
        this.getWidget().load();
    },

    /**
     * Process widget loaded event
     */
    onLoaded: function () {
        this.loader.hide();
    },

    /**
     * Switch payment method event
     */
    onSwitchPaymentMethod: function () {
        if (this.isCurrent()) {
            this.load();
        } else {
            this.getWidget().resize(0);
        }
    },

    /**
     * Submit payment form event
     */
    onSubmitPayment: function (origSubmitPayment) {

        if ('function' == typeof origSubmitPayment) {
            this.origSubmitPayment = origSubmitPayment;
        }

        if (this.isCurrent()) {
            this.loader.show();
            this.getWidget().submit();
        } else {
            this.origSubmitPayment();
        }
    },

    /**
     * Successfully tokenized payment event
     */
    onSuccess: function (params) {

        var formElm = this.getWidget().getFormElm();

        if (formElm) {

            var inputElm = this.getTokenInput();

            inputElm.value = params.token;

            if (this.settings.autoSubmit) {
                formElm.submit();
            } else {
                this.origSubmitPayment();
            }
        }
    },

    /**
     * Something went wronng
     */
    onFail: function (params) {
        this.loader.hide();
    }
};
