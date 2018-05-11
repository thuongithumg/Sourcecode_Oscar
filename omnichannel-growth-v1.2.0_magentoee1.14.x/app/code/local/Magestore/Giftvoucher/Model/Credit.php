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
 * Giftvoucher Credit Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */

class Magestore_Giftvoucher_Model_Credit extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('giftvoucher/credit');
    }

    /**
     * @return Magestore_Giftvoucher_Model_Credit
     */
    public function getCreditAccountLogin()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customer->getId();
        return $this->getCreditByCustomerId($customerId);
    }

    /**
     * Get credit by customer ID
     *
     * @param int $customerId
     * @return Magestore_Giftvoucher_Model_Credit
     */
    public function getCreditByCustomerId($customerId)
    {
        $collection = $this->getCollection()->addFieldToFilter('customer_id', $customerId);
        if ($collection->getSize()) {
            $id = $collection->getFirstItem()->getId();
            $this->load($id);
        }
        return $this;
    }

}
