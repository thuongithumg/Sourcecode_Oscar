<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Inventoryreport_Modifigrids_Modifigrids
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

use Magestore_Reportsuccess_Helper_Data as Data;
class Magestore_Reportsuccess_Model_Service_Inventoryreport_Modifigrids_Modifigrids
{
    /**
     * @var array
     */
    protected $TOTAL_DETAILS = array(
        'sum_total_qty' => 0,
        'sum_qty_to_ship' => 0,
        'available_qty'=>0,
        'qty_in_order'=>0,
    );
    /**
     * @var array
     */
    protected $TOTAL_ON_HAND = array(
        'sum_total_qty' => 0,
        'total_inv_value' => 0,
        'total_retail_value'=>0,
        'total_profit'=>0,
    );
    /**
     * @var array
     */
    protected $TOTAL_INCOMING = array(
        'total_in_coming_group' => 0,
        'total_in__coming_due_group' => 0,
        'total_cost_group'=>0,
    );

    /**
     * @param $ids
     * @param $type
     * @return bool
     */
    public function Type($ids,$type){
        if($type == Data::LOCATIONS){
            $type_Session = Mage::getModel('admin/session')->getData('type_locations');
            if(!$ids){
                if(!$type_Session){
                    return false;
                }else{
                    return $type_Session;
                }
            }else{
                Mage::getModel('admin/session')->setData('type_locations',$ids);
                return $ids;
            }
        }
    }

    /**
     * @param $grid
     * @param $type
     * @return mixed
     */
    public function Columns($grid,$type){
        if($type == Data::STOCK_ON_HAND){
           return  $this->stockOnhandColumns($grid);
        }
        if($type == Data::DETAILS){
            return  $this->detailsColumns($grid);
        }
        if($type == Data::INCOMING_STOCK){
            return $this->incomingStockColumns($grid);
        }
        if($type == Data::HISTORICS){
            return $this->historicsColumns($grid);
        }

        if($type == Data::SALESREPORT){
            return $this->salereportColumns($grid);
        }
    }

    /**
     * @param $grid
     * @param $type
     * @return Varien_Object
     */
    public function Totals($grid,$type){
        $totals = new Varien_Object();
        if($type == Data::STOCK_ON_HAND){
            $fields = $this->TOTAL_ON_HAND;
        }
        if($type == Data::DETAILS){
            $fields = $this->TOTAL_DETAILS;
        }
        if($type == Data::INCOMING_STOCK){
            $fields = $this->TOTAL_INCOMING;
        }
        foreach ($grid->getCollection() as $item) {
            foreach($fields as $field=>$value){
                $fields[$field]+=$item->getData($field);
            }
        }
        $fields['sku']='Totals';
        $fields['action']='Totals';
        if($type == Data::INCOMING_STOCK){
            $fields['product_sku']='Totals';
        }
        $totals->setData($fields);
        return $totals;
    }

    /**
     * @param $ids
     * @param $type
     * @return bool
     */
    public function Warehouse($ids,$type){
        if( ($type == Data::STOCK_ON_HAND) || ($type == Data::DETAILS) || ($type == Data::HISTORICS)){
            return  $this->stockOnhandWarehouse($ids);
        }
        if($type == Data::LOCATIONS){
            return  $this->locationWarehouse($ids);
        }
    }

    /**
     * @return bool|string
     */
    public function temp_date(){
        return date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
    }
    /**
     * @param $date
     * @param $type
     * @return bool
     */
    public function SelectDate($date,$type){
        $temp_date = $this->temp_date();
        if($type == Data::HISTORICS){
            $date_Session = Mage::getModel('admin/session')->getData('date_session');
            if(!$date){
                if(!$date_Session){
                    return $temp_date;
                }else{
                    return $date_Session;
                }
            }else{
                Mage::getModel('admin/session')->setData('date_session',$date);
                return $date;
            }
        }
        return $temp_date;
    }

