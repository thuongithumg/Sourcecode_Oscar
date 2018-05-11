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

class Magestore_Webpos_Model_Observer_Inventorysuccess_WebposCatalogProductGetByIds
    extends Magestore_Webpos_Model_Observer_Inventorysuccess_WebposAddItemFromShoppingCartBefore
{

    /**
     * Assign product to current warehouse if not existed
     * @param $observer
     */
    public function execute($observer)
    {
        $params = $observer->getData('params');
        if ($this->_helper->isInventorySuccessEnable() && $params) {
            $productIds = $params->getData('product_ids');
            $posWarehouseId = $this->getCurrentWarehouseId();
            if($posWarehouseId && !empty($productIds)){
                foreach ($productIds as $productId){
                    $this->assignProductToWarehouse($productId, $posWarehouseId);
                }
            }
        }
    }

}