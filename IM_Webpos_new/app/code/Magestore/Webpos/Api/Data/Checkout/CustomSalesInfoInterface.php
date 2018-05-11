<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface CustomSalesInfoInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface CustomSalesInfoInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const PRODUCT_ID = 'product_id';
    const PRODUCT_NAME = 'product_name';
    const UNIT_PRICE = 'unit_price';
    const TAX_CLASS_ID = 'tax_class_id';
    const IS_VIRTUAL = 'is_virtual';
    const QTY = 'qty';
    /**#@-*/
    
    /**
     * Returns the product id.
     *
     * @return string. Otherwise, null.
     */
    public function getProductId();

    /**
     * Sets the product id.
     *
     * @param string $productId
     * @return $this
     */
    public function setId($productId);

    /**
     * Returns the product name.
     *
     * @return string. Otherwise, null.
     */
    public function getProductName();

    /**
     * Sets the product name.
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName);

    /**
     * get unit price
     *
     * @return string. Otherwise, null.
     */
    public function getUnitPrice();

    /**
     * set unit price
     *
     * @param string $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice);

    /**
     * get tax class id
     *
     * @return string. Otherwise, null.
     */
    public function getTaxClassId();

    /**
     * get tax class id
     *
     * @param string $taxClassId
     * @return $this
     */
    public function setTaxClassId($taxClassId);

    /**
     * get is virtual
     *
     * @return string. Otherwise, null.
     */
    public function getIsVirtual();

    /**
     * set is virtual
     *
     * @param string $isVirtual
     * @return $this
     */
    public function setIsVirtual($isVirtual);

    /**
     * get qty.
     *
     * @return string. Otherwise, null.
     */
    public function getQty();

    /**
     * set qty
     *
     * @param string $qty
     * @return $this
     */
    public function setQty($qty);
    

}
