<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model;

/**
 * class \Magestore\Webpos\Model\Transaction
 * 
 * Web POS Transaction model
 * Use to work with Web POS Transaction table
 * Methods:
 *  currentBalance
 *  getPreviousBalance
 *  saveTransactionData
 *  setTransactionFlag
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Transaction extends \Magento\Framework\Model\AbstractModel
{
    /**
     *
     * @var Magestore\Webpos\Helper\Data 
     */
    protected $_helper;
    
    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magestore\Webpos\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_helper = $helper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Transaction');
    }
    
    /**
     * 
     * @param int $store_id
     * @param int $userId
     * @param int $tillId
     * @return int
     */
    public function getCurrentBalance($store_id, $userId, $tillId) {
        if ($store_id == NULL && $store_id == '') {
            $store_id = 0;
        }
        if ($tillId == NULL && $tillId == '') {
            $tillId = 0;
        }
        $current_balance = 0;
        $collection = $this->getCollection()
                           ->addFieldToFilter('store_id',$store_id)
                           ->addFieldToFilter('user_id',$userId)
                           ->addFieldToFilter('till_id',$tillId)
                           ->addFieldToFilter('transac_flag',1)
                           ->setOrder('transaction_id', 'DESC');
        if($collection->getSize() > 0){
            $current_balance = $collection->getFirstItem()->getData('current_balance');
        }
        return $current_balance;
    }
    
    /**
     * 
     * @param int $store_id
     * @param int $userId
     * @param int $tillId
     * @return string
     */
    public function currentBalance($store_id, $userId, $tillId) {
        $current_balance = $this->getCurrentBalance($store_id, $userId, $tillId);
        return $this->_helper->formatPrice($current_balance);
    }
    
    /**
     * 
     * @param int $store_id
     * @param int $userId
     * @param int $tillId
     * @return int
     */
    public function getPreviousBalance($store_id, $userId, $tillId) {
        if ($store_id == NULL && $store_id == '') {
            $store_id = 0;
        }
        if ($tillId == NULL && $tillId == '') {
            $tillId = 0;
        }
        $current_balance = 0;
        $collection = $this->getCollection()
                           ->addFieldToFilter('store_id',$store_id)
                           ->addFieldToFilter('user_id',$userId)
                           ->addFieldToFilter('till_id',$tillId)
                           ->addFieldToFilter('transac_flag',1)
                           ->setOrder('transaction_id', 'DESC');
        if($collection->getSize() > 0){
            $current_balance = $collection->getFirstItem()->getData('previous_balance');
        }
        return $current_balance;
    }
    
    /**
     * save transaction data
     * @param array $data
     * @return array
     */
    public function saveTransactionData($data) {
        $return = array(
            'msg' => __('Error! Please recheck the form OR contact administrator for more details.'),
            'error' => true);
        
        $store_id = $data['store_id'];
        if ($store_id == "" || $store_id == NULL) {
            $store_id = 0;
        }
        /*
        $current_balance = "SELECT `current_balance` FROM " . $tableName . " WHERE  `store_id` = '" . $store_id . "' AND `user_id` = '" . $data['user_id'] . "' AND `till_id` = '" . $data['till_id'] . "' AND `location_id` = '" . $data['location_id'] . "' ORDER BY `transaction_id` DESC LIMIT 1";
        
        $current_balance = $readConnection->fetchOne($current_balance);
        */
        
        $current_balance = $this->getCurrentBalance($store_id, $data['user_id'], $data['till_id']);
        /*
        $previous_balance = "SELECT `previous_balance` FROM " . $tableName . " WHERE  `store_id` = '" . $store_id . "' AND `user_id` = '" . $data['user_id'] . "' AND `till_id` = '" . $data['till_id'] . "' AND `location_id` = '" . $data['location_id'] . "' ORDER BY `transaction_id` DESC LIMIT 1";
        $previous_balance = $readConnection->fetchOne($previous_balance);
        */
        
        $previous_balance = $this->getPreviousBalance($store_id, $data['user_id'], $data['till_id']);
        
        $now = date('Y-m-d H:i:s');
        $model = $this;
        $model->setId(null);
        $model->setData('created_time',$now);
        $model->setData('order_id',__('Manual'));
        $model->setData('previous_balance',$previous_balance );
        $model->setData('current_balance',$current_balance);
        $model->setData('user_id',$data['user_id']);
        $model->setData('comment',$data['note']);
        $model->setData('store_id',$store_id);
        $model->setData('transac_flag','1');
        $model->setData('till_id',$data['till_id']);
        $model->setData('location_id',$data['location_id']);
        
        switch ($data['type']) {
            case 'in':
                $previous_balance = $current_balance;
                $current_balance += $data['amount'];
                if (!isset($data['user_id']))
                    $data['user_id'] = NULL;
                $model->setData('cash_in',$data['amount']);
                $model->setData('type','in');
                $model->setData('payment_method','cash_in');
                $model->setData('previous_balance',$previous_balance );
                $model->setData('current_balance',$current_balance);
                $model->setData('user_id',$data['user_id']);
                break;

            case 'out':
                if ($data['type'] == 'out' && $current_balance >= $data['amount']) {
                    $previous_balance = $current_balance;
                    $current_balance -= $data['amount'];
                    if (!isset($data['user_id']))
                        $data['user_id'] = NULL;
                    
                    $model->setData('cash_out',$data['amount']);
                    $model->setData('type','out');
                    $model->setData('payment_method','cash_out');
                    $model->setData('previous_balance',$previous_balance );
                    $model->setData('current_balance',$current_balance);
                    $model->setData('user_id',$data['user_id']);
                } else {
                    $return['msg'] = __('You can NOT withdraw an amount of money which is greater than the Current Balance');
                    $return['error'] = true;
                }

                break;
            default:
                $amount = $data['cash_in'] - $data['cash_out'];
                $note = "";
                $previous_balance = $current_balance;
                $current_balance += $amount;
                $type = ($amount > 0) ? 'in' : 'out';
                $model->setData('cash_in',$data['cash_in']);
                $model->setData('cash_out',$data['cash_out']);
                $model->setData('type',$type);
                $model->setData('payment_method',$data['payment_method']);
                $model->setData('previous_balance',$previous_balance );
                $model->setData('current_balance',$current_balance);
                $model->setData('order_id',$data['order_id']);
                $model->setData('comment',$note);
                break;
        }
        try{
            $model->save();
            if($model->getId()){
                $return['msg'] = __('The transaction has been saved successfully.');
                $return['error'] = false;
            }else {
                $return['msg'] = __('Can NOT save this transaction');
                $return['error'] = true;
            }
        }catch(\Exception $e){
            $return['msg'] = $e->getMessage();
            $return['error'] = true;
        }
        return $return;
    }
    
    /**
     * change transaction flag after close store
     * @param int $till_id
     * @param int $store_id
     */
    public function setTransacsionFlag($till_id, $store_id = 'NULL') {
        if ($store_id == 'NULL' || $store_id == "") {
            $transac_collection = $this->getCollection()
                    ->addFieldToFilter('store_id', array('eq' => 0))
                    ->addFieldToFilter('till_id', array('eq' => $till_id))
                    ->addFieldToFilter('transac_flag', array('eq' => '1'));
        } else {
            $transac_collection = $this->getCollection()
                    ->addFieldToFilter('store_id', array('eq' => $store_id))
                    ->addFieldToFilter('till_id', array('eq' => $till_id))
                    ->addFieldToFilter('transac_flag', array('eq' => '1'));
        }
        if(count($transac_collection) > 0){
            foreach ($transac_collection as $transac) {
                $transac->setData('transac_flag', '0');
                $transac->save();
            }
        }
    }
    
    
}