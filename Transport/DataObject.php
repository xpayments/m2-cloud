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
 * Base data-object class for transport
 */
abstract class DataObject extends \Magento\Framework\DataObject
{
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
     * Converts field names for setters and geters
     * (Do not use underscopes, actually, keep orig names)
     *
     * @param string $name
     *
     * @return string
     */
    protected function _underscore($name)
    {
        return lcfirst($name);
    }
}
