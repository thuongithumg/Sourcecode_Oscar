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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Giftvoucher_Model_Total_Pdf_Giftvoucher
 */
class Magestore_Giftvoucher_Model_Total_Pdf_Giftvoucher extends Mage_Sales_Model_Order_Pdf_Total_Default
{
	/**
	 * @return array
     */
	public function getTotalsForDisplay(){
		$amount = $this->getAmount();
		$fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
		if(floatval($amount)){
			$amount = $this->getOrder()->formatPriceTxt($amount);
			if ($this->getAmountPrefix()){
				$discount = $this->getAmountPrefix().$discount;
			}
			$totals = array(array(
				'label' => Mage::helper('giftvoucher')->__('Gift Card (%s):',$this->getGiftCodes()),
				'amount' => $amount,
				'font_size' => $fontSize,
				)
			);	
			return $totals;
		}
	}

	/**
	 * @return mixed
     */
	public function getAmount(){
        if ($this->getSource()->getGiftVoucherDiscount()) {
            return -$this->getSource()->getGiftVoucherDiscount();
        }
		return -$this->getOrder()->getGiftVoucherDiscount();
	}

    /**
     * @return mixed
     */
    public function getGiftCodes(){
		return $this->getOrder()->getGiftCodes();
	}
}
