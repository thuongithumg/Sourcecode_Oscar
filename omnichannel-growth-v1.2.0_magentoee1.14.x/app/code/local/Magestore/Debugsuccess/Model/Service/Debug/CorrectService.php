<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 19/02/2017
 * Time: 17:08
 */

class Magestore_Debugsuccess_Model_Service_Debug_CorrectService
{

     const SIZE = 1;
     const _primaryField = 'item_id';

     const _REASON_1 = 'Collect by Catalog - Update to Warehouse';
     const _REASON_2 = 'Collect by warehouse - Update to Warehouse';
     const _REASON_2_2 = 'Collect by warehouse - Update to Catalog';


     protected $_SQL_CORRECT_QTY;
     protected $_WAREHOUSE_ID;
     protected $_ALL_WAREHOUSE_IDS;
     protected $_CONDITION = array();
     protected $_ITEMS_ID = array();
     protected $_REMAIN_ID ;

     protected $_INSERT_HISTORY_COLLECT = array();

    /**
     * @param $product
     * @param $warehouse
     */
     public function correctQty($product,$warehouse){
            $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
            $this->_ALL_WAREHOUSE_IDS = $warehouses->getAllIds();
            $product = Mage::helper('debugsuccess')->base64Decode($product);
            $this->_WAREHOUSE_ID = Mage::helper('debugsuccess')->base64Decode($warehouse);
            return  $this->CollectQty($product);
     }

    /**
     * @param $product
     */
    public function CollectQty($product){

        $product = explode(',',$product);
        if (sizeof($product) > self::SIZE){
            $ids_need_correct = array_slice($product, 0, self::SIZE, true);
            $remain_ids = array_diff($product,$ids_need_correct);
            $this->_REMAIN_ID = $remain_ids;
            return $this->correctbyId($ids_need_correct);
        }else{
            return $this->correctbyId($product);
        }
    }


    /**
     * @param $ids
     * @return string
     */
    public function correctbyId($ids){
        $this->addwarehouseIdTorOrderItemsNoneWarehouse($ids);
        $Collection = Mage::getResourceModel('debugsuccess/wrongqty_collection')->getprepareCollectionToCollect($ids);
        $result = $this->_prepareData($Collection);
        if($result){
            $products = $this->_REMAIN_ID;
            $remain_size = sizeof($products);

            $products = ($products) ? implode(',',$products) : 0;

            $products = Mage::helper('debugsuccess')->base64Encode($products);
            $warehouseId = Mage::helper('debugsuccess')->base64Encode($this->_WAREHOUSE_ID);

            if($remain_size == 0){
                $warehouseId = 0;
                $products = 0;
            }
            $data =  array('product_remain'=>$products,
                           'warehouse_id' => $warehouseId,
                           'remain_size' => $remain_size
                        );
            return json_encode($data);
        }else{
            $data =  array('product_remain'=>0,
                'warehouse_id' => 0,
                'remain_size' => 0
            );
            return json_encode($data);
        }
    }

    /**
     * @param $Collection
     * @return array
     */
    public function _prepareData($Collection){
        foreach($Collection as $data){
                $this->_addData($data);
        }

        if(!$this->_CONDITION){
            return array();
        }

        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');

        $values = array(
            'qty' => $connection->getCaseSql(self::_primaryField, $this->_CONDITION['convert_qty'],0),
            'total_qty' => $connection->getCaseSql(self::_primaryField, $this->_CONDITION['convert_totalqty'],0),
            'is_in_stock' => $connection->getCaseSql(self::_primaryField, $this->_CONDITION['convert_is_in_stock'],0),
        );
        $where = array(self::_primaryField.' IN (?)' => $this->_ITEMS_ID);
        /* query to update warehouse_id */

        $insert = $this->_INSERT_HISTORY_COLLECT;
        $query = array(
            'insert' => $insert,
            'values' => $values,
            'condition' => $where,
            'table' => Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item')
        );

        $queryAdapter = Mage::getResourceModel('debugsuccess/wrongqty');
        return $queryAdapter->updateTable($query);

    }

