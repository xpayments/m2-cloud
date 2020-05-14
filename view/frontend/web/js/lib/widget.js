/*
 * X-Payments Cloud SDK - Payment Widget
 */

function XPaymentsWidget()
{
    this.serverDomain = 'xpayments.com';
    this.messageNamespace = 'xpayments.widget.';
    this.receiverNamespace = 'xpayments.checkout.';
    this.widgetId = this.generateId();
    this.previousHeight = -1;
    this.applePaySession = null;
    this.paymentMethod = null;

    this.config = {
        debug: false,
        account: '',
        widgetKey: '',
        container: '',
        form: '',
        language: '',
        customerId: '',
        showSaveCard: true,
        enableApplePay: true,
        company: {
            name: '',
            domain: document.location.hostname,
            countryCode: '',
        },
        order: {
            tokenizeCard: false,
            total: -1,
            currency: ''
        }
    }

    this.handlers = {};

    this.bindedListener = false;
    this.bindedSubmit = false;

}

XPaymentsWidget.prototype.on = function(event, handler)
{
    if ('formSubmit' !== event) {

        this.handlers[event] = handler.bind(this);

    } else {
        var formElm = this.getFormElm();

        if (formElm) {
            if (this.bindedSubmit) {
                formElm.removeEventListener('submit', this.bindedSubmit);
            }
            this.bindedSubmit = handler.bind(this);
            formElm.addEventListener('submit', this.bindedSubmit);
        }
    }

    return this;
}


XPaymentsWidget.prototype.trigger = function(event, params)
{
    if ('function' === typeof this.handlers[event]) {
        this.handlers[event](params);
    }

    return this;
}

XPaymentsWidget.prototype.init = function(settings)
{
  for (var key in settings) {
      if ('undefined' !== typeof this.config[key]) {
          if ('object' === typeof this.config[key]) {
              for (var subkey in settings[key]) {
                  if ('undefined' !== typeof this.config[key][subkey]) {
                      this.config[key][subkey] = settings[key][subkey];
                  }
              }
          } else {
              this.config[key] = settings[key];
          }
      }
  }

  if (this.config.order.tokenizeCard) {
      this.config.showSaveCard = false;
  }

  // Set default handlers
  // other events: fail, loaded, unloaded, submitReady

  this.on('formSubmit', function (domEvent) {
      // "this" here is the widget
      this.submit();
      domEvent.preventDefault();
  }).on('success', function(params) {
      var formElm = this.getFormElm();
      if (formElm) {
          var input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'xpaymentsToken';
          input.value = params.token;
          formElm.appendChild(input);
          formElm.submit();
      }
  }).on('alert', function(params) {
      window.alert(params.message);
  });

  this.bindedListener = this.messageListener.bind(this);
  window.addEventListener('message', this.bindedListener);

  if (
      'undefined' !== typeof settings.autoload
      && settings.autoload
  ) {
      this.load();
  }

  return this;
}

