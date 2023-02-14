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

/**
 * Helper for cart
 */
class Cart extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Default string length
     */
    protected const DEFAULT_STRING_LENGTH = 255;

    /**
     * Store information
     *
     * @var \Magento\Store\Model\Information
     */
    private $storeInfo = null;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager = null;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig = null;

    /**
     * Helper for address
     *
     * @var \CDev\XPaymentsCloud\Helper\Address
     */
    protected $addressHelper = null;

    /**
     * Constructor
     *
     * @param \Magento\Store\Model\Information $storeInfo
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \CDev\XPaymentsCloud\Helper\Address $addressHelper
     *
     * @return void
     */
    public function __construct(
        \Magento\Store\Model\Information $storeInfo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \CDev\XPaymentsCloud\Helper\Address $addressHelper
    ) {
        $this->storeInfo = $storeInfo;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;

        $this->addressHelper = $addressHelper;
    }

    /**
     * Format price in 1234.56 format
     *
     * @param mixed $price
     *
     * @return string
     */
    public function preparePrice($price)
    {
        return number_format($price, 2, '.', '');
    }

    /**
     * Convert passed value to a string of limited length
     *
     * @param mixed $value
     * @param int $length
     *
     * @return string
     */
    protected function prepareString($value, $length = self::DEFAULT_STRING_LENGTH)
    {
        return substr((string)$value, 0, $length);
    }

    /**
     * Check if quantity is positive integer
     *
     * @param int|float|string $quantity Quantity
     *
     * @return bool
     */
    protected function isNaturalNumber($quantity)
    {
        return (int)$quantity == $quantity
            && (int)$quantity > 0;
    }

    /**
     * Prepare items from quote for initial payment request
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return array
     */
    protected function prepareItems(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $items = array();

        foreach ($order->getAllVisibleItems() as $item) {

            if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $item->getProductType()) {
                continue;
            }

            $name = $item->getName();
            $sku = $item->getSku();
            $quantity = $item->getQtyOrdered();

            if ($item->getParentItem()) {
                $currentItem = $item->getParentItem();
            } else {
                $currentItem = $item;
            }

            $quantity = $item->getQtyOrdered();

            if ($this->isNaturalNumber($quantity)) {

                $price = $currentItem->getPrice();
                $quantity = (int)$quantity;

            } else {

                $name = sprintf('%s (x%s)', $name, round($quantity, 2));
                $quantity = 1;
                $price = $currentItem->getBaseRowTotal();
            }

            $items[] = array(
                'quantity' => $quantity,
                'name'     => $this->prepareString($name),
                'sku'      => $this->prepareString($sku),
                'price'    => $this->preparePrice($price),
            );
        }

        return $items;
    }

    /**
     * Prepare cart for initial payment request
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return array
     */
    public function prepareCart(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $description = 'Order #' . $order->getIncrementId();

        $billingAddress = $this->addressHelper->prepareBillingAddress($order);

        if ($order->getShippingAddress()) {
            $shippingAddress = $this->addressHelper->prepareShippingAddress($order);
        } else {
            // For downloadable products
            $shippingAddress = $billingAddress;
        }

        $cart = array(
            'login'                => $order->getCustomerEmail(),
            'billingAddress'       => $billingAddress,
            'shippingAddress'      => $shippingAddress,
            'items'                => $this->prepareItems($order),
            'currency'             => $order->getBaseCurrencyCode(),
            'shippingCost'         => $this->preparePrice($order->getShippingAmount()),
            'taxCost'              => $this->preparePrice($order->getTaxAmount()),
            'discount'             => $this->preparePrice($order->getDiscountAmount()),
            'totalCost'            => $this->preparePrice($order->getGrandTotal()),
            'description'          => $description,
            'merchantEmail'        => $this->scopeConfig->getValue('trans_email/ident_sales/email'),
        );

        return $cart;
    }
}
