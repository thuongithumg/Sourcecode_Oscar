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
 * Giftvoucher Cart Item Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Cart_Item extends Mage_Checkout_Block_Cart_Item_Renderer
{

    /**
     * @return array
     */
    public function getProductOptions()
    {
        $options = parent::getProductOptions();
        //Hai.Tran 28/11
        foreach (Mage::helper('giftvoucher')->getGiftVoucherOptions() as $code => $label) {
            if ($option = $this->getItem()->getOptionByCode($code)) {
                if ($code == 'giftcard_template_id') {
                    $valueTemplate = Mage::getModel('giftvoucher/gifttemplate')->load($option->getValue());
                    $options[] = array(
                        'label' => $label,
                        'value' => $this->htmlEscape($valueTemplate->getTemplateName() ? 
                            $valueTemplate->getTemplateName() : $option->getValue()),
                    );
                } else if ($code == 'amount') {
                    $options[] = array(
                        'label' => $label,
                        'value' => Mage::helper('core')->formatPrice($option->getValue()),
                    );
                } else {
                    $options[] = array(
                        'label' => $label,
                        'value' => $this->htmlEscape($option->getValue()),
                    );
                }
            }
        }
        return $options;
    }

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail()
    {
        if (!Mage::helper('giftvoucher')->getInterfaceCheckoutConfig('display_image_item') 
            || $this->getProduct()->getTypeId() != 'giftvoucher') {
            return parent::getProductThumbnail();
        }
        $item = $this->getItem();
        if ($item->getOptionByCode('giftcard_template_image')) {
            $filename = $item->getOptionByCode('giftcard_template_image')->getValue();
        } else {
            $filename = 'default.png';
        }
        if ($item->getOptionByCode('giftcard_use_custom_image') 
            && $item->getOptionByCode('giftcard_use_custom_image')->getValue()) {
            $urlImage = '/tmp/giftvoucher/images/' . $filename;
            $filename = 'custom/' . $filename;
        } else {
            if ($item->getOptionByCode('giftcard_template_id')) {
                $templateId = $item->getOptionByCode('giftcard_template_id')->getValue();
                $designPattern = Mage::getModel('giftvoucher/gifttemplate')->load($templateId)->getDesignPattern();
                $filename = $designPattern . '/' . $filename;
            }
            $urlImage = '/giftvoucher/template/images/' . $filename;
        }
        $imageUrl = Mage::getBaseDir('media') . str_replace("/", DS, $urlImage);

        if (!file_exists($imageUrl)) {
            return parent::getProductThumbnail();
        }
        return $this->helper('giftvoucher')->getProductThumbnail($imageUrl, $filename, substr($urlImage, 1));
    }

    /**
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getItem()
    {

        $item = parent::getItem();
        $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());

        $rowTotal = $item->getRowTotal();
        $qty = $item->getQty();
        $store = $item->getStore();
        $price = $store->roundPrice($rowTotal) / $qty;

        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $quoteCurrencyCode = $item->getQuote()->getQuoteCurrencyCode();
        $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);

        if ($baseCurrencyCode != $quoteCurrencyCode) {
            $quoteCurrency = Mage::getModel('directory/currency')->load($quoteCurrencyCode);
            if ($product->getGiftType() == Magestore_Giftvoucher_Model_Gifttype::GIFT_TYPE_RANGE) {
                $price = $price * $price / $baseCurrency->convert($price, $quoteCurrency);
                $item->setPrice($price);
            }
        }

        $options = $item->getOptions();
        $result = array();
        foreach ($options as $option) {
            $result[$option->getCode()] = $option->getValue();
        }

        if (isset($result['base_gc_value']) && isset($result['base_gc_currency'])) {
            $currency = $store->getCurrentCurrencyCode();
            $currentCurrency = Mage::getModel('directory/currency')->load($currency);
            $amount = $baseCurrency->convert($result['base_gc_value'], $currentCurrency);
            foreach ($options as $option) {
                if ($option->getCode() == 'amount') {
                    $option->setValue($amount);
                }
            }
            $item->setOptions($options)->save();
        }

        return $item;
    }

}