    /**
     * @param $date_from
     * @param $date_to
     * @param $type
     * @return array
     */
    public function reportSelectDate($date_from,$date_to,$type){
        $temp_date = $this->temp_date();
        $date_Session = Mage::getModel('admin/session')->getData('report_select_date');
        $date = array(
            'date_from' => $date_from ? $date_from : ($date_Session ? $date_Session['date_from'] : $temp_date ),
            'date_to' => $date_to ? $date_to : ($date_Session ? $date_Session['date_to'] : $temp_date) ,
        );
        Mage::getModel('admin/session')->setData('report_select_date',$date);
        return $date;
    }

    /**
     * @param $ids
     * @return bool
     */
    public function locationWarehouse($ids){
        $warehouseIds_Session = Mage::getModel('admin/session')->getData('warehouse_locations');
        if(!$ids){
            if(!$warehouseIds_Session){
                return false;
            }else{
                return $warehouseIds_Session;
            }
        }else{
            Mage::getModel('admin/session')->setData('warehouse_locations',$ids);
            return $ids;
        }
    }

    /**
     * @param $ids
     * @return bool
     */
    public function stockOnhandWarehouse ($ids){
        $warehouseIds_Session = Mage::getModel('admin/session')->getData('warehouse_stockonhand');
        if(!$ids){
            if(!$warehouseIds_Session){
                return false;
            }else{
                return $warehouseIds_Session;
            }
        }else{
            Mage::getModel('admin/session')->setData('warehouse_stockonhand',$ids);
            return $ids;
        }
    }


