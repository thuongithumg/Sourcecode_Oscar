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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseorder Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Supplier extends Varien_Object
{
    /**
     * @param int $supplierId
     * @param array $productSku
     * @param string $time
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return array
     */
    public function getPriceListJson($supplierId, $productSku = null, $time = null, $purchaseOrder)
    {
        /** @var Magestore_Suppliersuccess_Model_Mysql4_Supplier_Pricelist_Collection $collection */
        $collection = Mage::getResourceModel('suppliersuccess/supplier_pricelist_collection');
        if (!$time) {
            $time = strftime('%Y-%m-%d', Mage::app()->getLocale()->date()->getTimestamp());
        }
        $from = $time . ' 23:59:59';
        $end = $time . ' 00:00:00';
        $orWhereConditions = array(
            "(main_table.start_date <= '{$from}' and main_table.end_date >= '{$end}')",
            "(main_table.start_date <= '{$from}' and main_table.end_date is null)",
            "(main_table.start_date is null and main_table.end_date >= '{$end}')",
            "(main_table.start_date is null and main_table.end_date is null)"
        );
        if ($productSku) {
            if(is_array($productSku))
                $productSku = "'" . implode("','", $productSku) . "'";
            $andWhereConditions = array(
                "main_table.supplier_id = '{$supplierId}'",
                "main_table.product_sku IN ({$productSku})",
                "main_table.cost > 0"
            );
        }
        if (!$productSku) {
            $andWhereConditions = array(
                "main_table.supplier_id = '{$supplierId}'",
                "main_table.cost > 0"
            );
        }
        $orWhereCondition = implode(' OR ', $orWhereConditions);
        $andWhereCondition = implode(' AND ', $andWhereConditions);
        $collection->getSelect()
            ->where('(' . $orWhereCondition . ') AND ' . $andWhereCondition)
            ->joinLeft(
                array("catalog_product" => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')),
                "main_table.product_sku = catalog_product.sku",
                array('product_id' => 'catalog_product.entity_id')
            )->order('main_table.cost ASC');
        $priceListJson = array();
        $store = Mage::app()->getStore();
        foreach ($collection->getData() as $price) {
            $price['cost'] = $store->convertPrice($price['cost'] * $purchaseOrder->getCurrencyRate(), false, false);
            $priceListJson[] = $price;
        }
        
        return $priceListJson;
    }
}