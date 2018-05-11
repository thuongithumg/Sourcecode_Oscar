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
class Magestore_Inventorysuccess_Model_Service_Stocktaking_StocktakingService
    extends
    Magestore_Inventorysuccess_Model_Service_ProductSelection_ProductSelectionService
{
    /**
     * Create new Stock Stocktaking
     *
     * @param Magestore_Inventorysuccess_Model_Stocktaking $stocktaking
     * @param array $data
     * @return Magestore_Inventorysuccess_Model_Stocktaking
     */
    public function createStocktaking(
        Magestore_Inventorysuccess_Model_Stocktaking $stocktaking,
        $data
    ) {
        $createdAt       = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::CREATED_AT]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::CREATED_AT] :
            now();
        $createdBy       = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::CREATED_BY]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::CREATED_BY] :
            $this->getAdminSession()->getUser()->getUsername();
        $stocktakingCode = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKING_CODE]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKING_CODE] :
            $this->generateCode();
        $status          = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::STATUS]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::STATUS] :
            null;
        /* prepare data for stock stocktaking */
        $this->prepareDataForStocktaking($stocktaking, $data, $createdAt, $createdBy, $stocktakingCode, $status);


        /* load warehouse data if $data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_NAME] is null */
        if ( !$data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_NAME] ) {
            $this->setWarehouseInformation($stocktaking, $data);
        }
        if ( isset($data['products']) && count($data['products']) ) {
            $this->prepareStockInWarehouse($data);
        }
        /* create Product Selection */
        $this->createSelection($stocktaking, $data);

        return $stocktaking;
    }

    /**
     * prepare data for stocktaking
     *
     * @param $stocktaking
     * @param $data
     * @param $status
     * @param $createdAt
     * @param $createdBy
     * @param $stocktakingCode
     * @return mixed
     */
    public function prepareDataForStocktaking(
        &$stocktaking,
        $data,
        $createdAt,
        $createdBy,
        $stocktakingCode,
        $status = null
    ) {
        $stocktaking->setReason($data[Magestore_Inventorysuccess_Model_Stocktaking::REASON])
                    ->setWarehouseId($data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_ID])
                    ->setWarehouseName($data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_NAME])
                    ->setWarehouseCode($data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_CODE])
                    ->setParticipants($data[Magestore_Inventorysuccess_Model_Stocktaking::PARTICIPANTS])
                    ->setStocktakeAt($data[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKE_AT])
                    ->setCreatedAt($createdAt)
                    ->setCreatedBy($createdBy)
                    ->setStocktakingCode($stocktakingCode);
        if ( isset($status) && $status >= 0 ) {
            $stocktaking->setStatus($status);
        }
        if ( isset($data[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_BY]) ) {
            $stocktaking->setVerifiedBy($data[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_BY]);
        }
        if ( isset($data[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_AT]) ) {
            $stocktaking->setVerifiedAt($data[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_AT]);
        }
        if ( isset($data[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_BY]) ) {
            $stocktaking->setConfirmedBy($data[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_BY]);
        }
        if ( isset($data[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_AT]) ) {
            $stocktaking->setConfirmedAt($data[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_AT]);
        }

        return $stocktaking;
    }

    /**
     * @param $stocktaking
     * @param $data
     */
    public function setWarehouseInformation(
        &$stocktaking,
        $data
    ) {
        $warehouse = Mage::getModel('inventorysuccess/warehouse')
                         ->load($data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_ID]);
        $stocktaking->setData(Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_NAME,
                              $warehouse->getWarehouseName());
        $stocktaking->setData(Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_CODE,
                              $warehouse->getWarehouseCode());
    }

    /**
     * @param $data
     */
    public function prepareStockInWarehouse( &$data )
    {
        /* load old_qty of products in warehouse */
        $stockRegistryService = Mage::getModel('inventorysuccess/service_stock_stockRegistryService');
        $whProducts           = $stockRegistryService->getStocks($data[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID],
                                                                 array_keys($data['products']));
        if ( $whProducts->getSize() ) {
            foreach ( $whProducts as $whProduct ) {
                $data['products'][$whProduct->getProductId()]['old_qty'] = $whProduct->getTotalQty();
            }
        }
    }

    /**
     * Generate unique code of Stock Stocktaking
     *
     * @return string
     */
    public function generateCode()
    {
        return parent::generateUniqueCode(Magestore_Inventorysuccess_Model_Stocktaking::PREFIX_CODE);
    }

    /**
     * get different product list
     *
     * @return string
     */
    public function getDifferentProducts( $stocktaking )
    {
        $products = $stocktaking->getSelectionProductModel()->getCollection()
                                ->getStocktakingDifferentProducts($stocktaking->getId());
        return $products;
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
     * get stocktaking stock data
     *
     * @param array
     * @return array
     */
    public function getStocktakingData(
        $data,
        $backParam
    ) {
        $stocktakingData                                                                 = array();
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKING_CODE] = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKING_CODE]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKING_CODE] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_ID]     = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_ID]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_ID] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_CODE]   = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_CODE]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_CODE] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_NAME]   = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_NAME]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::WAREHOUSE_NAME] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::REASON]           = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::REASON]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::REASON] :
            '';
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::PARTICIPANTS]     = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::PARTICIPANTS]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::PARTICIPANTS] :
            '';
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKE_AT]     = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKE_AT]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::STOCKTAKE_AT] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_BY]      = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_BY]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_BY] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_AT]      = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_AT]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_AT] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_BY]     = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_BY]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_BY] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_AT]     = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_AT]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_AT] :
            null;
        $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::STATUS]           = isset($data[Magestore_Inventorysuccess_Model_Stocktaking::STATUS]) ?
            $data[Magestore_Inventorysuccess_Model_Stocktaking::STATUS] :
            0;
        $stocktakingData['products']                                                     = isset($data['products']) ? $data['products'] : array();
        $this->prepareDataByParam($stocktakingData, $backParam);

        return $stocktakingData;
    }

    /*
     * prepare stock taking by param
     * @param array
     * @param string
     *
     * @return void
     */
    /**
     * @param $stocktakingData
     * @param $backParam
     */
    public function prepareDataByParam(
        &$stocktakingData,
        $backParam
    ) {
        if ( $backParam == 'start' || $backParam == 'redata' ) {
            $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::STATUS] = Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING;
        }
        if ( $backParam == 'verify' ) {
            $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::STATUS]      = Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED;
            $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_BY] = $this->getAdminSession()->getUser()->getUserName();
            $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::VERIFIED_AT] = now();
        }
        if ( $backParam == 'confirm' ) {
            $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::STATUS]       = Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED;
            $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_BY] = $this->getAdminSession()->getUser()->getUserName();
            $stocktakingData[Magestore_Inventorysuccess_Model_Stocktaking::CONFIRMED_AT] = now();
        }
    }


    public function addProductFromBarcode(
        $stocktaking,
        $barcodes
    ) {
        if ( !is_object($stocktaking) ) {
            $stocktaking = Mage::getModel('inventorysuccess/stocktaking')->load($stocktaking);
        }
        $products = $this->getProducts($stocktaking);
        $data     = array();
        foreach ( $products as $product ) {
            $data[$product['product_id']] = $product->getData();
        }
        foreach ( $barcodes as $barcode ) {
            if ( array_key_exists($barcode['product_id'], $data) ) {
                continue;
            } else {
                $oldQty = Magestore_Coresuccess_Model_Service::warehouseStockService()
                                                             ->getStocks($stocktaking->getWarehouseId(), $barcode['product_id'])
                                                             ->getFirstItem()
                                                             ->getTotalQty();

                $data[$barcode['product_id']] = array(
                    'product_name' => $barcode['product_name'],
                    'product_sku'  => $barcode['product_sku'],
                    'old_qty'      => $oldQty,
                );

            }
        }
        return $this->setProducts($stocktaking, $data);
    }

    public function countProductFromBarcode(
        $stocktaking,
        $barcodes
    ) {
        if ( !is_object($stocktaking) ) {
            $stocktaking = Mage::getModel('inventorysuccess/stocktaking')->load($stocktaking);
        }
        $products = $this->getProducts($stocktaking);
        $data     = array();
        foreach ( $products as $product ) {
            $data[$product['product_id']] = $product->getData();
        }
        foreach ( $barcodes as $barcode ) {
            if ( array_key_exists($barcode['product_id'], $data) ) {
                $data[$barcode['product_id']]['stocktaking_qty'] += $barcode['qty'];
            }
        }
        return $this->setProducts($stocktaking, $data);
    }
}