XPaymentsWidget.prototype.generateId = function()
{
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

XPaymentsWidget.prototype.getIframeId = function()
{
    return 'xpayments-' + this.widgetId;
}

XPaymentsWidget.prototype.getIframeElm = function()
{
    return document.getElementById(this.getIframeId());
}

XPaymentsWidget.prototype.getContainerElm = function()
{
    return this.safeQuerySelector(this.config.container);
}

XPaymentsWidget.prototype.getFormElm = function()
{
    return this.safeQuerySelector(this.config.form);
}

XPaymentsWidget.prototype.isValid = function()
{
    return this.getIframeElm() && this.getFormElm();
}

XPaymentsWidget.prototype.safeQuerySelector = function(selector)
{
    var elm = false;
    if (selector) {
        elm = document.querySelector(selector);
    }
    return elm;
}

XPaymentsWidget.prototype.load = function()
{
    var containerElm = this.getContainerElm();
    if (!containerElm) {
        return this;
    }

    var elm = this.getIframeElm();
    if (!elm) {
        elm = document.createElement('iframe');
        elm.id = this.getIframeId();
        elm.style.width = '100%';
        elm.style.height = '0';
        elm.style.overflow = 'hidden';
        elm.style.border = 'none';
        elm.setAttribute('scrolling', 'no');
        containerElm.appendChild(elm);
    }

    var url =
        this.getServerUrl() + '/payment.php' +
        '?widget_key=' + encodeURIComponent(this.config.widgetKey) +
        '&widget_id=' + encodeURIComponent(this.widgetId);
    if (this.config.customerId) {
        url += '&customer_id=' + encodeURIComponent(this.config.customerId);
    }
    if (this.config.language) {
        url += '&language=' + encodeURIComponent(this.config.language);
    }
    elm.src = url;

    return this;
}

XPaymentsWidget.prototype.getServerHost = function()
{
    return this.config.account + '.' + this.serverDomain;
}

XPaymentsWidget.prototype.getServerUrl = function()
{
    return 'https://' + this.getServerHost();
}

XPaymentsWidget.prototype.submit = function()
{
    this._sendEvent('submit');
}

XPaymentsWidget.prototype._afterLoad = function(params)
{
    this.showSaveCard();
    if (this._isApplePayAvailable() && this.config.enableApplePay) {
      this._sendEvent('applepay.enable');
    }
    this.setOrder();
    this.resize(params.height);
}

XPaymentsWidget.prototype.getPaymentMethod = function()
{
    return this.paymentMethod;
}

XPaymentsWidget.prototype._paymentMethodChange = function(params)
{
    this.paymentMethod = params.newId;
}

XPaymentsWidget.prototype._applePayValidated = function(params)
{
    this.applePaySession.completeMerchantValidation(params.data);
}

XPaymentsWidget.prototype._applePayCompleted = function(params)
{
    this.applePaySession.completePayment({ status: ApplePaySession.STATUS_SUCCESS, errors: [] });
}

XPaymentsWidget.prototype._applePayError = function(params)
{
    this.applePaySession.abort();
}

XPaymentsWidget.prototype._applePayStart = function(params)
{
    var request = {
        countryCode: this.config.company.countryCode,
        currencyCode: this.config.order.currency,
        supportedNetworks: params.supportedNetworks,
        merchantCapabilities: ['supports3DS'],
        total: {
            label: this.config.company.name,
            amount: this.config.order.total
        },
    };

    this.applePaySession = new ApplePaySession(3, request);

    this.applePaySession.onvalidatemerchant = (function(event) {
        this._sendEvent('applepay.validatemerchant', {
            validationURL: event.validationURL,
            displayName: this.config.company.name,
            context: this.config.company.domain,
        });
    }).bind(this);

    this.applePaySession.onpaymentauthorized = (function(event) {
        this._sendEvent('applepay.paymentauthorized', { payment: event.payment });
    }).bind(this);

    this.applePaySession.oncancel = (function(event) {
        this._sendEvent('applepay.cancel');
    }).bind(this);

    this.applePaySession.begin();

}

XPaymentsWidget.prototype._isApplePayAvailable = function() {
    return (window.ApplePaySession && ApplePaySession.canMakePayments());
}

XPaymentsWidget.prototype._checkApplePayActiveCard = function(params)
{
    var promise = ApplePaySession.canMakePaymentsWithActiveCard(params.merchantId);
    promise.then((function (canMakePayments) {
        if (canMakePayments) {
            this._sendEvent('applepay.select');
        }
    }).bind(this));
}

XPaymentsWidget.prototype.showSaveCard = function(value)
{
    if ('undefined' === typeof value) {
        value = this.config.showSaveCard;
    } else {
        this.config.showSaveCard = (true === value);
    }
    this._sendEvent('savecard', { show: value });
}


XPaymentsWidget.prototype.refresh = function()
{
    this._sendEvent('refresh');
}

XPaymentsWidget.prototype.resize = function(height)
{
    var elm = this.getIframeElm();
    if (elm) {
        this.previousHeight = elm.style.height;
        elm.style.height = height + 'px';
    }
}

XPaymentsWidget.prototype.setOrder = function(total, currency)
{
    if ('undefined' !== typeof total) {
        this.config.order.total = total;
        this.config.order.currency = currency;
    }
    this._sendEvent('details', {
        tokenizeCard: this.config.order.tokenizeCard,
        total: this.config.order.total,
        currency: this.config.order.currency
    });
}

XPaymentsWidget.prototype.destroy = function()
{
    if (this.bindedListener) {
        window.removeEventListener('message', this.bindedListener);
    }

    var formElm = this.getFormElm();
    if (this.bindedSubmit && formElm) {
        formElm.removeEventListener('submit', this.bindedSubmit);
    }

    var containerElm = this.getContainerElm();
    if (containerElm) {
        var elm = this.getIframeElm();
        if (elm && containerElm.contains(elm)) {
            containerElm.removeChild(elm);
        }
    }
}

XPaymentsWidget.prototype.messageListener = function(event)
{
    if (window.JSON) {
        var msg = false;
        if (-1 !== this.getServerUrl().toLowerCase().indexOf(event.origin.toLowerCase())) {
            try {
                msg = window.JSON.parse(event.data);
            } catch (e) {
                // Skip invalid messages
            }
        }

        if (
            msg &&
            msg.event &&
            0 === msg.event.indexOf(this.messageNamespace)
        ) {
            this._log('X-Payments Event: ' + msg.event + "\n" + window.JSON.stringify(msg.params));

            var eventType = msg.event.substr(this.messageNamespace.length);

            if ('loaded' === eventType) {
                this._afterLoad(msg.params);
            } else if ('applepay.start' === eventType) {
                this._applePayStart(msg.params);
            } else if ('applepay.checkactivecard' === eventType) {
                this._checkApplePayActiveCard(msg.params);
            } else if ('applepay.merchantvalidated' === eventType) {
                this._applePayValidated(msg.params);
            } else if ('applepay.completed' === eventType) {
                this._applePayCompleted(msg.params);
            } else if ('applepay.error' === eventType) {
                this._applePayError(msg.params);
            } else if ('paymentmethod.change' === eventType) {
                this._paymentMethodChange(msg.params);
            } else if ('resize' === eventType) {
                this.resize(msg.params.height);
            } else if ('alert' === eventType) {
                msg.params.message =
                    ('string' === typeof msg.params.message)
                    ? msg.params.message.replace(/<\/?[^>]+>/gi, '')
                    : '';
            }

            this.trigger(eventType, msg.params);
        }

    }
}

XPaymentsWidget.prototype._log = function(msg)
{
    if (this.config.debug) {
        console.log(msg);
    }
}

XPaymentsWidget.prototype._sendEvent = function(eventName, eventParams)
{
    if ('undefined' === typeof eventParams) {
        eventParams = {};
    }

    this._postMessage({
        event: this.receiverNamespace + eventName,
        params: eventParams
    })
}

XPaymentsWidget.prototype._postMessage = function(message)
{
    var elm = this.getIframeElm();
    if (
        window.postMessage
        && window.JSON
        && elm
        && elm.contentWindow
    ) {
        this._log('Sent to X-Payments: ' + message.event + "\n" + window.JSON.stringify(message.params));
        elm.contentWindow.postMessage(window.JSON.stringify(message), '*');
    } else {
        this._log('Error sending message - iframe wasn\'t initialized!');
    }
}
