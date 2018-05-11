<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Mysql4_Salesreport_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /**
     * construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('reportsuccess/salesreport');
    }

    /**
     * @return $this
     */
    public function getInformation(){
        /* join attribute code */
        Mage::helper('reportsuccess')->service()->attributeMapping($this);
        /* mapping dimensions */
        Mage::helper('reportsuccess')->service()->dimensionsMapping($this,Magestore_Reportsuccess_Helper_Data::salesreportGridJsObjectdimentions);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        // Count doesn't work with group by columns keep the group by
        if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
        } else {
            $countSelect->columns('COUNT(*)');
        }
        return $countSelect;
    }

    /**
     * @return array
     */
    public function getPaymentOptions(){
        $paymentArray = array();
        $allPaymentMethods = Mage::getModel('payment/config')->getAllMethods();
        foreach($allPaymentMethods as $paymentMethod) {
            $paymentArray[$paymentMethod->getCode()] = $paymentMethod->getTitle();
        }
        return $paymentArray;
    }

    /**
     * @return array
     */
    public function getShippingMethod(){
        $methods = Mage::getSingleton('shipping/config')->getAllCarriers();
        $shipMethods = array();
        foreach ($methods as $shippigCode=>$shippingModel)
        {   $shippingTitle = Mage::getStoreConfig('carriers/'.$shippigCode.'/title');
            $shipMethods[$shippigCode.'_'.$shippigCode] = $shippingTitle;
            if($_methods = $shippingModel->getAllowedMethods()){
                foreach($_methods as $_mcode => $_method)
                {   $_code = $shippigCode . '_' . $_mcode;
                    $shipMethods[$_code] = $shippingTitle.' - '.$_method;
                }
            }
        }
        return $shipMethods;
    }
}