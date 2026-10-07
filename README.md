# X-Payments Cloud connector for Magento 2
This extension connects your Magento 2 store with X-Payments Cloud - a PSD2/SCA ready PCI Level 1 certified credit card processing app which allows you to store customers' credit card information and still be compliant with PCI security mandates.

The credit card form is embedded right into the checkout page, so your customers don't leave your site to complete an order. Card details are entered in an iframe served by X-Payments Cloud and never reach your Magento server.

### Requirements
- Magento 2.4 (`magento/module-payment` 100.4.*)
- PHP 7.4 or 8.1
- The [X-Payments Cloud PHP SDK](https://github.com/xpayments/cloud-sdk-php), installed automatically by Composer

### Installation
Via Composer:
```sh
composer require cdev/x-payments-cloud
bin/magento module:enable CDev_XPaymentsCloud
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
```
Or copy these files into the `<mage-dir>/app/code/CDev/XPaymentsCloud/` directory, install the SDK with `composer require xpayments/cloud-sdk-php`, and run:
```sh
bin/magento module:enable CDev_XPaymentsCloud
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
```

### Configuration
First, create the X-Payments Cloud account:
 - Navigate to **Stores -> Configuration -> Sales -> Payment Methods**
 - Expand the **X-Payments Cloud** section
 - Start the signup process to create an X-Payments Cloud account and follow the wizard instructions
 - At the end you will be prompted to set a password. Then complete the 2-step user authentication setup for your account

X-Payments Cloud is now available at checkout in demo mode. Don't forget to enable it.

To start accepting real payments, go back to the X-Payments Cloud payment method configuration and:
  - Select the necessary payment gateway from the **Add payment configuration** list
  - Enter your gateway credentials and adjust the settings specific to this payment gateway

Refer to the manual for detailed instructions: https://www.x-payments.com/wp-content/uploads/help_uploads/Using-X-Payments-Cloud-with-Magento-2.pdf

### Supported payment gateways
X-Payments Cloud supports more than 50 payment gateway integrations, including: American Express Web-Services API Integration, Authorize.Net, Bambora (Beanstream), Bendigo Bank, BluePay, BlueSnap Payment API (XML), Braintree, BluePay Canada (Caledon), Cardinal Commerce Centinel, Chase Paymentech, BAC Credomatic, CyberSource - SOAP Toolkit API, X-Payments Demo Pay, X-Payments Demo Pay 3-D Secure, DIBS, DirectOne - Direct Interface, eProcessing Network - Transparent Database Engine, SecurePay Australia, Moneris eSELECTplus, eWAY Rapid - Direct Connection, Sparrow (5th Dimension Gateway), Global Payments, GoEmerchant - XML Gateway API, HeidelPay, Innovative Gateway, iTransact XML, Payment XP (Meritus) Web Host, NAB - National Australia Bank, NMI (Network Merchants Inc.), Netbilling - Direct Mode, Netevia, Ingenico ePayments (Ogone e-Commerce), PayGate South Africa, PayPal REST API, PSiGate XML API, QuantumGateway - XML Requester, Intuit QuickBooks Payments, QuickPay, Worldpay Corporate Gateway - Direct Model, Opayo Direct (ex. Sage Pay Go - Direct Interface), Paya (ex. Sage Payments US), Simplify Commerce by MasterCard, SkipJack, Stripe, TranSafe, powered by Monetra, 2Checkout, USA ePay - Transaction Gateway API, Elavon Converge (ex VirtualMerchant), WebXpress, Worldpay Total US, Worldpay US (Lynk Systems).

### Supported fraud-screening services
 - Kount
 - NoFraud
 - Signifyd

### Reference
 - [X-Payments Cloud API](https://xpayments.stoplight.io/docs/server-side-api/spsxnj22ewcd7-x-payments-cloud-api-overview)
 - [X-Payments Cloud PHP SDK](https://github.com/xpayments/cloud-sdk-php)
 - [X-Payments development docs](https://support.x-cart.com/en/articles/5842415-x-payments-development-docs)
 - [X-Payments Cloud for Magento 2](https://www.x-payments.com/features/magento2)

### License
Open Software License 3.0 ([LICENSE.txt](LICENSE.txt)); Academic Free License 3.0 ([LICENSE_AFL.txt](LICENSE_AFL.txt)).

Copyright (c) 2010-present X-Cart Holdings LLC.

### Support
If you have any questions, please [contact us](https://www.x-payments.com/contact-us).
