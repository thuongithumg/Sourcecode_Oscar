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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Shipping
 */
class Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_Shipping extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('shipping_and_tracking', array(
            'legend' => Mage::helper('giftvoucher')->__('Shipping and Tracking Information'))
        );

        $fieldset->addField('shipped_to_customer', 'select', array(
            'label' => Mage::helper('giftvoucher')->__('Shipped to Customer'),
            'required' => false,
            'name' => 'shipped_to_customer',
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        if (is_object($this->getShipment())) {
            $shipment = $this->getShipment();
            Mage::register('current_shipment', $shipment);
            $fieldset->addField('adminhtml_shipment', 'note', array(
                'label' => Mage::helper('giftvoucher')->__('Shipment'),
                'text' => '<a href="' . $this->getUrl('adminhtml/sales_order_shipment/view', array('shipment_id' => $shipment->getId())) . '" title="">#' . $shipment->getIncrementId() . '</a>',
            ));
            $fieldset->addField('adminhtml_tracking', 'note', array(
                'label' => Mage::helper('giftvoucher')->__('Tracking Information'),
                'text' => $this->getLayout()->createBlock('adminhtml/sales_order_shipment_view_tracking')
                        ->setShipment($shipment)
                        ->setTemplate('giftvoucher/tracking.phtml')->toHtml(),
            ));
        }

        if (Mage::registry('giftvoucher_data')) {
            $form->addValues(Mage::registry('giftvoucher_data')->getData());
        }
        return parent::_prepareForm();
    }

}
