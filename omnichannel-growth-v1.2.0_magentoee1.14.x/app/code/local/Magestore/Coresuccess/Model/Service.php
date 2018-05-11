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
 * Coresuccess Status Model
 *
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
class Magestore_Coresuccess_Model_Service
{


    /**
     * get report service
     *
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public static function reportInventoryService()
    {
        return self::getService('reportsuccess/service_inventoryreport_inventoryService');
    }


    /**
     * get warehouse service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    public static function warehouseService()
    {
        return self::getService('inventorysuccess/service_warehouse_warehouseService');
    }

    /**
     * get warehouse stock service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseStockService
     */
    public static function warehouseStockService()
    {
        return self::getService('inventorysuccess/service_warehouse_warehouseStockService');
    }

    /**
     * get installation service
     *
     * @return Magestore_Inventorysuccess_Model_Service_InstallationService
     */
    public static function installService()
    {
        return self::getService('inventorysuccess/service_installationService');
    }

    /**
     * get query processor service
     *
     * @return Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    public static function queryProcessorService()
    {
        return self::getService('coresuccess/service_queryProcessorService');
    }

    /**
     * get service model
     *
     * @param string $servicePath
     * @throws Exception
     */
    public static function getService( $servicePath )
    {
        $service = Mage::getSingleton($servicePath);
        if ( $service == false ) {
            throw new Exception('There is no available service: ' . $servicePath);
        }
        return $service;
    }

    /**
     * get rule service
     *
     * @return Magestore_Inventorysuccess_Model_Service_LowStockNotification_RuleService
     */
    public static function ruleService()
    {
        return self::getService('inventorysuccess/service_lowStockNotification_ruleService');
    }

    /**
     * get rule product service
     *
     * @return Magestore_Inventorysuccess_Model_Service_LowStockNotification_RuleProductService
     */
    public static function ruleProductService()
    {
        return self::getService('inventorysuccess/service_lowStockNotification_ruleProductService');
    }

    /**
     * get transfer stock service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Transfer_TransferService
     */
    public static function transferStockService()
    {
        return self::getService('inventorysuccess/service_transfer_transferService');
    }

    /**
     * get transfer activity service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Transfer_TransferActivityService
     */
    public static function transferActivityService()
    {
        return self::getService('inventorysuccess/service_transfer_transferActivityService');
    }

    /**
     * get transfer import service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Transfer_ImportService
     */
    public static function transferImportService()
    {
        return self::getService('inventorysuccess/service_transfer_importService');
    }

    /**
     * get transfer import service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Transfer_ImportService
     */
    public static function transferEmailService()
    {
        return self::getService('inventorysuccess/service_transfer_emailService');
    }

    /**
     * get StockRegistry service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockRegistryService
     */
    public static function stockRegistryService()
    {
        return self::getService('inventorysuccess/service_stock_stockRegistryService');
    }

    /**
     * get StockChange service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockChangeService
     */
    public static function stockChangeService()
    {
        return self::getService('inventorysuccess/service_stock_stockChangeService');
    }

    /**
     * get StockChange service
     *
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_OptionService
     */
    public static function warehouseOptionService()
    {
        return self::getService('inventorysuccess/service_warehouse_optionService');
    }

    /**
     * get inbox service
     *
     * @return Magestore_Inventorysuccess_Model_Service_LowStockNotification_InboxService
     */
    public static function inboxService()
    {
        return self::getService('inventorysuccess/service_lowStockNotification_inboxService');
    }

    /**
     * get notification service
     *
     * @return Magestore_Inventorysuccess_Model_Service_LowStockNotification_NotificationService
     */
    public static function notificationService()
    {
        return self::getService('inventorysuccess/service_lowStockNotification_notificationService');
    }

