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
 * Install data
 */
class InstallData implements \Magento\Framework\Setup\InstallDataInterface 
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    protected $customerSetupFactory = null;
    
    /**
     * Attribute setup factory
     *
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory = null;
    
    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     *
     * @return void
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
 
    
    /**
     * Install
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface
     *
     * @return void
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {

        $customerSetup = $this->customerSetupFactory->create(array('setup' => $setup));
        
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        
        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'xpayments_customer_id',
            array(
                'type'         => 'varchar',
                'label'        => 'X-Payments Cloud customer ID',
                'input'        => 'text',
                'required'     => false,
                'visible'      => false,
                'user_defined' => true,
                'sort_order'   => 1000,
                'position'     => 1000,
                'system'       => 0,
            )
        );
        
        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(
                \Magento\Customer\Model\Customer::ENTITY, 
                'xpayments_customer_id'
            )->addData(
                array(
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                )
            );
        
        $attribute->save();
    }
}
