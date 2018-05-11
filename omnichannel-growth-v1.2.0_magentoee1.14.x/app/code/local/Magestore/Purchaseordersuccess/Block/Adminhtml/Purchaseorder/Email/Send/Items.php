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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as Status;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Email_Send_Items
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Email_Header
{
    /**
     * @var Mage_Directory_Model_Currency
     */
    protected $currency;
    
    protected $_template = 'purchaseordersuccess/purchaseorder/email/items.phtml';

    public function checkIsShowQtyReceived(){
        $status = $this->purchaseOrder->getStatus();
        return in_array($status, array(Status::STATUS_COMPLETED, Status::STATUS_CANCELED));
    }
    
    public function getPurchaseOrderItems(){
        return $this->purchaseOrder->getItems();
    }

    /**
     * @param float $price
     * @return string
     */
    public function formatTxt($price){
        if (!$this->currency)
            $this->currency = Mage::getModel('directory/currency')->load($this->purchaseOrder->getCurrencyCode());
        return $this->currency->formatTxt($price);
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item$item
     * @return string
     */
    public function getCost($item){
       return $this->formatTxt($item->getCost() * 1);
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $item
     */
    public function getItemTotal($item){
        $itemQty = $item->getQtyOrderred();
        $itemTotal = $itemQty * $item->getCost();
        $itemDiscount = $itemTotal*$item->getDiscount()/100;
        $taxType = $this->getTaxType();
        if($taxType == 0){
            $itemTax = $itemTotal*$item->getTax()/100;
        }else{
            $itemTax = ($itemTotal-$itemDiscount)*$item->getTax()/100;
        }
        return $this->formatTxt(($itemTotal-$itemDiscount+$itemTax)*1);
    }
}