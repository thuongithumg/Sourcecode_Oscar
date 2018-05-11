<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Class Magestore_Inventorysuccess_Model_Observer_Adminhtml_ControllerActionPredispatch
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Observer_Core_BlockAbstractToHtmlAfter
{
    /**
     *
     * @param type $observer
     */
    public function execute($observer)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_View_Info $block */
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Info) {
            $transport = $observer->getEvent()->getTransport();
            $html = explode("</table>", $transport->getHtml());
            if (isset($html[0])) {
                $warehouseBlockHtml = $block->getLayout()
                    ->createBlock('inventorysuccess/adminhtml_sales_order_view_warehouse')
                    ->setTemplate('inventorysuccess/sales/order/view/warehouse.phtml')
                    ->toHtml();
                $html[0] .= $warehouseBlockHtml;
            }
            $html = implode("</table>", $html);
            $transport->setHtml($html);
            return $this;
        }
    }
}