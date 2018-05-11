<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Product\Type;
/**
 * Catalog product model
 *
 * @method Product setHasError(bool $value)
 * @method \Magento\Catalog\Model\ResourceModel\Product getResource()
 * @method null|bool getHasError()
 * @method Product setAssociatedProductIds(array $productIds)
 * @method array getAssociatedProductIds()
 * @method Product setNewVariationsAttributeSetId(int $value)
 * @method int getNewVariationsAttributeSetId()
 * @method int getPriceType()
 * @method \Magento\Catalog\Model\ResourceModel\Product\Collection getCollection()
 * @method string getUrlKey()
 * @method Product setUrlKey(string $urlKey)
 * @method Product setRequestPath(string $requestPath)
 * @method Product setWebsiteIds(array $ids)
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Configurable extends \Magento\Catalog\Model\Product
{


    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        if ($this->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
            return;
        $store = $this->_storeManager->getStore();
        $currentProduct = $this->getProduct();

        $regularPrice = $currentProduct->getPriceInfo()->getPrice('regular_price');
        $finalPrice = $currentProduct->getPriceInfo()->getPrice('final_price');

        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\ConfigurableProduct\Helper\Data'
        );
        $options = $helper->getOptions($currentProduct, $this->getAllowProducts());
        $attributesData = \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magento\ConfigurableProduct\Model\ConfigurableAttributeData'
        )->getAttributesData($currentProduct, $options);

        $config = [
            'attributes' => $attributesData['attributes'],
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $this->getOptionPrices(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->registerJsPrice($regularPrice->getAmount()->getValue()),
                ],
                'basePrice' => [
                    'amount' => $this->registerJsPrice(
                        $finalPrice->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->registerJsPrice($finalPrice->getAmount()->getValue()),
                ],
            ],
            'productId' => $currentProduct->getId(),
            'chooseText' => __('Choose an Option...'),
            'images' => isset($options['images']) ? $options['images'] : [],
            'index' => isset($options['index']) ? $options['index'] : [],
        ];

        if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->getAdditionalConfig());

        $str =  \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magento\Framework\Json\EncoderInterface'
        )->encode($config);
        return $str;
    }

    /**
     * Returns additional values for js config, con be overridden by descendants
     *
     * @return array
     */
    public function getAdditionalConfig()
    {
        return [];
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    public function registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

    /**
     * @return array
     */
    public function getOptionPrices()
    {
        $prices = [];
        foreach ($this->getAllowProducts() as $product) {
            $priceInfo = $product->getPriceInfo();
            $prices[$product->getId()] =
                [
                    'oldPrice' => [
                        'amount' => $this->registerJsPrice(
                            $priceInfo->getPrice('regular_price')->getAmount()->getValue()
                        ),
                    ],
                    'basePrice' => [
//                        'amount' => $this->registerJsPrice(
//                            $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount()
//                        ),
                        'amount' => $product->getFinalPrice(),
                    ],
                    'finalPrice' => [
                        'amount' => $this->registerJsPrice(
                            $priceInfo->getPrice('final_price')->getAmount()->getValue()
                        ),
                    ]
                ];
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
        if (!$this->hasAllowProducts()) {
            $currentProduct = $this->getProduct();
            $products = [];
            $skipSaleableCheck = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Catalog\Helper\Product'
            )->getSkipSaleableCheck();
            $allProducts = $currentProduct->getTypeInstance()->getUsedProducts($currentProduct, null);
            foreach ($allProducts as $product) {
                if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
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

}
