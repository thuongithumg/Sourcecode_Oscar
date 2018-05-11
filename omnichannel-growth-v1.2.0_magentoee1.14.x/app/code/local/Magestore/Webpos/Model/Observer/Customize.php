<?php
/**
 * Created by Wazza Rooney on 7/27/17 1:54 PM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 7/27/17 1:54 PM
 */

class Magestore_Webpos_Model_Observer_Customize
{
    public function getFinalPrice(Varien_Event_Observer $observer)
    {
        if (strpos(Mage::app()->getRequest()->getRequestUri(), "webpos") !== false)
        {
            /** @var Mage_Catalog_Model_Product $product */
            $product = $observer->getProduct();
            $qty     = $observer->getQty();

            if ($product->getTypeId() === Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
                $customOptions = $product->getCustomOptions();
                if (empty($customOptions)) return $this;
                if (empty($customOptions['simple_product'])) return $this;

                $simpleProduct = $customOptions['simple_product']->getProduct();

                $groupPrices = $simpleProduct->getData('group_price');

                if (is_null($groupPrices)) {
                    $attribute = $simpleProduct->getResource()->getAttribute('group_price');
                    if ($attribute) {
                        $attribute->getBackend()->afterLoad($simpleProduct);
                        $groupPrices = $simpleProduct->getData('group_price');
                    }
                }

                if (is_null($groupPrices) || !is_array($groupPrices)) {
                    return $this;
                }

                $customerGroup = $this->_getCustomerGroupId($simpleProduct);

                foreach ($groupPrices as $groupPrice) {
                    if ($groupPrice['cust_group'] == $customerGroup && $groupPrice['website_price'] < $product->getPrice()) {
                        $product->setFinalPrice($groupPrice['website_price']);
                        break;
                    }
                }

                return $this;
            }
        }
    }

    protected function _getCustomerGroupId($product)
    {
        if ($product->getCustomerGroupId()) {
            return $product->getCustomerGroupId();
        }

        if ($customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId() ) {
            return $customerGroupId;
        }

        $quoteId = Mage::getModel('checkout/session')->getWebposQuoteId();
        if ($quoteId) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            return $quote->getCustomerGroupId();
        }

        return '';
    }
}