<?php
/**
 * Created by Wazza Rooney on 7/25/17 10:38 AM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 7/25/17 10:38 AM
 */

class Magestore_Webpos_Block_Adminhtml_Report_Renderer_PurchasedOn extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $this->_getValue($row)) {
            $data = Mage::app()->getLocale()->storeDate(
                    Mage::app()->getStore($row->getData('store_id')),
                    Varien_Date::toTimestamp($data),
                    true
                );
            Mage::helper('core')->formatDate($data, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}