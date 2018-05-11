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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Sales_Order_Create_Items_Grid
 */
class Magestore_Giftvoucher_Block_Adminhtml_Sales_Order_Create_Items_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid {

    /**
     * Returns the items
     *
     * @return array
     */
    public function getItems() {
        $items = $this->getParentBlock()->getItems();
        $oldSuperMode = $this->getQuote()->getIsSuperMode();
        $this->getQuote()->setIsSuperMode(false);
        foreach ($items as $item) {
            // To dispatch inventory event sales_quote_item_qty_set_after, set item qty
            $item->setQty($item->getQty());
            $stockItem = $item->getProduct()->getStockItem();
            if ($stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                // This check has been performed properly in Inventory observer, so it has no sense
                /*
                  $check = $stockItem->checkQuoteItemQty($item->getQty(), $item->getQty(), $item->getQty());
                  $item->setMessage($check->getMessage());
                  $item->setHasError($check->getHasError());
                 */
                if ($item->getProduct()->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED) {
                    $item->setMessage(Mage::helper('adminhtml')->__('This product is currently disabled.'));
                    $item->setHasError(true);
                }
            }

            if ($item->getProductType() == 'giftvoucher') {

                $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());

                $rowTotal = $item->getRowTotal();
                $qty = $item->getQty();
                $store = $item->getStore();
                $price = $store->roundPrice($rowTotal) / $qty;

                $baseCurrencyCode = $store->getBaseCurrencyCode();
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
                        if ($option->getCode() == 'amount')
                            $option->setValue($amount);
                    }
                    $item->setOptions($options)->save();
                }
            }
        }
        $this->getQuote()->setIsSuperMode($oldSuperMode);
        return $items;
    }

}
