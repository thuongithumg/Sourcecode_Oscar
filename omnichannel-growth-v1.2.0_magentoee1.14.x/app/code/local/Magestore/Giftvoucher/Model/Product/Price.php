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
 * Giftvoucher Product Price Model
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{

    const PRICE_TYPE_FIXED = 1;
    const PRICE_TYPE_DYNAMIC = 0;

    /**
     * Get Gift Card price information
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getGiftAmount($product = null)
    {
        $giftAmount = Mage::helper('giftvoucher/giftproduct')->getGiftValue($product);
        switch ($giftAmount['type']) {
            case 'range':
                $giftAmount['min_price'] = $giftAmount['from'];
                $giftAmount['max_price'] = $giftAmount['to'];
                $giftAmount['price'] = $giftAmount['from'];
                if ($giftAmount['gift_price_type'] == 'percent') {
                    $giftAmount['price'] = $giftAmount['price'] * $giftAmount['gift_price_options'] / 100;
                    $giftAmount['min_price'] = $giftAmount['from'] * $giftAmount['gift_price_options'] / 100;
                    $giftAmount['max_price'] = $giftAmount['to'] * $giftAmount['gift_price_options'] / 100;
                }

                if ($giftAmount['min_price'] == $giftAmount['max_price']) {
                    $giftAmount['price_type'] = self::PRICE_TYPE_FIXED;
                } else {
                    $giftAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                }
                break;
            case 'dropdown':
                $giftAmount['min_price'] = min($giftAmount['prices']);
                $giftAmount['max_price'] = max($giftAmount['prices']);
                $giftAmount['price'] = $giftAmount['prices'][0];
                if ($giftAmount['min_price'] == $giftAmount['max_price']) {
                    $giftAmount['price_type'] = self::PRICE_TYPE_FIXED;
                } else {
                    $giftAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                }
                break;
            case 'static':
                $giftAmount['price'] = $giftAmount['gift_price'];
                $giftAmount['price_type'] = self::PRICE_TYPE_FIXED;
                break;
            default:
                $giftAmount['min_price'] = 0;
                $giftAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                $giftAmount['price'] = 0;
        }
        return $giftAmount;
    }

    /**
     * Default action to get price of product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getPrice($product)
    {
        $giftAmount = $this->getGiftAmount($product);
        return $giftAmount['price'];
    }

    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param float $finalPrice
     * @return float
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        if ($amount = $product->getCustomOption('price_amount')) {
            $finalPrice = $amount->getValue();
        }
        return parent::_applyOptionsPrice($product, $qty, $finalPrice);
    }

    /**
     * Get product's price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $which
     * @return array
     */
    public function getPrices($product, $which = null)
    {
        return $this->getPricesDependingOnTax($product, $which);
    }

    /**
     * Get price depending on Tax
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $which
     * @param bool $includeTax
     * @return array
     */
    public function getPricesDependingOnTax($product, $which = null, $includeTax = null)
    {
        $giftAmount = $this->getGiftAmount($product);
        if (isset($giftAmount['min_price']) && isset($giftAmount['max_price'])) {
            $minimalPrice = Mage::helper('tax')->getPrice($product, $giftAmount['min_price'], $includeTax);
            $maximalPrice = Mage::helper('tax')->getPrice($product, $giftAmount['max_price'], $includeTax);
        } else {
            $minimalPrice = $maximalPrice = Mage::helper('tax')->getPrice($product, $giftAmount['price'], $includeTax);
        }

        if ($which == 'max') {
            return $maximalPrice;
        } elseif ($which == 'min') {
            return $minimalPrice;
        }
        return array($minimalPrice, $maximalPrice);
    }

    /**
     * Get min price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getMinimalPrice($product)
    {
        return $this->getPrices($product, 'min');
    }

    /**
     * Get max price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getMaximalPrice($product)
    {
        return $this->getPrices($product, 'max');
    }

    /**
     * Retrieve product final price
     *
     * @param null $qty
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getFinalPrice($qty = null, $product)
    {
        $finalPrice = $this->getPrice($product);
        $product->setFinalPrice($finalPrice);

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);

        return $finalPrice;
    }

}
