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

namespace CDev\XPaymentsCloud\Helper;

require_once(BP . '/vendor/xpayments/cloud-sdk-php/lib/XPaymentsCloud/Client.php');

/**
 * Helper for X-Payments SDK Client 
 */
class Client extends \Magento\Framework\App\Helper\AbstractHelper 
{
    /**
     * X-Payments SDK Client
     *
     * @var \XPaymentsCloud\Client
     */
    protected $client = null;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig = null;

    /**
     * Logger
     *
     * @var \CDev\XPaymentsCloud\Logger\Logger
     */
    protected $xpaymentsLogger = null;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \CDev\XPaymentsCloud\Logger\Logger $xpaymentsLogger
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \CDev\XPaymentsCloud\Logger\Logger $xpaymentsLogger
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get SDK Client
     *
     * @return \XPaymentsCloud\Client
     */
    public function getClient()
    {
        if (null === $this->client) {

            $this->client = false;

            try {

                $this->client = new \XPaymentsCloud\Client(
                    $this->scopeConfig->getValue('payment/xpayments_cloud/account'),
                    $this->scopeConfig->getValue('payment/xpayments_cloud/api_key'),
                    $this->scopeConfig->getValue('payment/xpayments_cloud/secret_key')
                );

            } catch (\Exception $exception) {

                $message = $this->xpaymentsLogger->processException($exception);

                throw new \Magento\Framework\Exception\LocalizedException(__($message));
            }
        }

        return $this->client;
    }
}
