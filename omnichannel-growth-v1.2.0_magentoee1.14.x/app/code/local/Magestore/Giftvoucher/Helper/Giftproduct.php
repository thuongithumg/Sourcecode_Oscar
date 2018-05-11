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
 * Giftvoucher product helper
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Helper_Giftproduct extends Mage_Core_Helper_Data
{

    /**
     * Get the price information of Gift Card product 
     *
     * @param Magestore_Giftvoucher_Model_Product $product
     * @return array
     */
    public function getGiftValue($product)
    {
        $giftType = $product->getGiftType();
        switch ($giftType) {
            case Magestore_Giftvoucher_Model_Gifttype::GIFT_TYPE_FIX:
                return array(
                    'type' => 'static', 
                    'gift_price' => $this->getGiftPriceByStatic($product), 
                    'value' => $product->getGiftValue()
                );

            case Magestore_Giftvoucher_Model_Gifttype::GIFT_TYPE_RANGE:
                $data = array('type' => 'range', 'from' => $product->getGiftFrom(), 'to' => $product->getGiftTo());
                $priceType = $product->getGiftPriceType();
                if ($priceType == Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT) {
                    $data['gift_price_type'] = 'default';
                } else {
                    $data['gift_price_type'] = 'percent';
                    $data['gift_price_options'] = $product->getGiftPrice();
                }
                return $data;

            case Magestore_Giftvoucher_Model_Gifttype::GIFT_TYPE_DROPDOWN:
                $options = explode(',', $product->getGiftDropdown());
                $giftPrices = explode(',', $product->getGiftPrice());

                foreach ($options as $key => $option) {
                    if (!is_numeric($option) || $option <= 0) {
                        unset($options[$key]);
                    }
                }

                $data = array('type' => 'dropdown', 'options' => $options);
                $priceType = $product->getGiftPriceType();
                if ($priceType == Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT) {
                    $data['prices'] = $options;
                } else if ($priceType == Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_FIX) {
                    $optionsPrice = explode(',', $product->getGiftPrice());
                    $data['prices'] = $optionsPrice;
                } else {
                    if (count($giftPrices) == count($options)) {
                        for ($i = 0; $i < count($giftPrices); $i++) {
                            $data['prices'][] = $giftPrices[$i] * $options[$i] / 100;
                        }
                    } else {
                        foreach ($options as $value) {
                            $data['prices'][] = $value * $product->getGiftPrice() / 100;
                        }
                    }
                }

                return $data;
            default:
                $giftValue = Mage::helper('giftvoucher')->getInterfaceConfig('amount');
                $options = explode(',', $giftValue);
                return array('type' => 'dropdown', 'options' => $options, 'prices' => $options);
        }
    }

    /**
     * Get the static price of Gift Card product 
     *
     * @param Magestore_Giftvoucher_Model_Product $product
     * @return float
     */
    public function getGiftPriceByStatic($product)
    {
        $giftValue = $product->getGiftValue();
        $giftPrice = $product->getGiftPrice();
        if ($product->getGiftPriceType() == Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT) {
            return $giftValue;
        } else if ($product->getGiftPriceType() == Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_FIX) {
            return $giftPrice;
        } else {
            return $giftValue * $giftPrice / 100;
        }
    }

}
