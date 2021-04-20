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
 * Logger
 */
class Logger extends \Monolog\Logger
{
    /** 
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager = null;

    /**
     * Current URL
     *
     * @var string
     */
    protected $url = null;

    /**
     * Constructor
     *
     * @param string $name
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param HandlerInterface[] $handlers
     * @param callable[] $processors
     *
     * @return void
     */
    public function __construct(
        $name,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $handlers = array(),
        array $processors = array()
    ) {

        $this->storeManager = $storeManager;

        parent::__construct($name, $handlers, $processors);
    }

    /**
     * Process exception
     *
     * @param \Exception $exception
     * @param string $title
     * @param bool $trace Include backtrace or not
     *
     * @return string
     */
    public function processException(\Exception $exception, $title = false, $trace = false)
    {
        if (
            method_exists($exception, 'getPublicMessage')
            && $exception->getPublicMessage()
        ) {
            $message = $exception->getPublicMessage();
        } else {
            $message = $exception->getMessage();
        }

        $data = array();

        if (!empty($title)) {
            $data[] = $message;
        } else {
            $title = $message;
        }

        $data[] = get_class($exception);

        $data = implode(PHP_EOL, $data);

        $this->writeLog($title, $data, $trace);

        return $message;
    }

    /**
     * Get current URL
     *
     * @return string
     */
    protected function getUrl()
    {
        if (null === $this->url) {

            // Remove session key from URL
            $this->url = preg_replace(
                array('/\/key\/\w+\//', '/\?.*$/'),
                array('/', ''),
                $this->storeManager->getStore()->getCurrentUrl()
            );
        }

        return $this->url;
    }

    /**
     * Write log record
     *
     * @param string $title Log title
     * @param mixed  $data  Data to log
     * @param bool   $trace Include backtrace or not
     *
     * @return bool   Whether the record has been processed
     */
    public function writeLog($title, $data = '', $trace = false)
    {
        if (!is_string($data)) {
            $data = var_export($data, true);
        }

        $message = $title . PHP_EOL
            . date('Y-m-d H:i:s') . PHP_EOL
            . $data . PHP_EOL
            . $this->getUrl() . PHP_EOL;

        if ($trace) {
            $message .= '--------------------------' . PHP_EOL
                . \Magento\Framework\Debug::backtrace(true, false, false)
                . PHP_EOL;
        }

        return $this->debug($message);
    }
}
