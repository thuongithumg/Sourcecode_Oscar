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
 * Class Magestore_Storepickup_Block_Adminhtml_Gmap
 */
class Magestore_Storepickup_Block_Adminhtml_Gmap extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface {

    /**
     * @var
     */
    protected $_element;

    /**
     * constructor
     */
    public function __construct() {

        $this->setTemplate('storepickup/gmap.phtml');
    }

    /*
     * renderer
     */

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * get and set element
     * @param Varien_Data_Form_Element_Abstract $element
     * @return $this
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element) {
        $this->_element = $element;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getElement() {
        return $this->_element;
    }

    /**
     * @return null
     */
    public function getStore() {
        $id = $this->getRequest()->getParam('id');
        if (!$id)
            return null;
        $store = Mage::getModel('storepickup/store')->load($id);
        return $store;
    }

    /**
     * @return null
     */
    public function getCoodinates() {
        if (Mage::registry('store_data')) {
            $data = Mage::registry('store_data');
            $coordinates['lat'] = $data['store_latitude'];
            $coordinates['lng'] = $data['store_longitude'];

            return $coordinates;
        } else {
            $store = $this->getStore();
            if ($store) {
                $address['street'] = $store->getAddress();
                $address['city'] = $store->getCity();
                $address['region'] = $store->getRegion();
                $address['zipcode'] = $store->getState();
                $address['country'] = $store->getCountry();
                $coordinates = Mage::getModel('storepickup/gmap')
                        ->getCoordinates($address);

                return $coordinates;
            } else {
                return null;
            }
        }
    }

}