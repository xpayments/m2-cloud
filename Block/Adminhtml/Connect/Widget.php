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

namespace CDev\XPaymentsCloud\Block\Adminhtml\Connect;

/**
 * X-Payments Cloud Connect Widget
 */
class Widget extends \Magento\Backend\Block\Template
{
    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig = null;

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request = null;

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager = null;

    /**
     * Backend URL builder
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl = null;

    /**
     * PayPal Structure Plugin
     *
     * @var \Magento\Paypal\Model\Config\StructurePlugin
     */
    protected $paypalStructure;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Paypal\Model\Config\StructurePlugin $paypalStructure
     * @param array $data
     *
     * @return void
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Paypal\Model\Config\StructurePlugin $paypalStructure,
        array $data = array()
    ) {
        $this->_template = 'connect/widget.phtml';

        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->backendUrl = $backendUrl;
        $this->paypalStructure = $paypalStructure;

        parent::__construct($context, $data);
    }

    /**
     * Get hash of widget settings
     *
     * @return \CDev\XPaymentsCloud\Transport\ConnectSettings 
     */
    public function getConnectWidgetSettings()
    {
        return new \CDev\XPaymentsCloud\Transport\ConnectSettings(
            $this->scopeConfig,
            $this->request,
            $this->storeManager,
            $this->backendUrl,
            $this->paypalStructure
        );
    }
}
