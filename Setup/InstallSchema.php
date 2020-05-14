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

namespace CDev\XPaymentsCloud\Setup;

/**
 * Install Schemma
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface 
{
    /**
     * Install
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup, 
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_payment'),
            'cc_first_6',
            array(
                'type'     => 'text',
                'length'   => '6',
                'nullable' => false,
                'comment'  => 'Cc First 6',
            )
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_payment'),
            'xpayments_customer_id',
            array(
                'type'     => 'text',
                'length'   => '32',
                'nullable' => false,
                'comment'  => 'X-Payments Customer ID',
            )
        );

        $setup->endSetup();
    }
}
