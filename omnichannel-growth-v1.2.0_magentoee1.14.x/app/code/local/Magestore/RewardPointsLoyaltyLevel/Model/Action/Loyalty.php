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
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Action Earn Point for Order
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Model_Action_Loyalty
    extends Magestore_RewardPoints_Model_Action_Abstract
    implements Magestore_RewardPoints_Model_Action_Interface
{
    /**
     * Calculate and return point amount that customer earned from order
     * 
     * @return int
     */
    public function getPointAmount()
    {
        $level = $this->getData('action_object');
        return -(int)$level->getDemeritPoints();
    }
    
    /**
     * get Label for this action, this is the reason to change 
     * customer reward points balance
     * 
     * @return string
     */
    public function getActionLabel()
    {
        return Mage::helper('rewardpointsloyaltylevel')->__('Subtract points when join group');
    }
    
    public function getActionType()
    {
        return Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_SPEND;
    }
    
    /**
     * get Text Title for this action, used when create an transaction
     * 
     * @return string
     */
    public function getTitle()
    {
        $level = $this->getData('action_object');
        return Mage::helper('rewardpoints')->__('Subtract points when join group "%s"', $level->getLevelName());
    }
    
    /**
     * get HTML Title for action depend on current transaction
     * 
     * @param Magestore_RewardPoints_Model_Transaction $transaction
     * @return string
     */
    public function getTitleHtml($transaction = null)
    {
        if (is_null($transaction)) {
            return $this->getTitle();
        }
        return $transaction->getTitle();
    }
    
    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     * 
     * @return Magestore_RewardPoints_Model_Action_Interface
     */
    public function prepareTransaction()
    {
        $level = $this->getData('action_object');
        
        $transactionData = array(
            'status'    => Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED,
            'store_id'      => Mage::app()->getStore()->getId(),
        );        
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