    /**
     * get notification product service
     *
     * @return Magestore_Inventorysuccess_Model_Service_LowStockNotification_NotificationProductService
     */
    public static function notificationProductService()
    {
        return self::getService('inventorysuccess/service_lowStockNotification_notificationProductService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Catalog_ProductSaveService
     */
    public static function productSaveService()
    {
        return self::getService('inventorysuccess/service_catalog_productSaveService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Adjuststock_AdjuststockService
     */
    public static function adjustStockService()
    {
        return self::getService('inventorysuccess/service_adjuststock_adjuststockService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Stocktaking_StocktakingService
     */
    public static function stocktakingService()
    {
        return self::getService('inventorysuccess/service_stocktaking_stocktakingService');
    }

    public static function stocktakingImportService()
    {
        return self::getService('inventorysuccess/service_stocktaking_importService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_IncrementIdService
     */
    public static function incrementIdService()
    {
        return self::getService('inventorysuccess/service_incrementIdService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Adjuststock_ImportService
     */
    public static function adjustImportService()
    {
        return self::getService('inventorysuccess/service_adjuststock_importService');
    }

    /**
     * get mail service
     * @return Magestore_Inventorysuccess_Model_Service_MailService
     */
    public static function emailService()
    {
        return self::getService('inventorysuccess/service_mailService');
    }

    /**
     * get supply needs service
     * @return Magestore_Inventorysuccess_Model_Service_SupplyNeedsService
     */
    public static function supplyNeedsService()
    {
        return self::getService('inventorysuccess/service_supplyNeedsService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Sales_OrderService
     */
    public static function orderService()
    {
        return self::getService('inventorysuccess/service_sales_orderService');
    }

    /**
     * import product service
     * @return Magestore_Inventorysuccess_Model_Service_ImportExport_ImportProductService
     */
    public static function importProductService()
    {
        return self::getService('inventorysuccess/service_importExport_importProductService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_CancelOrderService
     */
    public static function cancelOrderService()
    {
        return self::getService('inventorysuccess/service_orderProcess_cancelOrderService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_CreateCreditmemoService
     */
    public static function createCreditmemoService()
    {
        return self::getService('inventorysuccess/service_orderProcess_createCreditmemoService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_CreateShipmentService
     */
    public static function createShipmentService()
    {
        return self::getService('inventorysuccess/service_orderProcess_createShipmentService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_CreditmemoFormService
     */
    public static function creditmemoFormService()
    {
        return self::getService('inventorysuccess/service_orderProcess_creditmemoFormService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_CreditmemoViewService
     */
    public static function creditmemoViewService()
    {
        return self::getService('inventorysuccess/service_orderProcess_creditmemoViewService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_PlaceNewOrderService
     */
    public static function placeNewOrderService()
    {
        return self::getService('inventorysuccess/service_orderProcess_placeNewOrderService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_ShipmentFormService
     */
    public static function shipmentFormService()
    {
        return self::getService('inventorysuccess/service_orderProcess_shipmentFormService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_ShipmentViewService
     */
    public static function shipmentViewService()
    {
        return self::getService('inventorysuccess/service_orderProcess_shipmentViewService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_CreditmemoItemService
     */
    public static function creditmemoItemService()
    {
        return self::getService('inventorysuccess/service_warehouse_sales_creditmemoItemService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_OrderItemService
     */
    public static function orderItemService()
    {
        return self::getService('inventorysuccess/service_warehouse_sales_orderItemService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_ShipmentItemService
     */
    public static function shipmentItemService()
    {
        return self::getService('inventorysuccess/service_warehouse_sales_shipmentItemService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_StockMovement_StockMovementProviderService
     */
    public static function stockMovementProviderService()
    {
        return self::getService('inventorysuccess/service_stockMovement_stockMovementProviderService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_StockMovement_StockMovementActionService
     */
    public static function stockMovementActionService()
    {
        return self::getService('inventorysuccess/service_stockMovement_stockMovementActionService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Permission_PermissionService
     */
    public static function permissionService()
    {
        return self::getService('inventorysuccess/service_permission_permissionService');
    }

    /**
     *
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_LocationService
     */
    public static function locationService()
    {
        return self::getService('inventorysuccess/service_warehouse_locationService');
    }

    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_BarcodeService
     */
    public static function barcodeService()
    {
        return self::getService('barcodesuccess/service_barcode_barcodeService');
    }

    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_GenerateService
     */
    public static function barcodeGenerateService()
    {
        return self::getService('barcodesuccess/service_barcode_generateService');
    }

    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_ImportService
     */
    public static function barcodeImportService()
    {
        return self::getService('barcodesuccess/service_barcode_importService');
    }

    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_HistoryService
     */
    public static function barcodeHistoryService()
    {
        return self::getService('barcodesuccess/service_barcode_historyService');
    }


    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_TemplateService
     */
    public static function barcodeTemplateService()
    {
        return self::getService('barcodesuccess/service_barcode_templateService');
    }
    /**
     * @return Magestore_Barcodesuccess_Model_Service_Barcode_ProductService
     */
    public static function barcodeProductService()
    {
        return self::getService('barcodesuccess/service_barcode_productService');
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Service_PurchaseorderService
     */
    public static function purchaseorderService(){
        return self::getService('purchaseordersuccess/service_purchaseorderService');
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_ItemService
     */
    public static function purchaseorderItemService(){
        return self::getService('purchaseordersuccess/service_purchaseorder_itemService');
    }

    /**
     * @return Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_ReceivedService
     */
    public static function purchaseorderItemReceivedService(){
        return self::getService('purchaseordersuccess/service_purchaseorder_item_receivedService');
    }
    
    /**
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_SupplierService
     */
    public static function supplierService()
    {
        return self::getService('suppliersuccess/service_supplier_supplierService');
    }
    
    /**
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_ProductService
     */
    public static function supplierProductService()
    {
        return self::getService('suppliersuccess/service_supplier_productService');
    }

    /**
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_PricelistService
     */
    public static function supplierPricelistService()
    {
        return self::getService('suppliersuccess/service_supplier_pricelistService');
    }

    /**
     * @return Magestore_Suppliersuccess_Model_Service_Catalog_ProductSaveService
     */
    public static function supplierProductSaveService()
    {
        return self::getService('suppliersuccess/service_catalog_productSaveService');
    }    
    
    /**
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_CountryService
     */
    public static function supplierCountryService()
    {
        return self::getService('suppliersuccess/service_supplier_countryService');
    }        
   
    /**
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_ImportService
     */
    public static function supplierImportService()
    {
        return self::getService('suppliersuccess/service_supplier_importService');
    }
    
    /**
     * @return Magestore_Inventorysuccess_Model_Service_Stock_StockService
     */
    public static function stockService()
    {
        return self::getService('inventorysuccess/service_stock_stockService');
    }    

    /**
     * @return Magestore_Inventorysuccess_Model_Service_Catalog_ProductLimitationService
     */
    public static function productLimitationService()
    {
        return self::getService('inventorysuccess/service_catalog_productLimitationService');
    } 
    
    /**
     * @return Magestore_Inventorysuccess_Model_Service_Installation_ConvertDataService
     */
    public static function installationConvertDataService()
    {
        return self::getService('inventorysuccess/service_installation_convertDataService');
    }     
    
    /**
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_StoreService
     */
    public static function warehouseStoreService()
    {
        return self::getService('inventorysuccess/service_warehouse_storeService');
    }         
    
    /**
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_OrderProcessService
     */
    public static function orderProcessService()
    {
        return self::getService('inventorysuccess/service_orderProcess_orderProcessService');
    }          
    
    /**
     * @return Magestore_Inventorysuccess_Model_Service_OrderProcess_ChangeOrderWarehouseService
     */
    public static function changeOrderWarehouseService()
    {
        return self::getService('inventorysuccess/service_orderProcess_changeOrderWarehouseService');
    }       
    
    /**
     * @return Magestore_Inventorysuccess_Model_Service_StockMovement_StockTransferService
     */
    public static function stockTransferService()
    {
        return self::getService('inventorysuccess/service_stockMovement_stockTransferService');
    }         
}