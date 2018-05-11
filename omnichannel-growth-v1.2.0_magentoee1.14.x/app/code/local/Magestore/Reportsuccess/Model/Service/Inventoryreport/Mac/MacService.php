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
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Inventoryreport_Mac_MacService
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Model_Service_Inventoryreport_Mac_MacService

{
    /**
     * @var Connection
     */
    protected $Connection ;

    /**
     * const
     */
    const _primaryField = 'product_id';
    const _order_item_id = 'item_id';

    /**
     * @var array
     */
    protected $_CONDITION = array();
    protected $_ITEMS_ID = array();
    protected $_INSERT_ITEMS = array();

    /**
     * Magestore_Reportsuccess_Model_Service_Inventoryreport_Mac_MacService constructor.
     */
    public function __construct(){
        $cr = Mage::getSingleton('core/resource');
        $this->Connection = $cr->getConnection('core_write');
    }

    /**
     * @param $data
     * @return mixed
     */
    public function updateMacValues($data = array()){

        try {
            $purchaseId = '';
            if ($data) {
                foreach($data as $d){
                    if(isset($d['purchase_order_id'])){
                        $purchaseId =  $d['purchase_order_id'];
                        break;
                    }
                }
            }
            if ($purchaseId) {
                $this->prepareDataToUpdate($purchaseId, $data);
                if ($this->_ITEMS_ID) {
                    if($this->_CONDITION){
                        $values = array(
                            'mac' => $this->Connection->getCaseSql(self::_primaryField, $this->_CONDITION['update_mac'], 0),
                        );
                    }else{
                        $values = '';
                    }
                    $where = array(self::_primaryField . ' IN (?)' => $this->_ITEMS_ID);
                    $query = array(
                        'insert' => $this->_INSERT_ITEMS,
                        'values' => $values,
                        'condition' => $where,
                        'table' => Mage::getSingleton('core/resource')->getTableName('reportsuccess/costofgood')
                    );
                    $queryAdapter = Mage::getResourceModel('reportsuccess/costofgood');
                    return $queryAdapter->_updateMacData($query);
                }
            }

        }catch (Exception $e){
            Mage::log($e->getMessage(),null,'log.log');
        }
    }

    /**
     * @param $purchaseId
     * @param $data
     */
    public function prepareDataToUpdate($purchaseId,$data){
        /* Mac = total cost after PO / total qty after Purchase Order  */
        /*
            A   = bằng chi phí trung bình trước đó (nhân) với số lượng còn trong kho(chưa tính qty chuẩn bị received từ PO này)
            Nếu trước đó ko có  chi phí trung bình -> sẽ = cost hiện tại trong PO
            B = tổng chi phí nhập hàng lần này (= cost nhân với qty nhập trong PO )
            MAC = A + B / (tổng qty từ A và B)
        */
        try {
            $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseId);
            $rateCost = '';
            $rateCode = '';
            if ($purchaseOrder->getId()) {
                $rateCost = $purchaseOrder->getCurrencyRate();
                $rateCode = $purchaseOrder->getCurrencyCode();
            }
            if ($rateCost && $rateCode) {
                foreach ($data as $value) {
                    $products = Mage::getModel('cataloginventory/stock_item')->getCollection()
                        ->addFieldToFilter('main_table.stock_id', 1)
                        ->addFieldToFilter('main_table.product_id', $value['product_id']);
                    $products->getSelect()->joinLeft(
                        array('mac' => Mage::getSingleton('core/resource')->getTableName('reportsuccess/costofgood')),
                        'main_table.product_id = mac.product_id', array(
                            'mac' => 'IFNULL(mac.mac,0)', 'inv_value' => 'main_table.qty*(IFNULL(mac.mac,0))',
                            'exist_product' => 'IF(mac.product_id,1,0)'
                        )
                    );
                    $qtyAfterReceived = '';
                    $mac = '';
                    $exist_product = '';
                    foreach ($products as $product) {
                        $qtyAfterReceived = $product->getQty();
                        $mac = $product->getMac();
                        $exist_product = $product->getExistProduct();
                    }
                    $base_cost_in_po = $value['cost'] / $rateCost;
                    $prepareMacupdate = '';
                    if (!$mac || $mac == 0) {
                        $prepareMacupdate = $base_cost_in_po;
                    } else {
                        $prepareMacupdate = (($mac * ($qtyAfterReceived - $value['transfer_qty'])) + ($base_cost_in_po * $value['transfer_qty'])) / $qtyAfterReceived;
                    }

                    $case = $this->Connection->quoteInto('?', $value['product_id']);
                    $this->_ITEMS_ID[] = $value['product_id'];
                    if($exist_product == 1){
                        $this->_CONDITION['update_mac'][$case] = $prepareMacupdate;
                    }else{
                        $this->_INSERT_ITEMS[] = array('product_id' => $value['product_id'], 'mac' =>  $prepareMacupdate);
                    }

                }
            }
        }catch ( Exception $e){
            Mage::log($e->getMessage(),null,'log.log');
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function updateMacInOrderItem($data){
        if(empty($data)){
            return array();
        }
        $list_product_ids = array_values($data);
        $mac = Mage::getModel('reportsuccess/costofgood')->getCollection()
            ->addFieldToFilter('product_id',array('in' => $list_product_ids));
        foreach($mac as $key => $value){
            foreach($data as $item_id => $product_id){
                if($product_id == $value->getProductId()){
                    $this->_ITEMS_ID[] = $item_id;
                    $case = $this->Connection->quoteInto('?',$item_id);
                    $this->_CONDITION['update_mac'][$case] = $value->getMac();
                }
            }
        }
        if ($this->_ITEMS_ID) {
            if($this->_CONDITION){
                $values = array(
                    'os_mac' => $this->Connection->getCaseSql(self::_order_item_id, $this->_CONDITION['update_mac'], 0),
                );
            }else{
                $values = '';
            }
            $where = array(self::_order_item_id . ' IN (?)' => $this->_ITEMS_ID);
            $query = array(
                'insert' => '',
                'values' => $values,
                'condition' => $where,
                'table' => Mage::getSingleton('core/resource')->getTableName('sales/order_item')
            );
            $queryAdapter = Mage::getResourceModel('reportsuccess/costofgood');
            return $queryAdapter->_updateMacData($query);
        }
    }

    /**
     * @param $p_id
     * @param $value
     * @return mixed
     */
    public function updateMacInline($p_id , $value){
            $_coreHelper = Mage::helper('core');
            $value = preg_replace( '/[^0-9,"."]/', '', $value );
            if(!is_numeric($value)){
                $mac = Mage::getModel('reportsuccess/costofgood')->getCollection()
                    ->addFieldToFilter('product_id',$p_id)
                    ->getFirstItem();
                if(!$mac->getId()){
                    return $_coreHelper->currency(0,true,false);
                }else{
                    return $_coreHelper->currency($mac->getMac(),true,false);
                }
            }
            $values = '';
            $where = '';
            $mac = Mage::getModel('reportsuccess/costofgood')->getCollection()
                ->addFieldToFilter('product_id',$p_id)
                ->getFirstItem();
            if(!$mac->getId()){
                $this->_INSERT_ITEMS[] = array('product_id' => $p_id, 'mac' =>  $value);
            }else{
                $this->_ITEMS_ID[] = $p_id;
                $case = $this->Connection->quoteInto('?',$p_id);
                $this->_CONDITION['update_mac'][$case] = $value;
                $where = array(self::_primaryField . ' IN (?)' => $this->_ITEMS_ID);
                $values = array(
                    'mac' => $this->Connection->getCaseSql(self::_primaryField, $this->_CONDITION['update_mac'], 0),
                );
            }
            $query = array(
                'insert' => $this->_INSERT_ITEMS,
                'values' => $values,
                'condition' => $where,
                'table' => Mage::getSingleton('core/resource')->getTableName('reportsuccess/costofgood')
            );
            $queryAdapter = Mage::getResourceModel('reportsuccess/costofgood');
            $queryAdapter->_updateMacData($query);
            return $_coreHelper->currency($value,true,false);

    }
}