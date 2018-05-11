<?php

/**
 * Class Magestore_Inventorysuccess_Model_Api2_WarehouseStock_Rest_Admin_V1
 */
class Magestore_Inventorysuccess_Model_Api2_WarehouseStock_Rest_Admin_V1 extends
    Magestore_Inventorysuccess_Model_Api2_Abstract
{
    const ACTION_TYPE_GETLIST_UPDATE = 'getlist_update';
    const ACTION_TYPE_GETLIST_UPDATE_MAGMI = 'getlist_update_magmi';
    const ACTION_TYPE_RETRIEVE       = 'retrieve';

    public function dispatch()
    {
        switch ( $this->getActionType() ) {
            case self::ACTION_TYPE_GETLIST_UPDATE:
                /** PUT = update */
                if ( $this->getRequest()->isPut() ) {
                    $data   = $this->getRequest()->getBodyParams();
                    $result = $this->updateWarehouseStock($data);
                }
                /** GET = get collection */
                if ( $this->getRequest()->isGet() ) {
                    $result = $this->getWarehouseStockCollection();
                }
                break;
            case self::ACTION_TYPE_RETRIEVE:
                $warehouseId = $this->getRequest()->getParam('warehouseId');
                $productSku  = $this->getRequest()->getParam('productSku');
                $result      = $this->getWarehouseStockBySku($warehouseId, $productSku);
                break;

            case self::ACTION_TYPE_GETLIST_UPDATE_MAGMI:
                /** PUT = update */
                if ( $this->getRequest()->isPut() ) {
                    $data   = $this->getRequest()->getBodyParams();
                    $result = $this->updateWarehouseStockByMagmi($data);
                }
                break;

            default:
                $result = array();
        }
        $this->_render($result);
        $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
    }

    /**
     * @return array
     */
    public function getWarehouseStockCollection()
    {
        $collection = $this->_warehouseStockService()->getAllStocksWithProductInformation();
        $this->_applyCollectionModifiersNew($collection);
        $this->_applyFilter($collection);
        return $collection->load()->toArray();
    }

    /**
     * @param $warehouseId
     * @param $productSku
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     * @throws Exception
     */
    public function getWarehouseStockBySku(
        $warehouseId,
        $productSku
    ) {
        /** @var Mage_Catalog_Model_Product $product */
        $product   = Mage::getModel('catalog/product');
        $productId = $product->getIdBySku($productSku);
        if ( !$productId ) {
            throw new \Exception("Product with SKU $productSku does not exist");
        }
        return $this->_warehouseStockService()->getStocks($warehouseId, $productId)->getFirstItem()->getData();
    }


    /**
     * @param $warehouseStocks
     * @return array
     * @throws Exception
     */
    public function updateWarehouseStock( $warehouseStocks )
    {
        $result = array();
        foreach ( $warehouseStocks as $data ) {
            $warehouseStock = new Varien_Object($data);
            $productId      = $this->resolveProductId($warehouseStock->getProductSku());
            $warehouseId    = $this->resolveWarehouseId($warehouseStock->getWarehouseCode());
            switch ( $warehouseStock->getOperator() ) {
                case Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_UPDATE_ACTION:
                    $this->_getStockChange()->update($warehouseId, $productId, $warehouseStock->getQty());
                    break;
                case Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_INCREASE_ACTION:
                    $this->_getStockChange()->increase($warehouseId, $productId, $warehouseStock->getQty());
                    break;
                case Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_DECREASE_ACTION:
                    $this->_getStockChange()->decrease($warehouseId, $productId,$warehouseStock->getProductSku(),$warehouseStock->getQty());
                    break;
                default:
                    throw new \Exception("Operator not allowed!");
            }
            $warehouseProduct = $this->getWarehouseStockBySku($warehouseId, $warehouseStock->getProductSku());
            array_push($result,$warehouseProduct);
        }
        return $result;
    }

    /**
     * @param $warehouseStocks
     * @return array
     * @throws Exception
     */
    public function updateWarehouseStockByMagmi( $warehouseStocks )
    {
        $result = array();
        foreach ( $warehouseStocks as $data ) {
            $warehouseStock = new Varien_Object($data);
            $productId      = $this->resolveProductId($warehouseStock->getProductSku());
            $warehouseId    = $this->resolveWarehouseId($warehouseStock->getWarehouseCode());
            switch ( $warehouseStock->getOperator() ) {
                case Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService::QTY_UPDATE_ACTION:
                    $this->createMovementStockAfterCallAPI($warehouseId,$productId,$warehouseStock->getProductSku(), $warehouseStock->getQty());
                    $this->_getStockChange()->update($warehouseId, $productId, $warehouseStock->getQty(), false);
                    break;
                default:
                    throw new \Exception("Operator not allowed!");
            }
            $warehouseProduct = $this->getWarehouseStockBySku($warehouseId, $warehouseStock->getProductSku());
            array_push($result,$warehouseProduct);
        }
        return $result;
    }

    public function resolveProductNames($productId){
        return $productId;
    }

    public function createMovementStockAfterCallAPI($warehouseId,$productId,$sku ,$qty){
        $warehouseProduct = $this->getWarehouseStockBySku($warehouseId, $sku);
        Mage::dispatchEvent('stockchange_adjust_stock_after', array(
            'warehouse_id' => $warehouseId,
            'products' => array(
                $productId => array(
                    'old_qty' => $warehouseProduct['total_qty'],
                    'adjust_qty' => $qty,
                    'change_qty'  =>$qty - $warehouseProduct['total_qty'],
                    'product_sku' =>$sku,
                    'product_name' => $warehouseProduct['name']
                )
            ),
            'action_type' => "api_inventory",
            'action_id' => '',
        ));
    }


    /**
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseStockService
     */
    protected function _warehouseStockService()
    {
        return Magestore_Coresuccess_Model_Service::warehouseStockService();
    }

    /**
     * @param $productSku
     * @return string
     * @throws Exception
     */
    protected function resolveProductId( $productSku )
    {
        $product   = Mage::getSingleton('catalog/product');
        $productId = $product->getIdBySku($productSku);
        if ( !$productId ) {
            throw new \Exception("Product with SKU '$productSku' does not exist");
        }
        return $productId;
    }

    /**
     * @param $warehouseCode
     * @return string
     * @throws Exception
     */
    protected function resolveWarehouseId( $warehouseCode )
    {
        $warehouse = Mage::getModel('inventorysuccess/warehouse');
        $warehouse->getResource()->load($warehouse, $warehouseCode, 'warehouse_code');
        $warehouseId = $warehouse->getWarehouseId();
        if ( !$warehouseId ) {
            throw new \Exception("Warehouse with code '$warehouseCode' does not exist");
        }
        return $warehouseId;
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    protected function _getStockChange()
    {
        return Magestore_Coresuccess_Model_Service::stockChangeService();
    }
}
