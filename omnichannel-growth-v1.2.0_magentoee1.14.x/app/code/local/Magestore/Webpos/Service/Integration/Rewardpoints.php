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

class Magestore_Webpos_Service_Integration_Rewardpoints extends Magestore_Webpos_Service_Checkout_Checkout
{
    /**
     * @return mixed
     */
    public function getRates(){
        $data = array();
        $message = array();
        if($this->_helper->isRewardPointsEnable()){
            $model = $this->_getModel('rewardpoints/rate');
            $collection = $model->getCollection();
            $collection->addFieldToFilter('status','1');
            $collection->load();
            $data['items'] = $collection->getData();
            $data['total_count'] = $collection->getSize();
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        }else{
            $message[] = $this->__('Reward Points module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }
        return (!empty($data))?$data:$this->getResponseData($data, $message, $status);
    }

    /**
     * @param $customerId
     * @return mixed
     */
    public function getBalance($customerId){
        $data = array();
        $message = array();
        if($this->_helper->isRewardPointsEnable()){
            $model = $this->_getModel('rewardpoints/customer');
            $model->load($customerId, 'customer_id');
            if($model->getId() > 0){
                $data['balance'] = floatval($model->getPointBalance());
            }else{
                $data['balance'] = floatval(0);
            }
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        }else{
            $message[] = $this->__('Reward Points module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @return mixed
     */
    public function getList(){
        $data = array();
        $message = array();
        if($this->_helper->isRewardPointsEnable()){
            $model = $this->_getModel('rewardpoints/customer');
            $collection = $model->getCollection();
            $collection->load();
            $data['items'] = $collection->getData();
            $data['total_count'] = $collection->getSize();
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        }else{
            $message[] = $this->__('Reward Points module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }

        return (!empty($data))?$data:$this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $spendData
     * @return mixed
     */
    public function spendPoint($quoteData, $spendData){
        $data = array();
        $message = array();
        if($this->_helper->isRewardPointsEnable()){
            $session = $this->_getModel('checkout/session');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
            if(!empty($spendData)){
                $orderCreateModel = $this->_startAction($quoteData);
                $quote = $orderCreateModel->getQuote();
                $session->setWebposQuoteId($quote->getId());
                $session->setData('use_point', true);
                $session->setRewardSalesRules(array(
                    'rule_id'   => $spendData[Magestore_Webpos_Api_Integration_RewardpointsInterface::RULE_ID],
                    'use_point' => $spendData[Magestore_Webpos_Api_Integration_RewardpointsInterface::USE_POINT]
                ));
                $quote->collectTotals()->save();
                $orderCreateModel->setQuote($quote);
                $this->_finishAction();
                $data = $this->_getQuoteData(array(), $orderCreateModel);
                $data['used_point'] = $this->_getHelper('rewardpoints/calculation_spending')->getTotalPointSpent();
            }else{
                $session->unsetData('use_point');
                $orderCreateModel = $this->_startAction($quoteData);
                $data = $this->_getQuoteData(array(), $orderCreateModel);
            }
        }else{
            $message[] = $this->__('Reward Points module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }
        return $this->getResponseData($data, $message, $status);
    }
}
