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
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Sales_Creditmemo_View_Items_Column_Name_Grouped
    extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{
    
    /**
     * 
     * @return string
     */
    public function getReturnWarehouseColumn()
    {
        return $this->getLayout()
                ->createBlock('inventorysuccess/adminhtml_sales_creditmemo_view_items_column_warehouse')
                ->setItem($this->_getData('item'))
                ->setTemplate('inventorysuccess/sales/creditmemo/view/items/column/warehouse.phtml')
                ->toHtml();        
    }

}