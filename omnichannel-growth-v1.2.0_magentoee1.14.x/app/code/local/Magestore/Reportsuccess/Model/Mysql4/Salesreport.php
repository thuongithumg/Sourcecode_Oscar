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
 * Reportsuccess Resource  Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Mysql4_Salesreport extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('reportsuccess/salesreport', 'id');
    }

    /**
     *  dispatch even from Indexer
     */
    public function reindexAll(){
        return $this->service()->reindexDataSalesReport();
    }

    /**
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public function service(){
        return Magestore_Coresuccess_Model_Service::reportInventoryService();
    }

}