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
class Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product extends Mage_Core_Model_Mysql4_Abstract
{
    const FIELD_QTY_DELIVERED = "qty_delivered";
    const FIELD_QTY_RECEIVED  = "qty_received";


    public function _construct()
    {
        $this->_init('inventorysuccess/transferstock_product', 'transferstock_product_id');
    }

    /**
     * @param string $transferstockId
     * @param array $qtys
     * @param string $field
     * @return $this
     */
    public function updateQty($transferstockId, $qtys, $field)
    {
        /* start queries processing */
        $this->_getQueryProcessorService()->start();

        $productIds = array_keys($qtys);

        $products = Mage::getResourceModel('inventorysuccess/transferstock_product_collection')
            ->addFieldToFilter('transferstock_id', $transferstockId);
        if (count($productIds)) {
            $products->addFieldToFilter('product_id', array('in' => $productIds));
        }
        $connection = $this->_getConnection('core_write');
        $changeQtys = array();
        $newQtys    = $qtys;

        if ($products->getSize()) {

            $conditions = array();
            foreach ($products as $product) {
                $changeQty                            = (int)$qtys[$product->getProductId()][$product->getProductId()] + (int)$product->getData($field);
                $changeQtys[$product->getProductId()] = $changeQty;
                unset($newQtys[$product->getProductId()]);
                /* prepare update value */
                $case              = $connection->quoteInto('?', $product->getProductId());
                $condition         = $connection->quoteInto("?", $changeQty);
                $conditions[$case] = $condition;
            }
            $value = $connection->getCaseSql('product_id', $conditions, $field);
            $where = array(
                'product_id IN (?)' => array_keys($changeQtys),
                'transferstock_id=?' => $transferstockId
            );
            $this->_getQueryProcessorService()->addQuery(array(
                'type' => 'update',
                'values' => array($field => $value),
                'condition' => $where,
                'table' => $this->getTable('inventorysuccess/transferstock_product')
            ));
        }
        /* process queries in Processor */
        $this->_getQueryProcessorService()->process();

        return $this;
    }

    /**
     * @return Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    protected function _getQueryProcessorService()
    {
        return Magestore_Coresuccess_Model_Service::queryProcessorService();
    }
}