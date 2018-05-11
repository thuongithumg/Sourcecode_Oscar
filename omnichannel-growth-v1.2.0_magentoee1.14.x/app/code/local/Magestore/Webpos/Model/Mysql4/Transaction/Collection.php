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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/shift_cashtransaction');
    }

    /**
     * @param $cashDrawerId
     */
    public function deactiveByCashDrawerId($cashDrawerId){
        if($cashDrawerId){
            $collection = $this->addFieldToFilter('till_id', $cashDrawerId)->addFieldToFilter('status', Magestore_Webpos_Model_Transaction::STATUS_ACTIVE);
            if($collection->getSize() > 0){
                $resourceTransaction = Mage::getModel('core/resource_transaction');
                foreach ($collection as $transaction){
                    $transaction->setStatus(Magestore_Webpos_Model_Transaction::STATUS_INACTIVE);
                    $resourceTransaction->addObject($transaction);
                }
                try{
                    $resourceTransaction->save();
                } catch(Exception $e) {
                    Mage::log($e->getMessage(), null, 'system.log', true);
                }
            }
        }
    }

    /**
     * @param $cashDrawerId
     * @return Magestore_Webpos_Model_Mysql4_Transaction_Collection
     */
    public function getActiveTransactions($cashDrawerId = false){
        $this->addFieldToFilter('status', Magestore_Webpos_Model_Transaction::STATUS_ACTIVE);
        if($cashDrawerId !== false && !empty($cashDrawerId)){
            $this->addFieldToFilter('till_id', $cashDrawerId);
        }
        return $this;
    }

    /**
     * @param $shiftId
     * @return void
     */
    public function deactiveByShiftId($shiftId){
        if($shiftId){
            $collection = $this->addFieldToFilter('shift_id', $shiftId)
                ->addFieldToFilter('status', Magestore_Webpos_Model_Transaction::STATUS_ACTIVE);
            if($collection->getSize() > 0){
                $resourceTransaction = Mage::getModel('core/resource_transaction');
                foreach ($collection as $transaction){
                    $transaction->setStatus(Magestore_Webpos_Model_Transaction::STATUS_INACTIVE);
                    $resourceTransaction->addObject($transaction);
                }
                try{
                    $resourceTransaction->save();
                } catch(Exception $e) {
                    Mage::log($e->getMessage(), null, 'system.log', true);
                }
            }
        }
    }

    /**
     * @param $shiftId
     * @return Magestore_Webpos_Model_Mysql4_Transaction_Collection
     */
    public function getTransactionsByShiftId($shiftId = false){
        if($shiftId !== false && !empty($shiftId)){
            $this->addFieldToFilter('shift_id', $shiftId);
        }
        return $this;
    }

    /**
     * @param $shiftId
     * @return Magestore_Webpos_Model_Mysql4_Transaction_Collection
     */
    public function getActiveTransactionsByShift($shiftId = false){
        $this->addFieldToFilter('status', Magestore_Webpos_Model_Transaction::STATUS_ACTIVE);
        if($shiftId !== false && !empty($shiftId)){
            $this->addFieldToFilter('shift_id', $shiftId);
        }
        return $this;
    }

    /**
     * @param $shiftId
     * @return Magestore_Webpos_Model_Mysql4_Transaction_Collection
     */
    public function getCashAddTransaction($shiftId = false){
        if($shiftId !== false && !empty($shiftId)){
            $this->addFieldToFilter('shift_id', $shiftId);
            $this->addFieldToFilter('order_increment_id', 0);
            $this->addFieldToFilter('base_amount', array('gt' => 0));
        }
        return $this;
    }

    /**
     * @param $shiftId
     * @return Magestore_Webpos_Model_Mysql4_Transaction_Collection
     */
    public function getCashRemoveTransaction($shiftId = false){
        if($shiftId !== false && !empty($shiftId)){
            $this->addFieldToFilter('shift_id', $shiftId);
            $this->addFieldToFilter('order_increment_id', 0);
            $this->addFieldToFilter('base_amount', array('lt' => 0));
        }
        return $this;
    }

    /**
     * @param $shiftId
     * @return Magestore_Webpos_Model_Mysql4_Transaction_Collection
     */
    public function getCashSalesTransaction($shiftId = false){
        if($shiftId !== false && !empty($shiftId)){
            $this->addFieldToFilter('shift_id', $shiftId);
            $this->addFieldToFilter('order_increment_id', array('neq'=> '0'));
            $this->addFieldToFilter('base_amount', array('gt' => 0));
        }
        return $this;
    }
}