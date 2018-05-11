<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Cart\Data;

/**
 * Class QuoteItem
 * @package Magestore\Webpos\Model\Cart\Data
 */
class QuoteItem extends \Magento\Quote\Model\Quote\Item implements \Magestore\Webpos\Api\Data\Cart\QuoteItemInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setUnitPrice($unitPrice)
    {
        return $this->setData(self::UNIT_PRICE, $unitPrice);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getUnitPrice()
    {
        return $this->getData(self::UNIT_PRICE);
    }

    /**
     * Sets discount amount
     *
     * @param string $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }

    /**
     * Gets discount amount
     *
     * @return string.
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBaseDiscountAmount($baseDiscountAmount)
    {
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, $baseDiscountAmount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBaseDiscountAmount()
    {
        return $this->getData(self::BASE_DISCOUNT_AMOUNT);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setTaxPercent($taxPercect)
    {
        return $this->setData(self::TAX_PERCENT, $taxPercect);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTaxPercent()
    {
        return $this->getData(self::TAX_PERCENT);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setTaxAmount($taxAmount)
    {
        return $this->setData(self::TAX_AMOUNT, $taxAmount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTaxAmount()
    {
        return $this->getData(self::TAX_AMOUNT);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBaseTaxAmount($baseTaxAmount)
    {
        return $this->setData(self::BASE_TAX_AMOUNT, $baseTaxAmount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBaseTaxAmount()
    {
        return $this->getData(self::BASE_TAX_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setIsVirtual($isVirtual)
    {
        return $this->setData(self::UNIT_PRICE, $isVirtual);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getIsVirtual()
    {
        return $this->getData(self::IS_VIRTUAL);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setHasError($hasError)
    {
        return $this->setData(self::HAS_ERROR, $hasError);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getHasError()
    {
        return $this->getData(self::HAS_ERROR);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setQtyIncrement($qtyIncrement)
    {
        return $this->setData(self::QTY_INCREMENT, $qtyIncrement);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getQtyIncrement()
    {
        return $this->getData(self::QTY_INCREMENT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setMaximumQty($maximumQty)
    {
        return $this->setData(self::MAXIMUM_QTY, $maximumQty);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getMaximumQty()
    {
        return $this->getData(self::MAXIMUM_QTY);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setMinimumQty($minimumQty)
    {
        return $this->setData(self::MINIMUM_QTY, $minimumQty);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getMinimumQty()
    {
        return $this->getData(self::MINIMUM_QTY);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setSavedOnlineItem($savedOnlineItem)
    {
        return $this->setData(self::SAVED_ONLINE_ITEM, $savedOnlineItem);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSavedOnlineItem()
    {
        return $this->getData(self::SAVED_ONLINE_ITEM);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setImageUrl($imageUrl)
    {
        return $this->setData(self::IMAGE_URL, $imageUrl);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getImageUrl()
    {
        return $this->getData(self::IMAGE_URL);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setOfflineItemId($offlineItemId)
    {
        return $this->setData(self::OFFLINE_ITEM_ID, $offlineItemId);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getOfflineItemId()
    {
        return $this->getData(self::OFFLINE_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setPriceInclTax($priceInclTax)
    {
        return $this->setData(self::PRICE_INCL_TAX, $priceInclTax);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getPriceInclTax()
    {
        return $this->getData(self::PRICE_INCL_TAX);
    }
}