# X-Payments Cloud connector for Magento 2
This extension connects your Magento 2 store with X-Payments Cloud - a PSD2/SCA ready PCI Level 1 certified credit card processing app which allows you to store customers’ credit card information and still be compliant with PCI security mandates.

The credit card form can be embedded right into checkout page, so your customers won’t leave your site to complete an order.

### Installation
Via composer:
```sh
composer.phar require cdev/x-payments-cloud
bin/magento module:enable CDev_XPaymentsCloud
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
```
Or copy these files into the `<mage-dir>/app/code/CDev/XPaymentsCloud/` directory and run:
```sh
bin/magento setup:upgrade
bin/magento module:enable CDev_XPaymentsCloud
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
```
### Configuration
First, you shuld create the X-Payments Cloud account.
 - Navigate **Stores -> Configuration -> Sales -> Payment Methods**
 - Expand the **X-Payments Cloud** section
 - Start the signup process to create an X-Payments Cloud account and follow the Wizard instructions
 - In the end you will be prompted to set a password. Then complete 2-step user authentication setup for your account
 
 Now X-Payments Cloud payment method is available at checkout in demo mode. Just don't forget to enable it.
 To proceed with accepting real payments get back to the X-Payments Cloud payment method configuration and:
  - Select the necessary payment gateway from the **Add payment configuration** list
  - Enter you gateway credentials and adjust the necessary settings specific to this payment gateway
  
### Refference
 - [X-Payments Cloud API](https://xpayments.stoplight.io/docs/server-side-api/)
 - [X-Payments Cloud PHP SDK](https://github.com/xpayments/cloud-sdk-php)
 
### Support
If you have any questions please free to [contact us](https://www.x-payments.com/contact-us).