    /**
     * @param $grid
     * @return mixed
     */
    public function salereportColumns($grid){
        $warehouseoptions = Magestore_Reportsuccess_Model_Mysql4_Costofgood_Collection::getWarehouseIdsxName();
        $grid->addColumn('warehouse_id',
            array(
                'header' => $grid->__('Warehouse'),
                'index' => 'warehouse_id',
                'align' => 'right',
                'type'      =>  'options',
                'options'   =>  $warehouseoptions,
            )
        );


        /* order */

        $grid->addColumn('increment_id',
            array(
                'header' => $grid->__('Order ID'),
                'index' => 'increment_id',
                'align' => 'right',
            )
        );

        $grid->addColumn('status',
            array(
                'header' => $grid->__('Order Status'),
                'index' => 'status',
                'align' => 'right',
                'type'  => 'options',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            )
        );


        $shippingOptions = Magestore_Reportsuccess_Model_Mysql4_Salesreport_Collection::getShippingMethod();
        $grid->addColumn('shipping_method',
            array(
                'header' => $grid->__('Shipping Method'),
                'index' => 'shipping_method',
                'align' => 'right',
                'type'      =>  'options',
                'options'   =>  $shippingOptions,
            )
        );

        /*
        $grid->addColumn('shipping_description',
            array(
                'header' => $grid->__('Shipping Description'),
                'index' => 'shipping_description',
                'align' => 'right',
            )
        );
        */

        $paymentOptions = Magestore_Reportsuccess_Model_Mysql4_Salesreport_Collection::getPaymentOptions();
        $grid->addColumn('payment_method',
            array(
                'header' => $grid->__('Payment Method'),
                'index' => 'payment_method',
                'align' => 'right',
                'type'      =>  'options',
                'options'   =>  $paymentOptions,
            )
        );

        /* Customer */
        $grid->addColumn('customer_email',
            array(
                'header' => $grid->__('Customer Email'),
                'index' => 'customer_email',
                'align' => 'right',
            )
        );

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $grid->addColumn('customer_group_id',
            array(
                'header' => $grid->__('Customer Group'),
                'index' => 'customer_group_id',
                'align' => 'right',
                'type'      =>  'options',
                'options'   =>  $groups,
            )
        );


        /* Qty */
        $grid->addColumn('realized_sold_qty',
            array(
                'header' => $grid->__('Actual Sold Qty'),
                'index' => 'realized_sold_qty',
                'align' => 'right',
            )
        );
        $grid->addColumn('potential_sold_qty',
            array(
                'header' => $grid->__('Potential Sold Qty'),
                'index' => 'potential_sold_qty',
                'align' => 'right',
            )
        );
        /* COGS */
        $grid->addColumn('unit_cost',
            array(
                'header' => $grid->__('Unit Cost'),
                'index' => 'unit_cost',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('unit_price',
            array(
                'header' => $grid->__('Unit Price'),
                'index' => 'unit_price',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );


        $grid->addColumn('realized_cogs',
            array(
                'header' => $grid->__('Actual COGS'),
                'index' => 'realized_cogs',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('potential_cogs',
            array(
                'header' => $grid->__('Potential COGS'),
                'index' => 'potential_cogs',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );

        $grid->addColumn('cogs',
            array(
                'header' => $grid->__('COGS'),
                'index' => 'cogs',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );


        /*Profit*/
        $grid->addColumn('unit_profit',
            array(
                'header' => $grid->__('Unit Profit'),
                'index' => 'unit_profit',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );

        $grid->addColumn('realized_profit',
            array(
                'header' => $grid->__('Actual Profit'),
                'index' => 'realized_profit',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('potential_profit',
            array(
                'header' => $grid->__('Potential Profit'),
                'index' => 'potential_profit',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('profit',
            array(
                'header' => $grid->__('Profit'),
                'index' => 'profit',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );


        /*Tax*/
        $grid->addColumn('unit_tax',
            array(
                'header' => $grid->__('Unit Tax'),
                'index' => 'unit_tax',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('realized_tax',
            array(
                'header' => $grid->__('Actual Tax'),
                'index' => 'realized_tax',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('potential_tax',
            array(
                'header' => $grid->__('Potential Tax'),
                'index' => 'potential_tax',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('tax',
            array(
                'header' => $grid->__('Tax'),
                'index' => 'tax',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );


        /* Discount */
        $grid->addColumn('unit_discount',
            array(
                'header' => $grid->__('Unit Discount'),
                'index' => 'unit_discount',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );

        $grid->addColumn('realized_discount',
            array(
                'header' => $grid->__('Actual Discount'),
                'index' => 'realized_discount',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('potential_discount',
            array(
                'header' => $grid->__('Potential Discount'),
                'index' => 'potential_discount',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );

        $grid->addColumn('total_sale',
            array(
                'header' => $grid->__('Total Sales'),
                'index' => 'total_sale',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('created_at',
            array(
                'header' => $grid->__('Created Time'),
                'index' => 'created_at',
                'align' => 'right',
                'type' => 'datetime',
//            'format' => 'F',
            )
        );

        $grid->addColumn('updated_at',
            array(
                'header' => $grid->__('Updated Time'),
                'index' => 'updated_at',
                'align' => 'right',
                'type' => 'datetime',
//            'format' => 'F',
            )
        );

        $grid->addColumn('action',
            array(
                'header' => $grid->__('Action'),
                'renderer' => 'reportsuccess/adminhtml_salesreport_grid_columns_renderer_orderids',
                'index' => 'order_id',
                'align' => 'right',
                'type' => 'action',
                'filter' => false,
                'order' => false,
                'is_system' => true,
            )
        );

        return $this->removeColumnsFromDimensionsAndMetrics($grid);
    }

    /**
     * @param $grid
     */
    public function removeColumnsFromDimensionsAndMetrics($grid){
        /* Remove columns from Metrics */
        $removecolumn = Mage::getModel('reportsuccess/editcolumns')->getCollection()->addFieldToFilter('grid',Data::salesreportGridJsObject)
            ->getFirstItem();
        if($removecolumn->getId()){
            $columns = $removecolumn->getValue();
            $columns = explode(',',$columns);
            foreach($columns as $value){
                $column = explode(':',$value);
                if(trim($column[1]) == 0){
                    if(trim($column[0]) == 'shipping_method')
                        $grid->removeColumn('shipping_description');
                    $grid->removeColumn(trim($column[0]));
                }
            }
        }

        /* Remove columns from Dimensisons */
        $check_group = 0;
        $array_dimensions = array('increment_id','warehouse_id');
        $removecolumn = Mage::getModel('reportsuccess/editcolumns')->getCollection()->addFieldToFilter('grid',Magestore_Reportsuccess_Helper_Data::salesreportGridJsObjectdimentions)
            ->getFirstItem();
        if($removecolumn->getId()){
            $columns = $removecolumn->getValue();
            $columns = explode(',',$columns);
            foreach($columns as $value){
                $column = explode(':',$value);
                if($column[1] == 1){
                    $check_group = 1;
                    if($column[0] == 'order_id'){
                        $key = array_search('increment_id', $array_dimensions);
                        unset($array_dimensions[$key]);
                    }
                    if(($key = array_search($column[0], $array_dimensions)) !== false) {
                        unset($array_dimensions[$key]);
                    }
                }
            }
        }
        if($check_group == 1){
            if(sizeof($array_dimensions) > 0){
                foreach($array_dimensions as $key){
                    $grid->removeColumn($key);
                }
            }
        }
        return $grid;
    }

    /**
     * @param $grid
     * @return mixed
     */
    public function historicsColumns($grid){
        $grid->addColumn('total_qty',
            array(
                'header' => $grid->__('Qty In Warehouse'),
                'index' => 'total_qty',
                'type' =>'number',
            )
        );
        $grid->addColumn('inv_value',
            array(
                'width' => '100px',
                'header' => $grid->__('Inventory Value'),
                'index' => 'inv_value',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );

        $grid->addColumn('updated_at',
            array(
                'header' => $grid->__('Date'),
                'index' => 'updated_at',
                'type' => 'date',
                'filter' => false,
            )
        );

        $grid->removeColumn('sum_total_qty');

        return $grid;
    }
    /**
     * @param $grid
     * @return mixed
     */
    public function incomingStockColumns($grid){
        $grid->addColumn('product_sku',
            array(
                'header' => $grid->__('SKU'),
                'index' => 'product_sku',
            )
        );
        $grid->addColumn('product_name',
            array(
                'header' => $grid->__('Name'),
                'index' => 'product_name',

            )
        );

        $suppliers = Mage::getResourceModel('suppliersuccess/supplier_collection');
        $options = array();
        foreach($suppliers as $supplier){
            $options[$supplier['supplier_id']] =  $supplier['supplier_name'];
        }
        $grid->addColumn('supplier_id',
            array(
                'header' => $grid->__('Supplier'),
                'index' => 'supplier_id',
                //'renderer' => 'reportsuccess/adminhtml_inventoryreport_column_renderer_supplier',
                'type' => 'options',
                'options'=> $options,
                //'filter_condition_callback' => array($grid, '_filterSupplierCallback')
            )
        );
        $grid->addColumn('po_id_group',
            array(
                'header' => $grid->__('Purchase Order'),
                'index' => 'po_id_group',
                'renderer' => 'reportsuccess/adminhtml_inventoryreport_column_renderer_purchaseOrder',
                'filter' => false,
            )
        );
        $grid->addColumn('qty_in_warehouse',
            array(
                'header' => $grid->__('Qty In Warehouse(s)'),
                'index' => 'qty_in_warehouse',
                'renderer' => 'reportsuccess/adminhtml_inventoryreport_column_renderer_stockInWarehouse',
                'filter' => false,
            )
        );
        $grid->addColumn('total_in_coming_group',
            array(
                'header' => $grid->__('Incoming Stock'),
                'type' =>'number',
                'index' => 'total_in_coming_group',
                'filter_condition_callback' => array($grid, '_filterInventoryCallback')
            )
        );
        $grid->addColumn('total_in__coming_due_group',
            array(
                'header' => $grid->__('Overdue Incoming Stock'),
                'index' => 'total_in__coming_due_group',
                'type' =>'number',
                'filter_condition_callback' => array($grid, '_filterInventoryCallback')
            )
        );
        $grid->addColumn('total_cost_group',
            array(
                'header' => $grid->__('Total Cost of Incoming Stock'),
                'index' => 'total_cost_group',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                'filter_condition_callback' => array($grid, '_filterInventoryCallback')
            )
        );

        $grid->removeColumn('sum_total_qty');
        $grid->removeColumn('mac');
        $grid->removeColumn('sku');
        $grid->removeColumn('name');
        return $grid;
    }
    /**
     * @param $grid
     * @return mixed
     */
    public function detailsColumns($grid){
        $grid->addColumn("sum_qty_to_ship",
            array(
                "header" => $grid->__("Qty to Ship"),
                "index" => "sum_qty_to_ship",
                'type' => 'number',
                "sortable" => true,
                'align' => 'left',
                'filter_condition_callback' => array($grid, '_filterTotalQtyCallback')
            )
        );
        $grid->addColumn("available_qty",
            array(
                "header" => $grid->__("Available Qty"),
                "index" => "available_qty",
                'type' => 'number',
                "sortable" => true,
                'align' => 'left',
                'filter_condition_callback' => array($grid, '_filterTotalQtyCallback')
            )
        );

        $grid->addColumn('supplier_name',
            array(
                'header' => $grid->__('Supplier'),
                'index' => 'supplier_name',
                'renderer' => 'reportsuccess/adminhtml_inventoryreport_column_renderer_supplier',
                'filter_condition_callback' => array($grid, '_filterSupplierCallback')
            )
        );
        $grid->addColumn("qty_in_order",
            array(
                "header" => $grid->__("Qty On Purchase Order"),
                "index" => "qty_in_order",
                'type' => 'number',
                "sortable" => true,
                'align' => 'left',
            )
        );
        $grid->addColumn("shelf_location",
            array(
                "header" => $grid->__("Shelf Location"),
                "index" => "shelf_location",
                'type' => 'text',
                "sortable" => true,
                'align' => 'left',
            )
        );

        /* remove data if not install PurchaseOrder extension */
        Mage::helper('reportsuccess')->prepareDataNotPurchaseOrder($grid,Data::DETAILS);

        $removecolumn = Mage::getModel('reportsuccess/editcolumns')->getCollection()->addFieldToFilter('grid','detailsGridJsObject')
            ->getFirstItem();
        if($removecolumn){
            $columns = $removecolumn->getValue();
            $columns = explode(',',$columns);
            foreach($columns as $value){
                $column = explode(':',$value);
                if($column[1] == 0){
                    $grid->removeColumn($column[0]);
                }
            }
        }
        $grid->removeColumn('mac');
        return $grid;
    }

    /**
     * @param $grid
     * @return mixed
     */
    public function stockOnhandColumns($grid){

        $grid->addColumn('price',
            array(
                'header' => $grid->__('Selling Price'),
                'index' => 'price',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            )
        );
        $grid->addColumn('total_inv_value',
            array(
                'header' => $grid->__('Inventory Value'),
                'index' => 'total_inv_value',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                'filter_condition_callback' => array($grid, '_filterInventoryCallback')
            )
        );
        $grid->addColumn('total_retail_value',
            array(
                'header' => $grid->__('Potential Revenue'),
                'index' => 'total_retail_value',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                'filter_condition_callback' => array($grid, '_filterInventoryCallback')
            )
        );
        $grid->addColumn('total_profit',
            array(
                'header' => $grid->__('Potential Profit'),
                'index' => 'total_profit',
                'align' => 'right',
                'type' => 'currency',
                'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                'filter_condition_callback' => array($grid, '_filterInventoryCallback')
            )
        );
        $grid->addColumn('total_profit_margin',
            array(
                'header' => $grid->__('Profit Margin (%)'),
                'index' => 'total_profit_margin',
                'align' => 'right',
                'type' => 'number',
                'filter_condition_callback' => array($grid, '_filterInventoryCallback')
            )
        );

        $removecolumn = Mage::getModel('reportsuccess/editcolumns')->getCollection()->addFieldToFilter('grid','stockonhandGridJsObject')
        ->getFirstItem();
        if($removecolumn){
            $columns = $removecolumn->getValue();
            $columns = explode(',',$columns);
            foreach($columns as $value){
                $column = explode(':',$value);
                if($column[1] == 0){
                    $grid->removeColumn($column[0]);
                }
            }

        }
        return $grid;
    }

}