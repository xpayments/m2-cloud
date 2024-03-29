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

namespace CDev\XPaymentsCloud\Logger;

/**
 * Logger handler
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Log file name
     */
    protected const XPAYMENTS_LOG_FILE = '/var/log/xpayments-cloud-%s.log';

    /**
     * Constructor
     *
     * @param \Magento\Framework\Filesystem\DriverInterface $filesystem
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string $filePath
     * @param string $fileName
     *
     * @return void
     *
     * @SuppressWarnings(MEQP2.Classes.ConstructorOperations.CustomOperationsFound)
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        \Magento\Framework\Filesystem\DriverInterface $filesystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $filePath = null,
        $fileName = null
    ) {

        $this->fileName = sprintf(self::XPAYMENTS_LOG_FILE, $date->date('Y-m'));

        parent::__construct($filesystem, $filePath, $fileName);
    }
}