    /**
     * @param $data
     */
    public function _addData($data)
    {
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');

        if ($this->_WAREHOUSE_ID) {
            $temp_qty = $data->getQty();
            foreach ($this->_ALL_WAREHOUSE_IDS as $w_id) {
                if ($w_id == $this->_WAREHOUSE_ID) {
                    continue;
                }
                if ($data->getData('sum_qty_to_ship_' . $w_id) != $data->getData('on_hold_qty_' . $w_id)) {
                    $case = $connection->quoteInto('?', $data->getData('item_id' . $w_id));
                    $qty = $data->getData('available_qty_' . $w_id);
                    $totalQty = $data->getData('available_qty_' . $w_id) + $data->getData('on_hold_qty_' . $w_id);
                    $this->_ITEMS_ID[] = $data->getData('item_id' . $w_id);
                    $this->_CONDITION['convert_qty'][$case] = $qty;
                    $this->_CONDITION['convert_totalqty'][$case] = $totalQty;
                    $this->_CONDITION['convert_is_in_stock'][$case] = ($qty > 0) ? 1 : 0;

                    $temp_qty = $temp_qty - $qty;

                        $item = array();
                        $item['product_id'] = $data->getProductId();
                        $item['warehouse_id'] = $w_id;
                        $item['old_total_qty'] = $data->getData('sum_total_qty_' . $w_id);
                        $item['old_qty'] = $data->getData('available_qty_' . $w_id);
                        $item['total_qty'] = $totalQty;
                        $item['qty'] = $qty;
                        $item['reason'] = self::_REASON_1;
                        $item['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                    $this->_INSERT_HISTORY_COLLECT[] = $item;
                }
                if ($data->getData('sum_qty_to_ship_' . $w_id) == $data->getData('on_hold_qty_' . $w_id)) {
                    $temp_qty = $temp_qty - $data->getData('available_qty_' . $w_id);
                }
            }
            $case = $connection->quoteInto('?', $data->getData('item_id' . $this->_WAREHOUSE_ID));
            $qty = $temp_qty;
            $totalQty = $temp_qty + $data->getData('on_hold_qty_' . $this->_WAREHOUSE_ID);
            $this->_ITEMS_ID[] = $data->getData('item_id' . $this->_WAREHOUSE_ID);
            $this->_CONDITION['convert_qty'][$case] = $qty;
            $this->_CONDITION['convert_totalqty'][$case] = $totalQty;
            $this->_CONDITION['convert_is_in_stock'][$case] = ($qty > 0) ? 1 : 0;

                $item = array();
                $item['product_id'] = $data->getProductId();
                $item['warehouse_id'] = $this->_WAREHOUSE_ID;
                $item['old_total_qty'] = $data->getData('sum_total_qty_' . $this->_WAREHOUSE_ID);
                $item['old_qty'] = $data->getData('available_qty_' . $this->_WAREHOUSE_ID);
                $item['total_qty'] = $totalQty;
                $item['qty'] = $qty;
                $item['reason'] = self::_REASON_1;
                $item['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $this->_INSERT_HISTORY_COLLECT[] = $item;

        }else{
            $temp_qty = 0;
            $on_hold = 0;
            foreach ($this->_ALL_WAREHOUSE_IDS as $w_id) {
                if ($data->getData('sum_qty_to_ship_' . $w_id) != $data->getData('on_hold_qty_' . $w_id)) {
                    $case = $connection->quoteInto('?', $data->getData('item_id' . $w_id));
                    $qty = $data->getData('available_qty_' . $w_id);
                    $totalQty = $data->getData('available_qty_' . $w_id) + $data->getData('on_hold_qty_' . $w_id);
                    $this->_ITEMS_ID[] = $data->getData('item_id'.$w_id);
                    $this->_CONDITION['convert_qty'][$case] = $qty;
                    $this->_CONDITION['convert_totalqty'][$case] = $totalQty;
                    $this->_CONDITION['convert_is_in_stock'][$case] = ($qty > 0) ? 1 : 0;

                    $temp_qty = $temp_qty + $qty;

                        $item = array();
                        $item['product_id'] = $data->getProductId();
                        $item['warehouse_id'] = $w_id;
                        $item['old_total_qty'] = $data->getData('sum_total_qty_' . $w_id);
                        $item['old_qty'] = $data->getData('available_qty_' . $w_id);
                        $item['total_qty'] = $totalQty;
                        $item['qty'] = $qty;
                        $item['reason'] = self::_REASON_2;
                        $item['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                    $this->_INSERT_HISTORY_COLLECT[] = $item;

                    $on_hold += $data->getData('on_hold_qty_' . $w_id);

                }
                if ($data->getData('sum_qty_to_ship_' . $w_id) == $data->getData('on_hold_qty_' . $w_id)) {
                    $temp_qty = $temp_qty + $data->getData('available_qty_' . $w_id);
                }
            }
            $case = $connection->quoteInto('?', $data->getData('item_id'));
            $qty = $temp_qty;
            $totalQty = $temp_qty + $data->getData('on_hold_qty')-$on_hold;
            $this->_ITEMS_ID[] = $data->getData('item_id');
            $this->_CONDITION['convert_qty'][$case] = $qty;
            $this->_CONDITION['convert_totalqty'][$case] = $totalQty;
            $this->_CONDITION['convert_is_in_stock'][$case] = ($qty > 0) ? 1 : 0;

                $item = array();
                $item['product_id'] = $data->getProductId();
                $item['warehouse_id'] = 1;
                $item['old_total_qty'] = $data->getData('total_qty');
                $item['old_qty'] = $data->getData('qty');
                $item['total_qty'] = $totalQty;
                $item['qty'] = $qty;
                $item['reason'] = self::_REASON_2_2;
                $item['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $this->_INSERT_HISTORY_COLLECT[] = $item;

        }
    }

    /**
     * @param $ids
     */
    public function addwarehouseIdTorOrderItemsNoneWarehouse($ids){
        $primarywarehouseService = $this->warehouseservice()->getPrimaryWarehouse();
        $primarywarehouseId = $primarywarehouseService->getId();
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');
        $field = 'item_id';
        $orderItems = Mage::getResourceModel('debugsuccess/wrongqty_collection')->getOrderItemNoneWarehouse($ids);
        if(sizeof($orderItems) > 0){
            foreach($orderItems as $key => $value){
                    $item = array();
                    $item['product_id'] = $value['product_id'];
                    $item['warehouse_id'] = $this->_WAREHOUSE_ID ? $this->_WAREHOUSE_ID : $primarywarehouseId;
                    $item['reason'] = "Add warehouse ID into OrderItem-none-warehouse: List OrderId (".$value['order_ids'].")";
                    $item['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                    $insert[] = $item;

                    $updateValues  = array(
                        'warehouse_id'  => $item['warehouse_id'],
                    );
                    $item_ids = explode(',',$value['item_ids']);
                    $table =  Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                    $where =  $connection->quoteInto(Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item') . '.' . $field . ' IN (?) ', $item_ids);

                $query = array(
                    'insert' => $item,
                    'values' => $updateValues,
                    'condition' =>  $where,
                    'table' =>$table
                );
                $queryAdapter = Mage::getResourceModel('debugsuccess/wrongqty');
                $queryAdapter->updateTable($query);
            }
        }
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    public function warehouseservice(){
        return Magestore_Coresuccess_Model_Service::warehouseService();
    }

}