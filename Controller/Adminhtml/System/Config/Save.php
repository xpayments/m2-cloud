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

namespace CDev\XPaymentsCloud\Controller\Adminhtml\System\Config;

/**
 * Save X-Payments Cloud connect settings 
 */
class Save extends \Magento\Config\Controller\Adminhtml\System\Config\Save 
{
    /**
     * Resource config  
     *
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $resourceConfig = null;

    /**
     * Tiny function to enhance functionality of ucwords
     * Will capitalize first letters and convert separators if needed
     * Doesn't exist in Magento 2 unfortunately
     *
     * @param string $str
     * @param string $destSep
     * @param string $srcSep
     *
     * @return string
     */
    protected function ucWords($str, $destSep = '_', $srcSep = '_')
    {
        return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
    }

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param \Magento\Framework\Cache\FrontendInterface $cache
     * @param \Magento\Framework\Stdlib\StringUtils $string
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig
    ) {
        parent::__construct($context, $configStructure, $sectionChecker, $configFactory, $cache, $string);
        $this->resourceConfig = $resourceConfig;
    }

    /**
     * Save configuration
     *
     * @return void 
     */
    public function execute()
    {
        $fields = array(
            'account',
            'api_key',
            'secret_key',
            'widget_key',
            'quick_access_key',
        );

        $data = $this->getRequest()->getPost();

        foreach ($fields as $field) {

            $path = 'payment/xpayments_cloud/' . $field;

            $key = lcfirst($this->ucWords($field, ''));

            $value = isset($data[$key]) ? $data[$key] : '';

            $this->resourceConfig->saveConfig(
                $path,
                $value
            );
        }
    }
}
