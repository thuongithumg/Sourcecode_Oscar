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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tabs
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tabs constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('giftvoucher_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('giftvoucher')->__('Gift Code Information'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('giftvoucher')->__('General Information'),
            'title' => Mage::helper('giftvoucher')->__('General Information'),
            'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_giftvoucher_edit_tab_form')->toHtml(),
        ));

        $this->addTab('condition', array(
            'label' => Mage::helper('giftvoucher')->__('Shopping Cart Conditions'),
            'title' => Mage::helper('giftvoucher')->__('Shopping Cart Conditions'),
            'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_giftvoucher_edit_tab_conditions')->toHtml(),
        ));
        $this->addTab('action', array(
            'label' => Mage::helper('giftvoucher')->__('Cart Item Conditions'),
            'title' => Mage::helper('giftvoucher')->__('Cart Item Conditions'),
            'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_giftvoucher_edit_tab_actions')->toHtml(),
        ));
        $this->addTab('message_section', array(
            'label' => Mage::helper('giftvoucher')->__('Message Information'),
            'title' => Mage::helper('giftvoucher')->__('Message Information'),
            'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_giftvoucher_edit_tab_message')->toHtml(),
        ));

        if ($id = $this->getRequest()->getParam('id')) {
            if ($shipment = $this->getShipment($id)) {
                $this->addTab('shipping_and_tracking', array(
                    'label' => Mage::helper('giftvoucher')->__('Shipping and Tracking'),
                    'title' => Mage::helper('giftvoucher')->__('Shipping and Tracking'),
                    'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_giftvoucher_edit_tab_shipping')
                            ->setShipment($shipment)
                            ->toHtml(),
                ));
            }
            $this->addTab('history_section', array(
                'label' => Mage::helper('giftvoucher')->__('Transaction History'),
                'title' => Mage::helper('giftvoucher')->__('Transaction History'),
                'content' => $this->getLayout()->createBlock('giftvoucher/adminhtml_giftvoucher_edit_tab_history')->setGiftvoucher($id)->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }

    /**
     * @param $giftCardId
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getShipment($giftCardId) {
        $history = Mage::getResourceModel('giftvoucher/history_collection')
                ->addFieldToFilter('giftvoucher_id', $giftCardId)
                ->addFieldToFilter('action', Magestore_Giftvoucher_Model_Actions::ACTIONS_CREATE)
                ->getFirstItem();
        if (!$history->getOrderIncrementId() || !$history->getOrderItemId()) {
            return false;
        }
        $orderItem = Mage::getModel('sales/order_item')->load($history->getOrderItemId());
        $requestInfo = $orderItem->getProductOptionByCode('info_buyRequest');
        if (!isset($requestInfo['send_friend'])) {
            return false;
        }
        if (!$requestInfo['send_friend']) {
            return false;
        }
        $shipmentItem = Mage::getResourceModel('sales/order_shipment_item_collection')
                ->addFieldToFilter('order_item_id', $history->getOrderItemId())
                ->getFirstItem();
        if (!$shipmentItem || !$shipmentItem->getId()) {
            return true;
        }
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentItem->getParentId());
        if (!$shipment->getId()) {
            return true;
        }
        return $shipment;
    }

}
