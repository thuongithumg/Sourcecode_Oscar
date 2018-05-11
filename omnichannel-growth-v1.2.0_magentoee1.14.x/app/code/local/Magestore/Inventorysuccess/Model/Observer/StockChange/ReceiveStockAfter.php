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
 * Stock Change Observer
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Observer_StockChange_ReceiveStockAfter 
    extends Magestore_Inventorysuccess_Model_Observer_StockChange_StockMovementObserver
{
    /**
     * Process issue data to add stock movement
     *
     * @param $data
     * @return array
     */
    public function processData($data)
    {
        $insertData = array();
        $productData = $this->_loadProductData(array_keys($data['products']));
        $actionNumber = $this->getActionNumber($data['action_type'], $data['action_id']);
        foreach ($data['products'] as $productId => $qty) {
            if($qty == 0) {
                continue;
            }
            $productSku = isset($productData[$productId]) ? $productData[$productId]['sku'] : 'N/A';
            $insertData[] = array(
                'product_id' => $productId,
                'product_sku' => $productSku,
                'qty' => $qty,
                'action_code' => $data['action_type'],
                'action_id' => $data['action_id'],
                'action_number' => $actionNumber,
                'warehouse_id' => $data['warehouse_id'],
                'created_at' => date('Y-m-d H:i:s')
            );
        }
        return $insertData;
    }
}