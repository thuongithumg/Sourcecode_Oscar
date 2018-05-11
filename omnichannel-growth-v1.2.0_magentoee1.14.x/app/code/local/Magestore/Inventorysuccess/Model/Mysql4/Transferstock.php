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
 * Transferstock Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Transferstock extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('inventorysuccess/transferstock', 'transferstock_id');
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product
     */
    public function getTransferProductResource()
    {
        return Mage::getResourceModel('inventorysuccess/transferstock_product');
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Activity
     */
    public function getTransferActivityResource()
    {
        return Mage::getResourceModel('inventorysuccess/transferstock_activity');
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Activity_Product
     */
    public function getTransferActivityProductResource()
    {
        return Mage::getResourceModel('inventorysuccess/transferstock_activity_product');
    }
}