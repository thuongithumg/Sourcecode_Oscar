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

/**
 * Class Magestore_Webpos_Model_Transaction
 */
class Magestore_Webpos_Model_Transaction extends Mage_Core_Model_Abstract
{

    const STATUS_ACTIVE = '1';
    const STATUS_INACTIVE = '2';

    const TRUE = '1';
    const FALSE = '0';

    /**
     * Contructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('webpos/shift_cashtransaction');
    }

    /**
     * @return mixed
     */
    protected function _beforeSave()
    {
        if(!$this->getCreatedAt()){
            $currentTime = strftime('%Y-%m-%d %H:%M:%S', Mage::getModel('core/date')->gmtTimestamp());
            $this->setCreatedAt($currentTime);
        }
        if(!$this->getStatus()){
            $this->setStatus(self::STATUS_ACTIVE);
        }
        if($this->getIsOpening() !== self::TRUE){
            $this->setIsOpening(self::FALSE);
        }
        $this->setIsManual(($this->getOrderIncrementId())?self::FALSE:self::TRUE);
        return parent::_beforeSave();
    }

    /**
     * @return mixed
     */
    protected function _afterLoad()
    {
        $status = $this->getStatus();
        $isManual = $this->getIsManual();
        $isOpening = $this->getIsOpening();
        $orderIncrementId = $this->getOrderIncrementId();
//        $createdAt = $this->getCreatedAt();
//        $createdAt = Mage::app()->getLocale()->storeDate(
//            Mage::app()->getStore(),
//            Varien_Date::toTimestamp($createdAt),
//            true
//        )->toString("MM/dd/YYYY HH:mm:ss");
//        $this->setCreatedAt($createdAt);
        $this->setIsActive(($status == self::STATUS_ACTIVE)?true:false);
        $this->setIsManual(($isManual == self::TRUE)?true:false);
        $this->setIsOpening(($isOpening == self::TRUE)?true:false);
        if($orderIncrementId == 0){
            $this->setOrderIncrementId(Mage::helper('webpos')->__('Manual'));
        }
        return parent::_afterLoad();
    }

    /**
     * get all transactions for a shift has shift_id
     * @param string $shift_id
     * @return mixed
     */
    public function getByShiftId($shift_id)
    {
        $data = array();

        $collection = $this->getCollection();
        $collection->addFieldToFilter("shift_id", $shift_id);

        foreach ($collection as $item) {
            $itemData = $this->convertDataToCurrentCurrency($item->getData());
            $data[] = $itemData;
        }

        return $data;
    }

    /** convert transaction data to current currency value
     * @param $data
     * @return mixed
     */
    public function convertDataToCurrentCurrency($data){
        $transactionBaseCurrencyCode = $data['base_currency_code'];
        $TransactionCurrencyCode = $data['transaction_currency_code'];
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        //convert all data of $shiftBaseCurrencyCode to $baseCurrencyCode
        if($transactionBaseCurrencyCode != $baseCurrencyCode){
            $data['base_currency_code'] = $baseCurrencyCode;
            $data['base_value'] = Mage::helper('directory')->currencyConvert($data['base_value'],$transactionBaseCurrencyCode, $baseCurrencyCode );
            $data['base_balance'] = Mage::helper('directory')->currencyConvert($data['base_balance'],$transactionBaseCurrencyCode, $baseCurrencyCode );

        }
        //convert all data of $shiftCurrencyCode to $currentCurrencyCode
        if ($TransactionCurrencyCode != $currentCurrencyCode){
            $data['shift_currency_code'] = $currentCurrencyCode;
            $data['value'] = Mage::helper('directory')->currencyConvert($data['value'],$TransactionCurrencyCode, $currentCurrencyCode);
            $data['balance'] = Mage::helper('directory')->currencyConvert($data['balance'],$TransactionCurrencyCode, $currentCurrencyCode);

        }
        return $data;
    }


}