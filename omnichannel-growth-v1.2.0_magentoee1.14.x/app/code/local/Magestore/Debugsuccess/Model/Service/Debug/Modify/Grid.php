<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 22/02/2017
 * Time: 15:09
 */
use Magestore_Debugsuccess_Helper_Data as Data;
class Magestore_Debugsuccess_Model_Service_Debug_Modify_Grid
{


    protected $MANAGE_STOCK = array(
        '0'=>'No',
        '1'=>'YES',
    );
    protected $IS_IN_STOCK = array(
        '0'=>'Out of Stock',
        '1'=>'In Stock',
    );

    public function Columns($grid,$type){
        if($type == Data::WRONG_QTY){
            return  $this->wrongQtyColumns($grid);
        }
    }

    /**
     * @param $grid
     * @return mixed
     */
    public function wrongQtyColumns($grid){


        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouseIds = array();
        foreach($warehouses as $key => $value){
            $warehouseIds[$value['warehouse_id']] = $value['warehouse_name'];
        }

        $grid->addColumn('product_id', array(
            'header' => $grid->__('ID'),
            'index' => 'product_id',
            'sortable' => true,
            'type' => 'number',
            'filter_condition_callback' => array($grid, '_filterDebugCallback')
        ));

        $grid->addColumn('is_in_stock', array(
            'header' => $grid->__('Stock Availability'),
            'align' => 'right',
            'width' => '150px',
            'type' => 'options',
            'options' => $this->IS_IN_STOCK,
            'index' => 'is_in_stock'

        ));
//        $grid->addColumn('manage_stock', array(
//            'header' => $grid->__('Manage Stock'),
//            'align' => 'right',
//            'width' => '150px',
//            'type' => 'options',
//            'options' => $this->MANAGE_STOCK,
//            'index' => 'manage_stock'
//        ));
        $grid->addColumn("qty",
            array(
                "header" => $grid->__("Catalog Qty"),
                "index" => "qty",
                'type' => 'number',
                "sortable" => true,
                'align' => 'left',
            )
        );
        $grid->addColumn('on_hold_qty', array(
                'header' => $grid->__('Catalog On-hold'),
                'align' => 'right',
                'width' => '150px',
                'type' => 'number',
                'index' => 'on_hold_qty',
                'filter_condition_callback' => array($grid, '_filterDebugCallback')
        ));
        foreach($warehouseIds as $id => $name){
            $grid->addColumn('warehouse_' . $id, array(
                'header' => $name,
                'align' => 'right',
                'width' => '150px',
                'value' => $id,
                'type' => 'number',
                'sortable' => false,
                'filter' => false,
                'renderer' => 'debugsuccess/adminhtml_debug_wrongqty_renderer_warehouseqty')
            );
        }
        return $grid;
    }

}