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


class Magestore_Webpos_Model_Cart_Data_QuoteDataInit extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Cart_QuoteDataInitInterface
{
    /**
     * @return string
     */
    public function getId(){
        return $this->getData(self::ID);
    }

    /**
     * @param string $id
     * @return Magestore_Webpos_Api_Cart_ItemRequestInterface
     */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getStoreId(){
        return $this->getData(self::STORE_ID);
    }
    /**
     * @param string $storeId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setStoreId($storeId){
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @return string
     */
    public function getCustomerId(){
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param string $customerId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setCustomerId($customerId){
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @return string
     */
    public function getCurrencyId(){
        return $this->getData(self::CURRENCY_ID);
    }

    /**
     * @param string $currencyId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setCurrencyId($currencyId){
        return $this->setData(self::CURRENCY_ID, $currencyId);
    }

    /**
     * @return string
     */
    public function getTillId(){
        return $this->getData(self::TILL_ID);
    }

    /**
     * @param string $tillId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setTillId($tillId){
        return $this->setData(self::TILL_ID, $tillId);
    }

    /**
     * @return string
     */
    public function getShiftId(){
        return $this->getData(self::SHIFT_ID);
    }

    /**
     * @param string $shiftId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setShiftId($shiftId){
        return $this->setData(self::SHIFT_ID, $shiftId);
    }
}
