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
class Magestore_Inventorysuccess_Model_Service_Adjuststock_AdjuststockService
    extends
    Magestore_Inventorysuccess_Model_Service_ProductSelection_ProductSelectionService
{
    /**
     * create stock adjustment
     *
     * @param Magestore_Inventorysuccess_Model_Adjuststock $adjustStock
     * @param $data
     * @param bool $requiredProduct
     * @param bool $requireChange
     * @return Magestore_Inventorysuccess_Model_Adjuststock
     */
    public function createAdjustment(
        Magestore_Inventorysuccess_Model_Adjuststock $adjustStock,
        $data,
        $requiredProduct = false,
        $requireChange = false
    ) {
        $time_now = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        $createdAt       = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::CREATED_AT]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::CREATED_AT] : $time_now;
        $createdBy       = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::CREATED_BY]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::CREATED_BY] :
            $this->getAdminSession()->getUser()->getUsername();
        $adjustStockCode = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_CODE]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_CODE] :
            $this->generateCode();

        /* load warehouse data if $data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME] is null */
        if ( !isset($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME]) ) {
            $warehouse                                                          = Mage::getModel('inventorysuccess/warehouse')->load($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID]);
            $data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME] = $warehouse->getWarehouseName();
            $data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE] = $warehouse->getWarehouseCode();
        }

        $this->prepareStockData($adjustStock, $data, $createdAt, $createdBy, $adjustStockCode);

        if ( isset($data['products']) && count($data['products']) ) {
            $this->adjustStockInWarehouse($data, $requireChange);
        }
        /* create Product Selection */
        if ( !$requiredProduct || count($data['products']) > 0 ) {
            $this->createSelection($adjustStock, $data);
        }

        return $adjustStock;
    }

    /**
     * Generate unique code of Stock Adjustment
     *
     * @return string
     */
    public function generateCode()
    {
        return parent::generateUniqueCode(Magestore_Inventorysuccess_Model_Adjuststock::PREFIX_CODE);
    }

    /**
     * get admin session
     *
     * @return string
     */
    public function getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * get adjust stock data
     *
     * @param array
     * @return array
     */
    public function getAdjustData( $data )
    {
        $adjustData                                                                 = array();
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_CODE] = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_CODE]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_CODE] :
            null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID]     = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID] :
            null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE]   = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE] :
            null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME]   = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME] :
            null;
        $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::REASON]           = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::REASON]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::REASON] :
            '';
        $adjustData['products']                                                     = isset($data['products']) ? $data['products'] : array();
        return $adjustData;
    }

    /**
     * prepare data for stock adjustment
     *
     * @return string
     */
    public function prepareStockData(
        &$adjustStock,
        $data,
        $createdAt,
        $createdBy,
        $adjustStockCode
    ) {
        $adjustStock->setReason($data[Magestore_Inventorysuccess_Model_Adjuststock::REASON])
                    ->setStatus(Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING)
                    ->setWarehouseId($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID])
                    ->setWarehouseName($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME])
                    ->setWarehouseCode($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE])
                    ->setCreatedAt($createdAt)
                    ->setCreatedBy($createdBy)
                    ->setAdjuststockCode($adjustStockCode);
    }

    /**
     * adjust stock in warehouse
     *
     * @param $data
     * @param $requireChange
     */
    public function adjustStockInWarehouse(
        &$data,
        $requireChange
    ) {
        $helper = Mage::helper('inventorysuccess');
        /* load old_qty of products in warehouse */
        $stockRegistryService = Mage::getModel('inventorysuccess/service_stock_stockRegistryService');
        $whProducts           = $stockRegistryService->getStocks($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID],
                                                                 array_keys($data['products']));
        if ( $whProducts->getSize() ) {
            if ( $helper->getAdjustStockChange() ) {
                foreach ( $whProducts as $whProduct ) {
                    $data['products'][$whProduct->getProductId()]['old_qty'] = $whProduct->getTotalQty();
                    if ( !isset($data['products'][$whProduct->getProductId()]['adjust_qty'])
                         || $data['products'][$whProduct->getProductId()]['adjust_qty'] === null
                    ) {
                        $data['products'][$whProduct->getProductId()]['adjust_qty'] = $whProduct->getTotalQty() +
                                                                                      $data['products'][$whProduct->getProductId()]['change_qty'];
                    }
                    if ( !isset($data['products'][$whProduct->getProductId()]['change_qty'])
                         || $data['products'][$whProduct->getProductId()]['change_qty'] == ''
                    ) {
                        $data['products'][$whProduct->getProductId()]['change_qty'] = $data['products'][$whProduct->getProductId()]['adjust_qty'] - $whProduct->getTotalQty();
                    }
                }
            } else {
                foreach ( $whProducts as $whProduct ) {
                    $data['products'][$whProduct->getProductId()]['old_qty']    = $whProduct->getTotalQty();
                    $data['products'][$whProduct->getProductId()]['change_qty'] = $data['products'][$whProduct->getProductId()]['adjust_qty'] - $whProduct->getTotalQty();

                }
            }
        }
        /* require change qty while creating stock adjustment */
        /* if there is no qty changed, do not create adjuststock */
        if ( $requireChange ) {
            foreach ( $data['products'] as $productId => $adjustData ) {
                if ( isset($adjustData['change_qty']) && $adjustData['change_qty'] == 0 ) {
                    unset($data['products'][$productId]);
                }
            }
        }
    }

    /**
     * Complete a stock adjustment
     *
     * @param Magestore_Inventorysuccess_Model_Adjuststock $adjustStock
     * @param bool $updateCatalog
     */
    public function complete(
        Magestore_Inventorysuccess_Model_Adjuststock $adjustStock,
        $updateCatalog = true
    ) {
        $products    = $this->getProducts($adjustStock);
        $productData = array();
        if ( $products->getSize() ) {
            foreach ( $products as $product ) {
                $productData[$product->getProductId()] = array(
                    'old_qty'      => $product->getOldQty(),
                    'adjust_qty'   => $product->getAdjustQty(),
                    'change_qty'   => $product->getChangeQty(),
                    'product_sku'  => $product->getProductSku(),
                    'product_name' => $product->getProductName(),
                );
            }
        }
        /* adjust stocks in warehouse & global */
        $stockChangeService = Mage::getModel('inventorysuccess/service_stock_stockChangeService');
        $stockChangeService->adjust($adjustStock->getWarehouseId(), $productData, 'adjustment', $adjustStock->getId(), $updateCatalog);

        /* mark as completed */
        $data        = $adjustStock->getData();
        $confirmedAt = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::CONFIRMED_AT]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::CONFIRMED_AT] : now();
        $confirmedBy = isset($data[Magestore_Inventorysuccess_Model_Adjuststock::CONFIRMED_BY]) ?
            $data[Magestore_Inventorysuccess_Model_Adjuststock::CONFIRMED_BY] :
            $this->getAdminSession()->isLoggedIn() ? $this->getAdminSession()->getUser()->getUsername() : '';
        $adjustStock->setStatus(Magestore_Inventorysuccess_Model_Adjuststock::STATUS_COMPLETED)
                    ->setConfirmedBy($confirmedBy)
                    ->setConfirmedAt($confirmedAt);
        $this->getSelectionResource($adjustStock)->save($adjustStock);
    }

    /**
     * add product from barcode, if exists increase qty
     * @param $adjustStock
     * @param $barcodes
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
     */
    public function addProductFromBarcode(
        $adjustStock,
        $barcodes
    ) {
        if ( !is_object($adjustStock) ) {
            $adjustStock = Mage::getModel('inventorysuccess/adjuststock')->load($adjustStock);
        }
        $adjustStockProducts = $this->getProducts($adjustStock);
        $data                = array();
        foreach ( $adjustStockProducts as $adjustStockProduct ) {
            $data[$adjustStockProduct['product_id']] = $adjustStockProduct->getData();
        }
        foreach ( $barcodes as $barcode ) {
            if ( array_key_exists($barcode['product_id'], $data) ) {
                $data[$barcode['product_id']]['change_qty'] += $barcode['qty'];
                $data[$barcode['product_id']]['adjust_qty'] += $barcode['qty'];
            } else {
                $oldQty = Magestore_Coresuccess_Model_Service::warehouseStockService()
                                                             ->getStocks($adjustStock->getWarehouseId(), $barcode['product_id'])
                                                             ->getFirstItem()
                                                             ->getTotalQty();
                if ( Mage::helper('inventorysuccess')->getAdjustStockChange() ) {
                    $data[$barcode['product_id']] = array(
                        'adjust_qty'   => $oldQty + $barcode['qty'],
                        'product_name' => $barcode['product_name'],
                        'product_sku'  => $barcode['product_sku'],
                        'old_qty'      => $oldQty,
                        'change_qty'   => $barcode['qty'],
                    );
                } else {
                    $data[$barcode['product_id']] = array(
                        'adjust_qty'   => $barcode['qty'],
                        'product_name' => $barcode['product_name'],
                        'product_sku'  => $barcode['product_sku'],
                        'old_qty'      => $oldQty,
                        'change_qty'   => $oldQty + $barcode['qty'],
                    );
                }
            }
        }
        return $this->setProducts($adjustStock, $data);
    }
}
