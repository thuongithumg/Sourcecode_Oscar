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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Renderer_Order
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($row->getOrderIncrementId());
        return sprintf('<a href="%s" title="%s">%s</a>', $this->getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId())), Mage::helper('giftvoucher')->__('View Order Detail'), $row->getOrderIncrementId()
        );
    }

}
