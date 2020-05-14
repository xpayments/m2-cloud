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

namespace CDev\XPaymentsCloud\Transport;

/**
 * Transport for connect settings
 */
class ConnectSettings extends DataObject
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */ 
    protected $request = null;

    /**
     * Backend URL builder
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl = null;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     *
     * @return \Magento\Framework\DataObject 
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        $this->request = $request;
        $this->backendUrl = $backendUrl;

        $data = array(
            'account'        => $scopeConfig->getValue('payment/xpayments_cloud/account'),
            'quickAccessKey' => $scopeConfig->getValue('payment/xpayments_cloud/quick_access_key'),
            'devUrl'         => (string)$scopeConfig->getValue('payment/xpayments_cloud/dev_url'),
            'topElement'     => '',
            'container'      => '#xpayments-iframe-container',
            'sectionId'      => 'payment_us_xpayments_cloud_connection',
            'loaded'         => false,
            'saveUrl'        => $this->getSaveUrl(),
            'configMap'      => $this->getConfigMap(),
            'debug'          => (bool)$scopeConfig->getValue('payment/xpayments_cloud/debug'),
        );

        return $this->setData($data);
    }

    /**
     * Get map of the configuration fields: {
     *   field: payment_us_xpayments_cloud_connection_some_option
     *   param: someOption
     * }
     *
     * @return array
     */
    protected function getConfigMap()
    {
        $fields = array(
            'account',
            'api_key',
            'secret_key',
            'widget_key',
            'quick_access_key',
        );

        $map = array();

        foreach ($fields as $field) {

            $map[] = array(
                'field' => 'payment_us_xpayments_cloud_connection_' . $field,
                'param' => lcfirst($this->ucWords($field, '')),
            );
        }

        return $map;
    }

    /**
     * Get X-Payments Config save URL
     *
     * @return string
     */
    protected function getSaveUrl()
    {
        $params = array(
            'section' => 'payment',
        );

        if ($this->request->getParam('website')) {
            $params['website'] = $this->request->getParam('website');
        }

        if ($this->request->getParam('store')) {
            $params['store'] = $this->request->getParam('store');
        }

        return $this->backendUrl->getUrl('xpayments_cloud/system_config/save', $params);
    }
}
