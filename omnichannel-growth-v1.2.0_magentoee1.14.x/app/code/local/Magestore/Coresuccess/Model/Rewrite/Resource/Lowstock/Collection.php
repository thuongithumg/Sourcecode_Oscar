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
 * @package     Magestore_Coresuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
class Magestore_Coresuccess_Model_Rewrite_Resource_Lowstock_Collection 
    extends Mage_Reports_Model_Resource_Product_Lowstock_Collection
{

    /**
     * Join catalog inventory stock item table for further stock_item values filters
     *
     * @param unknown_type $fields
     * @return Mage_Reports_Model_Resource_Product_Lowstock_Collection
     */
    public function joinInventoryItem($fields = array())
    {

        if (!$this->_inventoryItemJoined) {
            $this->getSelect()->join(
                    array($this->_getInventoryItemTableAlias() => $this->_getInventoryItemTable()), 
                    sprintf('e.%s = %s.product_id and %s.stock_id = 1', 
                    $this->getEntity()->getEntityIdField(), $this->_getInventoryItemTableAlias(), $this->_getInventoryItemTableAlias()), 
                    array()
            );
            $this->_inventoryItemJoined = true;
        }

        if (!is_array($fields)) {
            if (empty($fields)) {
                $fields = array();
            } else {
                $fields = array($fields);
            }
        }

        foreach ($fields as $alias => $field) {
            if (!is_string($alias)) {
                $alias = null;
            }
            $this->_addInventoryItemFieldToSelect($field, $alias);
        }

        return $this;
    }

}
