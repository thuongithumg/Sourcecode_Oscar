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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
/**
 * class Magestore_Webpos_Model_Shipping_ShippingRepository
 *
 * Web POS Customer Complain model
 * Use to work with Web POS complain table
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Model_Shipping_ShippingRepository
{

    protected $_shippingSource = false;
    protected $_offlineShippingSource = false;

    /**
     * Magestore_Webpos_Model_Shipping_ShippingRepository constructor.
     */
    public function __construct(
    ) {
        $this->_shippingSource = Mage::getSingleton('webpos/source_adminhtml_shipping');
        $this->_offlineShippingSource = Mage::getSingleton('webpos/source_adminhtml_shippingoffline');
    }

    /**
     * Get shippings list
     *
     * @api
     * @return array|null
     */
    public function getList() {
        $shippingList = ($this->_shippingSource)?$this->_shippingSource->getShippingData():array();

        $shippings = array();
        $shippings['items'] = $shippingList;
        $shippings['total_count'] = count($shippingList);
        return $shippings;
    }

    /**
     * @return array
     */
    public function getOfflineShippingData(){
        $shippings = ($this->_offlineShippingSource)?$this->_offlineShippingSource->getOfflineShippingData():array();
        return $shippings;
    }
}