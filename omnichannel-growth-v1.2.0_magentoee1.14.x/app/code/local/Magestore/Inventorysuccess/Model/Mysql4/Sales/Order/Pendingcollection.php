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
 * Adjuststock Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Sales_Order_Pendingcollection 
    extends Mage_Sales_Model_Resource_Order_Grid_Collection
{
    
    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect(); 
        $orderIds = Magestore_Coresuccess_Model_Service::orderService()->getPendingShipOrderIds($this->getProductId());
        $this->addFieldToFilter('entity_id', array('in' => $orderIds));        
        return $this;
    }
    
    
    /**
     * 
     * @return int
     */
    public function getProductId()
    {
        return Mage::app()->getRequest()->getParam('product_id');
    }
}