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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Block_Adminhtml_Store_Edit_Tab_Gmap
 */
class Magestore_Storepickup_Block_Adminhtml_Store_Edit_Tab_Gmap extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return mixed
     */
    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        if (Mage::getSingleton('adminhtml/session')->getStorepickupData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStorepickupData();
            Mage::getSingleton('adminhtml/session')->setStorepickupData(null);
        } elseif (Mage::registry('store_data'))
            $data = Mage::registry('store_data')->getData();

        $fieldset = $form->addFieldset('storepickup_form', array('legend' => Mage::helper('storepickup')->__('Google Map')));
        $fieldset->addField('zoom_level', 'text', array(
            'label' => Mage::helper('storepickup')->__('Zoom Level'),
            'name' => 'zoom_level',
            'value' => 12
        ));
        $fieldset->addField('latitude', 'text', array(
            'label' => Mage::helper('storepickup')->__('Store Latitude'),
            'name' => 'latitude',
            'value' => $data['store_latitude']
        ));
        $fieldset->addField('longtitude', 'text', array(
            'label' => Mage::helper('storepickup')->__('Store Longitude'),
            'name' => 'longtitude',
            'value' => $data['store_longitude']
        ));

        $fieldset->addField('pin_color', 'text', array(
            'label' => Mage::helper('storepickup')->__('Pin Color'),
            'name' => 'pin_color',
            'value' => $data['pin_color'],
            'note' => Mage::helper('storepickup')->__('Set color for store’s pin shown on map'),
        ));

        $fieldset->addField('gmap', 'text', array(
            'label' => Mage::helper('storepickup')->__('Store Map'),
            'name' => 'gmap',
        ))->setRenderer($this->getLayout()->createBlock('storepickup/adminhtml_gmap'));

        //print_r($data);exit;
        // $form->setValues($data);

        return parent::_prepareForm();
    }

}