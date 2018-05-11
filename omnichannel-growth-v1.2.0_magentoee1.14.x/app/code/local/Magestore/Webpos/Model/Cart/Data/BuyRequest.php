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


class Magestore_Webpos_Model_Cart_Data_BuyRequest extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Cart_BuyRequestInterface
{
    /**
     * @return string
     */
    public function getItemId(){
        return $this->getData(self::ITEM_ID);
    }

    /**
     * @param string $itemId
     * @return string
     */
    public function setItemId($itemId){
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * @return string
     */
    public function getId(){
        return $this->getData(self::ID);
    }

    /**
     * @param string $id
     * @return string
     */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getQty(){
        return $this->getData(self::QTY);
    }

    /**
     * @param string $qty
     * @return string
     */
    public function setQty($qty){
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @return array
     */
    public function getSuperAttribute(){
        return $this->getData(self::SUPER_ATTRIBUTE);
    }

    /**
     * @param array $superAttribute
     * @return array
     */
    public function setSuperAttribute($superAttribute){
        return $this->setData(self::SUPER_ATTRIBUTE, $superAttribute);
    }

    /**
     * @return array
     */
    public function getOptions(){
        return $this->getData(self::OPTIONS);
    }

    /**
     * @param array $options
     * @return array
     */
    public function setOptions($options){
        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * @return array
     */
    public function getBundleOption(){
        return $this->getData(self::BUNDLE_OPTION);
    }

    /**
     * @param array $bundleOptions
     * @return array
     */
    public function setBundleOption($bundleOptions){
        return $this->setData(self::BUNDLE_OPTION, $bundleOptions);
    }

    /**
     * @return array
     */
    public function getBundleOptionQty(){
        return $this->getData(self::BUNDLE_OPTION_QTY);
    }

    /**
     * @param array $bundleOptionsQty
     * @return array
     */
    public function setBundleOptionQty($bundleOptionsQty){
        return $this->setData(self::BUNDLE_OPTION_QTY, $bundleOptionsQty);
    }

    /**
     * @return string
     */
    public function getCustomPrice(){
        return $this->getData(self::CUSTOM_PRICE);
    }

    /**
     * @param string $customPrice
     * @return string
     */
    public function setCustomPrice($customPrice){
        return $this->setData(self::CUSTOM_PRICE, $customPrice);
    }

    /**
     * @return boolean
     */
    public function getIsCustomSale(){
        return $this->getData(self::IS_CUSTOM_SALE);
    }

    /**
     * @return string
     */
    public function getUseDiscount(){
        return $this->getData(self::USE_DISCOUNT);
    }

    /**
     * @param string $useDiscount
     * @return string
     */
    public function setUseDiscount($useDiscount){
        return $this->setData(self::USE_DISCOUNT, $useDiscount);
    }

    /**
     * @param boolean $isCustomSale
     * @return boolean
     */
    public function setIsCustomSale($isCustomSale){
        return $this->setData(self::IS_CUSTOM_SALE, $isCustomSale);
    }

    /**
     * @return array
     */
    public function getQuoteItemData(){
        return $this->getData(self::QUOTE_ITEM_DATA);
    }

    /**
     * @param array $quoteItemData
     * @return array
     */
    public function setQuoteItemData($quoteItemData){
        return $this->setData(self::QUOTE_ITEM_DATA, $quoteItemData);
    }

    /**
     * @return array
     */
    public function getExtensionData(){
        return $this->getData(self::EXTENSION_DATA);
    }

    /**
     * @param array $extensionData
     * @return array
     */
    public function setExtensionData($extensionData){
        return $this->setData(self::EXTENSION_DATA, $extensionData);
    }

    /**
     * @return mixed
     */
    public function getAmount(){
        return $this->getData(self::CUSTOMERCREDIT_AMOUNT);
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function setAmount($amount){
        return $this->setData(self::CUSTOMERCREDIT_AMOUNT, $amount);
    }

    /**
     * @return mixed
     */
    public function getCreditPriceAmount(){
        return $this->getData(self::CUSTOMERCREDIT_PRICE_AMOUNT);
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function setCreditPriceAmount($amount){
        return $this->setData(self::CUSTOMERCREDIT_PRICE_AMOUNT, $amount);
    }
}
