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
 * WebPOS Rewrite Quote Discount Model
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Model_Quote_Discount extends Mage_SalesRule_Model_Quote_Discount
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $type = $quote->getData('webpos_cart_discount_type');
        $discountValue = $quote->getData('webpos_cart_discount_value');
        $discountName = $quote->getData('webpos_cart_discount_name');

        if (empty($discountName)) {
            $discountName = Mage::helper('webpos')->__('Custom Discount');
        }

        if(isset($type) && isset($discountValue) && $discountValue > 0){
            if($type == '%')
            {
                $discountPercentFormatted = round($discountValue, 2);
                $quote->setWebposDiscountAmount(0)
                ->setWebposDiscountPercent($discountValue)
                ->setWebposDiscountDesc("$discountName $discountPercentFormatted%");
            }
            else{
                $quote->setWebposDiscountAmount($discountValue)
                ->setWebposDiscountPercent(0)
                ->setWebposDiscountDesc($discountName);
            }
        }else{
            return $this;
        }

        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        $maxPercent = Mage::helper('webpos/permission')->getMaximumDiscountPercent();
        $quoteCurrency = $quote->getQuoteCurrencyCode();
        $baseCurrency = $quote->getBaseCurrencyCode();

		$showItemPriceInclTax = Mage::getStoreConfig('tax/cart_display/price');
        if ($type == '%') {
            $discountPercent = ($maxPercent > $discountValue)?$discountValue:$maxPercent;
            foreach ($items as $item) {
                if ($item->getParentItemId()) continue;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $itemBasePrice = ($showItemPriceInclTax != 1) ? $child->getBasePriceInclTax() : $child->getBasePrice();
                        $baseDiscount = $child->getQty() * $itemBasePrice * $discountPercent / 100;
                        $baseDiscount = min($baseDiscount, $child->getQty() * $itemBasePrice - $child->getBaseDiscountAmount());

                        $discount = ($baseCurrency == $quoteCurrency)?$baseDiscount:Mage::helper('directory')->currencyConvert($baseDiscount, $baseCurrency, $quoteCurrency);

                        $child->setDiscountAmount($child->getDiscountAmount() + $discount)
                            ->setBaseDiscountAmount($child->getBaseDiscountAmount() + $baseDiscount);

                        $this->_addAmount(-$discount);
                        $this->_addBaseAmount(-$baseDiscount);
                    }
                } else {
                    $itemBasePrice = ($showItemPriceInclTax != 1) ? $item->getBasePriceInclTax() : $item->getBasePrice();
                    $baseDiscount = $item->getQty() * $itemBasePrice * $discountPercent / 100;
                    $baseDiscount = min($baseDiscount, $item->getQty() * $itemBasePrice - $item->getBaseDiscountAmount());

                    $discount = ($baseCurrency == $quoteCurrency)?$baseDiscount:Mage::helper('directory')->currencyConvert($baseDiscount, $baseCurrency, $quoteCurrency);

                    $item->setDiscountAmount($item->getDiscountAmount() + $discount)
                        ->setBaseDiscountAmount($item->getBaseDiscountAmount() + $baseDiscount);
                    $this->_addAmount(-$discount);
                    $this->_addBaseAmount(-$baseDiscount);
                }
            }
            if ($address->getShippingAmount()) {
                $baseDiscount = $address->getBaseShippingAmount() * $discountPercent / 100;
                $baseDiscount = min($baseDiscount, $address->getBaseShippingAmount() - $address->getBaseShippingDiscountAmount());
                $discount = ($baseCurrency == $quoteCurrency)?$baseDiscount:Mage::helper('directory')->currencyConvert($baseDiscount, $baseCurrency, $quoteCurrency);

                $address->setShippingDiscountAmount($address->getShippingDiscountAmount() + $discount)
                    ->setBaseShippingDiscountAmount($address->getBaseShippingDiscountAmount() + $baseDiscount);
                
                $this->_addAmount(-$discount);
                $this->_addBaseAmount(-$baseDiscount);
            }
            $this->_addCustomDiscountDescription($address);
            return $this;
        }
        
        // Calculate items total
        $baseItemsPrice = 0;
        foreach ($items as $item) {
			$base_item_price = ($showItemPriceInclTax != 1) ? $item->getBasePriceInclTax() : $item->getBasePrice();
            if ($item->getParentItemId()) continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
					$base_child_price = ($showItemPriceInclTax != 1) ? $child->getBasePriceInclTax() : $child->getBasePrice();
                    $baseItemsPrice += $item->getQty() * ($child->getQty() * $base_child_price - $child->getBaseDiscountAmount());
                }
            } else {
                $baseItemsPrice += $item->getQty() * $base_item_price - $item->getBaseDiscountAmount();
            }
        }
        $baseItemsPrice += $address->getBaseShippingAmount() - $address->getBaseShippingDiscountAmount();
        if ($baseItemsPrice < 0.0001) {
            return $this;
        }

        // Calculate custom discount for each item
        $baseDiscountValue = ($baseCurrency == $quoteCurrency)?$discountValue:Mage::helper('webpos')->toBasePrice($discountValue, $quoteCurrency, $baseCurrency);

        $rate = $baseDiscountValue / $baseItemsPrice;
        if ($rate > 1) $rate = 1;
        if(($rate*100) > $maxPercent){
            $rate = $maxPercent/100;
        }
        foreach ($items as $item) {
            $base_item_price = ($showItemPriceInclTax != 1) ? $item->getBasePriceInclTax() : $item->getBasePrice();

            if ($item->getParentItemId()) continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
					$base_child_price = ($showItemPriceInclTax != 1) ? $child->getBasePriceInclTax() : $child->getBasePrice();

                    $discount = $rate * ($child->getQty() * $base_child_price - $child->getBaseDiscountAmount());
                    $baseDiscount = ($baseCurrency == $quoteCurrency)?$discount:Mage::helper('webpos')->toBasePrice($discount, $quoteCurrency, $baseCurrency);

                    $child->setDiscountAmount($child->getDiscountAmount() + $discount)
                        ->setBaseDiscountAmount($child->getBaseDiscountAmount() + $baseDiscount);

                    $this->_addAmount(-$discount);
                    $this->_addBaseAmount(-$baseDiscount);
                }
            } else {
                $baseDiscount = $rate * ($item->getQty() * $base_item_price - $item->getBaseDiscountAmount());
                $discount = ($baseCurrency == $quoteCurrency)?$baseDiscount:Mage::helper('directory')->currencyConvert($baseDiscount, $baseCurrency, $quoteCurrency);

                $item->setDiscountAmount($item->getDiscountAmount() + $discount)
                    ->setBaseDiscountAmount($item->getBaseDiscountAmount() + $baseDiscount);

                $this->_addAmount(-$discount);
                $this->_addBaseAmount(-$baseDiscount);
            }
        }
        if ($address->getShippingAmount()) {
            $discount = $rate * ($address->getShippingAmount() - $address->getShippingDiscountAmount());
            $baseDiscount = ($baseCurrency == $quoteCurrency)?$discount:Mage::helper('webpos')->toBasePrice($discount, $quoteCurrency, $baseCurrency);

            $address->setShippingDiscountAmount($address->getShippingDiscountAmount() + $discount)
                ->setBaseShippingDiscountAmount($address->getBaseShippingDiscountAmount() + $baseDiscount);

            $this->_addAmount(-$discount);
            $this->_addBaseAmount(-$baseDiscount);
        }
        $this->_addCustomDiscountDescription($address);
        return $this;
    }
    
    protected function _addCustomDiscountDescription($address)
    {
        $description = $address->getDiscountDescriptionArray();
        $label = $address->getQuote()->getWebposDiscountDesc();
        if (!$label) {
            $label = Mage::helper('webpos')->__('Custom Discount');
        }
        $description[0] = $label;
        
        $address->setDiscountDescriptionArray($description);
        $this->_calculator->prepareDescription($address);
    }
}
