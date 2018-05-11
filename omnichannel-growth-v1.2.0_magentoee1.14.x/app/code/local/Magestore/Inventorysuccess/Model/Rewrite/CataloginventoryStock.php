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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */

class Magestore_Inventorysuccess_Model_Rewrite_CataloginventoryStock 
    extends Mage_CatalogInventory_Model_Stock
{
    /**
     * @var bool
     */
    protected $prepareSave = false;
    
    /**
     * Retrieve stock identifier
     *
     * @return int
     */
    public function getId()
    {
        if($this->prepareSave) {
            return $this->_getData('stock_id');
        }
        return Magestore_Coresuccess_Model_Service::stockService()->getStockId();
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->prepareSave = true;
        return parent::_beforeSave();
    }
    
    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->prepareSave = false;
        return $this;
    }      
    
    
}
