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
class Magestore_Webpos_Model_Catalog_Product_Type_Configurable extends Mage_Catalog_Model_Product
{

    /**
     * @param null $product
     * @return mixed
     */
    public function getUsedProductCollection($product = null)
    {
        $collection = Mage::getResourceModel('catalog/product_type_configurable_product_collection')
            ->setFlag('require_stock_items', true)
            ->setFlag('product_children', true)
            ->setProductFilter($this->getProduct($product));
        if (!is_null($this->getStoreFilter($product))) {
            $collection->addStoreFilter($this->getStoreFilter($product));
        }

        return $collection;
    }

    /**
     * @return mixed
     */
    public function getFirstPriceConfig()
    {
        foreach ($this->getAllowProducts() as $product) {
            if ($product->getFinalPrice()) {
                return $product->getFinalPrice();
            }
        }
    }
    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        if ($this->getData('type_id') != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE)
            return;
        $config = array();
        return  Zend_Json::encode($config);

        $store = Mage::app()->getStore();

        $currentProduct = $this->getProduct();

        /* @var Mage_Catalog_Helper_Product_Configuration $helper*/
        $helper = Mage::helper('catalog/product_configuration');
        $options = $helper->getOptions($currentProduct, $this->getAllowProducts());


        $config = array(
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $this->getOptionPrices(),
            'prices' => array(
                'oldPrice' => array(
                    'amount' => $this->registerJsPrice($currentProduct->getPrice()),
                ),
                'basePrice' => array(
                    'amount' => $this->registerJsPrice(
                        $currentProduct->getBasePrice()
                    ),
                ),
                'finalPrice' => array(
                    'amount' => $this->registerJsPrice($currentProduct->getFinalPrice()),
                ),
            ),
            'productId' => $currentProduct->getId(),
            'chooseText' => Mage::helper('webpos')->__('Choose an Option...'),
//            'images' => isset($options['images']) ? $options['images'] : [],
//            'index' => isset($options['index']) ? $options['index'] : [],
        );


        if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->getAdditionalConfig());

        $str = Zend_Json::encode($config);
        return $str;
    }

    /**
     * Returns additional values for js config, con be overridden by descendants
     *
     * @return array
     */
    public function getAdditionalConfig()
    {
        return array();
    }


    /**
     * @param $price
     * @return mixed
     */
    public function registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

    /**
     * @return array
     */
    public function getOptionPrices() {


        $pricesByAttributeValues = array();
        $product = $this->getProduct();
        $attributes = $product->getTypeInstance()->getConfigurableAttributes($product);
        $basePrice = $product->getFinalPrice();

        foreach ($attributes as $attribute){
            $prices = $attribute->getPrices();
            foreach ($prices as $price){
                if ($price['is_percent']){ //if the price is specified in percents
                    $pricesByAttributeValues[$price['value_index']] = (float)$price['pricing_value'] * $basePrice / 100;
                }
                else { //if the price is absolute value
                    $pricesByAttributeValues[$price['value_index']] = (float)$price['pricing_value'];
                }
            }
        }


        $simple = $product->getTypeInstance()->getUsedProducts();
        $prices = array();
        foreach ($simple as $sProduct){
            $totalPrice = $basePrice;


            foreach ($attributes as $attribute){

                $value = $sProduct->getData($attribute->getProductAttribute()->getAttributeCode());
                if (isset($pricesByAttributeValues[$value])){
                    $totalPrice += $pricesByAttributeValues[$value];
                }
            }
            $prices[$sProduct->getId()] =
                array(
                    'oldPrice' => array(
                        'amount' => $this->registerJsPrice(
                            $totalPrice
                        ),
                    ),
                    'basePrice' => array(
//                        'amount' => $this->registerJsPrice(
//                            $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount()
//                        ),
                        'amount' => $totalPrice,
                    ),
                    'finalPrice' => array(
                        'amount' => $this->registerJsPrice(
                            $totalPrice
                        ),
                    )
                );
        }
        return $prices;


    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        $products = array();
        $currentProduct = $this->getProduct();
        $allProducts = $currentProduct->getTypeInstance()->getUsedProducts($currentProduct, null);
        foreach ($allProducts as $childProduct) {
            if ($childProduct->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED
//                || $skipSaleableCheck
//                || (!$childProduct->getStockItem()->getIsInStock()
//                    && Mage::helper('cataloginventory')->isShowOutOfStock())
                && ($childProduct->getStockItem()->getQty() > 0 ||
                    ($childProduct->getIsSalable() && $childProduct->getStockItem()->getBackorders())
                )
            ) {
                $products[] = $childProduct;
            }
        }
        return $products;
    }


}
