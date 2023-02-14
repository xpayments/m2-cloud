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

namespace CDev\XPaymentsCloud\Block\System\Config;

/**
 * X-Payments Cloud configuration iframe
 */
class CloudIframe extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $data
     *
     * @return void
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        array $data = array()
    ) {
        $this->layout = $layout;
        parent::__construct($context);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $this->layout
            ->createBlock(\CDev\XPaymentsCloud\Block\Adminhtml\Connect\Widget::class)
            ->toHtml();

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Decorate field row html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param string $html
     *
     * @return string
     */
    protected function _decorateRowHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element,
        $html
    ) {
        $html = sprintf('<td colspan="3">%s</td>', $html);

        return parent::_decorateRowHtml($element, $html);
    }
}
