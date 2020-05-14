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
 * Helper for address
 */
class Address extends \Magento\Framework\App\Helper\AbstractHelper 
{
    /**
     * Placeholder for empty email (something which will pass X-Payments validation)
     */
    const EMPTY_USER_EMAIL = 'user@example.com';

    /**
     * Placeholder for not available cart data
     */
    const NOT_AVAILABLE = 'N/A';

    /**
     * Billing and shipping address names
     */
    const BILLING_ADDRESS = 'Billing';
    const SHIPPING_ADDRESS = 'Shipping';

    /**
     * Address Repository
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository = null;

    /**
     * Region factory
     *
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory = null;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     *
     * @return void
     */
    public function __construct(
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {

        $this->addressRepository = $addressRepository;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Prepare state
     *
     * @param array $data Address data
     *
     * @return string
     */
    private function prepareState($data)
    {
        $state = self::NOT_AVAILABLE;

        if (!empty($data['region_id'])) {

            $region = $this->regionFactory->create()->load($data['region_id']);

            if (
                $region
                && $region->getCode()
            ) {
                $state = $region->getCode();
            }
        }

        return $state;
    }

    /**
     * Prepare street (Address lines 1 and 2)
     *
     * @param array $data Address data
     *
     * @return string
     */
    private function prepareStreet($data)
    {
        $street = self::NOT_AVAILABLE;

        if (!empty($data['street'])) {

            $street = $data['street'];

            if (is_array($street)) {
                $street = array_filter($street);
                $street = implode("\n", $street);
            }
        }

        return $street;
    }


    /**
     * Prepare address hash
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order Order
     * @param $type Address type, Billing or Shipping
     *
     * @return array
     */
    protected function prepareAddress(\Magento\Sales\Api\Data\OrderInterface $order, $type = self::BILLING_ADDRESS)
    {
        $method = 'get' . $type . 'Address';

        $data = $order->$method()->getData();

        if (!empty($data['email'])) {
            $email = $data['email'];
        } elseif ($order->getCustomerEmail()) {
            $email = $order->getCustomerEmail();
        } else {
            $email = self::EMPTY_USER_EMAIL;
        }

        $address = array(
            'firstname' => !empty($data['firstname']) ? $data['firstname'] : self::NOT_AVAILABLE,
            'lastname'  => !empty($data['lastname']) ? $data['lastname'] : self::NOT_AVAILABLE,
            'address'   => $this->prepareStreet($data),
            'city'      => !empty($data['city']) ? $data['city'] : self::NOT_AVAILABLE,
            'state'     => $this->prepareState($data),
            'country'   => !empty($data['country_id']) ? $data['country_id'] : 'XX', // WA fix for MySQL 5.7 with strict mode
            'zipcode'   => !empty($data['postcode']) ? $data['postcode'] : self::NOT_AVAILABLE,
            'phone'     => !empty($data['telephone']) ? $data['telephone'] : '',
            'company'   => '',
            'email'     => $email,
        );

        return $address;
    }

    /**
     * Prepare billing address
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return array
     */
    public function prepareBillingAddress(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $this->prepareAddress($order, self::BILLING_ADDRESS);
    }

    /**
     * Prepare shipping address
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return array
     */
    public function prepareShippingAddress(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $this->prepareAddress($order, self::SHIPPING_ADDRESS);
    }
}
