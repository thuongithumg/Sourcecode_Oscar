<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Checkout\Data;


class ItemsInfoBuy extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\CustomSalesInfoInterface
{
    /**
     * Returns the product id.
     *
     * @return string. Otherwise, null.
     */
    public function getProductId() {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Sets the product id.
     *
     * @param string $productId
     * @return $this
     */
    public function setId($productId) {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Returns the product name.
     *
     * @return string. Otherwise, null.
     */
    public function getProductName() {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * Sets the product name.
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName) {
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * get unit price
     *
     * @return string. Otherwise, null.
     */
    public function getUnitPrice() {
        return $this->getData(self::UNIT_PRICE);
    }

    /**
     * set unit price
     *
     * @param string $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice) {
        return $this->setData(self::UNIT_PRICE, $unitPrice);
    }

    /**
     * get tax class id
     *
     * @return string. Otherwise, null.
     */
    public function getTaxClassId() {
        return $this->getData(self::TAX_CLASS_ID);
    }

    /**
     * get tax class id
     *
     * @param string $taxClassId
     * @return $this
     */
    public function setTaxClassId($taxClassId) {
        return $this->setData(self::TAX_CLASS_ID, $taxClassId);
    }

    /**
     * get is virtual
     *
     * @return string. Otherwise, null.
     */
    public function getIsVirtual() {
        return $this->getData(self::IS_VIRTUAL);
    }

    /**
     * set is virtual
     *
     * @param string $isVirtual
     * @return $this
     */
    public function setIsVirtual($isVirtual) {
        return $this->setData(self::IS_VIRTUAL, $isVirtual);
    }

    /**
     * get qty.
     *
     * @return string. Otherwise, null.
     */
    public function getQty() {
        return $this->getData(self::QTY);
    }

    /**
     * set qty
     *
     * @param string $qty
     * @return $this
     */
    public function setQty($qty) {
        return $this->setData(self::QTY, $qty);
    }
}