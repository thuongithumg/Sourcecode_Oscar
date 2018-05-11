<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Product\Type;

use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;
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

class Bundle extends \Magento\Catalog\Model\Product
{

    /**
     * @var array
     */
    protected $options;

    /** @var array  */
    protected $selectedOptions = [];

    /**
     * Get formed data from option
     *
     * @param Option $option
     * @param Product $product
     * @param int $position
     * @return array
     */
    public function getOptionItemData(Option $option, Product $product, $position)
    {
        return [
            'selections' => $this->getSelections($option, $product),
            'title' => $option->getTitle(),
            'isMulti' => in_array($option->getType(), ['multi', 'checkbox']),
            'position' => $position
        ];
    }

    /**
     * Get formed data from selections of option
     *
     * @param Option $option
     * @param Product $product
     * @return array
     */
    public function getSelections(Option $option, Product $product)
    {
        $selections = [];
        $selectionCount = count($option->getSelections());
        foreach ($option->getSelections() as $selectionItem) {
            /* @var $selectionItem Product */
            $selectionId = $selectionItem->getSelectionId();
            $selections[$selectionId] = $this->getSelectionItemData($product, $selectionItem);

            if (($selectionItem->getIsDefault() || $selectionCount == 1 && $option->getRequired())
                && $selectionItem->isSalable()
            ) {
                $this->selectedOptions[$option->getId()][] = $selectionId;
            }
        }
        return $selections;
    }

    /**
     * Get formed config data from calculated options data
     *
     * @param Product $product
     * @param array $options
     * @return array
     */
    public function getConfigData(Product $product, array $options)
    {
        $isFixedPrice = $this->getProduct()->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED;

        $productAmount = $product
            ->getPriceInfo()
            ->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)
            ->getPriceWithoutOption();

        $baseProductAmount = $product
            ->getPriceInfo()
            ->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)
            ->getAmount();

        $config = [
            'options' => $options,
            'selected' => $this->selectedOptions,
            'bundleId' => $product->getId(),
            'priceFormat' => \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\Framework\Locale\FormatInterface'
            )->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $isFixedPrice ? $baseProductAmount->getValue() : 0
                ],
                'basePrice' => [
                    'amount' => $isFixedPrice ? $productAmount->getBaseAmount() : 0
                ],
                'finalPrice' => [
                    'amount' => $isFixedPrice ? $productAmount->getValue() : 0
                ]
            ],
            'priceType' => $product->getPriceType(),
            'isFixedPrice' => $isFixedPrice,
        ];
        return $config;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (!$this->options) {
            $product = $this->getProduct();
            $typeInstance = $product->getTypeInstance();
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $typeInstance->getOptionsCollection($product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($product),
                $product
            );

            $this->options = $optionCollection->appendSelections(
                $selectionCollection,
                false,
                \Magento\Framework\App\ObjectManager::getInstance()->create(
                    'Magento\Catalog\Helper\Product'
                )->getSkipSaleableCheck()
            );
        }

        return $this->options;
    }

}
