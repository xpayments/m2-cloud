// vim: set ts=2 sw=2 sts=2 et:
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

var config = {
    paths: {
        'xpayments/lib/widget': 'CDev_XPaymentsCloud/js/lib/widget',
        'xpayments/base': 'CDev_XPaymentsCloud/js/base',
    },
    shim: {
        'xpayments/base': ['prototype', 'xpayments/lib/widget'],
        'CDev_XPaymentsCloud/js/view/payment/xpayments-cloud': ['xpayments/base'],
    },
    deps: ['xpayments/lib/widget']
};
