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

namespace CDev\XPaymentsCloud\Transport;

/**
 * Transport for widget settings
 */
class WidgetSettings extends DataObject
{
    /**
     * X-Payments cart helper
     *
     * @var \CDev\XPaymentsCloud\Helper\Cart
     */
    protected $cartHelper = null;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \CDev\XPaymentsCloud\Helper\Cart $cartHelper
     *
     * @return \Magento\Framework\DataObject
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \CDev\XPaymentsCloud\Helper\Cart $cartHelper
    ) {

        $this->cartHelper = $cartHelper;

        $data = array(
            'account'      => $scopeConfig->getValue('payment/xpayments_cloud/account'),
            'widgetKey'    => $scopeConfig->getValue('payment/xpayments_cloud/widget_key'),
            'devUrl'       => (string)$scopeConfig->getValue('payment/xpayments_cloud/dev_url'),
            'container'    => '#xpayments-iframe-container',
            'tokenInputId' => 'xpayments-token',
            'language'     => 'en',
            'customerId'   => '',
            'autoload'     => false,
            'autoSubmit'   => false,
            'debug'        => (bool)$scopeConfig->getValue('payment/xpayments_cloud/debug'),
            'order' => array(
                'currency' => $storeManager->getStore()->getCurrentCurrency()->getCode(),
            ),
            'company' => array(
                'name'        => $scopeConfig->getValue('general/store_information/name'),
                'countryCode' => $scopeConfig->getValue('general/country/default'),
            ),
        );

        return $this->setData($data);
    }

    /**
     * Set order total
     *
     * @param float|string $value
     *
     * @return \Magento\Framework\DataObject
     */
    public function setTotal($value)
    {
        $order = $this->getOrder();

        $order['total'] = $this->cartHelper->preparePrice($value);

        return $this->setOrder($order);
    }

    /**
     * Set tokenize card flag
     *
     * @param bool $value
     *
     * @return \Magento\Framework\DataObject
     */
    public function setTokenizeCard($value)
    {
        $order = $this->getOrder();

        $order['tokenizeCard'] = (bool)$value;

        return $this->setOrder($order);
    }
}
