<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface ReorderItemInfoInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface InfoBuyInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_ID = 'id';
    const KEY_CHILD_ID = 'child_id';
    const KEY_QTY = 'qty';
    const KEY_SUPER_ATTRIBUTE = 'super_attribute';
    const KEY_SUPER_GROUP = 'super_group';
    const KEY_BUNDLE_OPTION = 'bundle_option';
    const KEY_BUNDLE_OPTION_QTY = 'bundle_option_qty';
    const KEY_CUSTOM_OPTION = 'options';
    const KEY_UNIT_PRICE = 'unit_price';
    const KEY_BASE_UNIT_PRICE = 'base_unit_price';
    const KEY_ORIGINAL_PRICE = 'original_price';
    const KEY_BASE_ORIGINAL_PRICE = 'base_original_price';
    const KEY_OPTIONS_LABEL = 'options_label';
    const KEY_CUSTOM_SALES = 'custom_sales_info';
    const KEY_HAS_CUSTOM_PRICE = 'has_custom_price';
    /**#@-*/
    
    /**
     * Returns the product id.
     *
     * @return string id. Otherwise, null.
     */
    public function getId();

    /**
     * Sets the product id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);
    
    /**
     * Returns the product child id.
     *
     * @return string id. Otherwise, null.
     */
    public function getChildId();

    /**
     * Sets the product child id.
     *
     * @param int $childId
     * @return $this
     */
    public function setChildId($childId);
    
    /**
     * Returns the item quantity.
     *
     * @return float Qty. Otherwise, null.
     */
    public function getQty();
    
    /**
     * Sets the item quantity.
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);
    
    /**
     * Sets the item supper attribute.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $super_attribute
     * @return $this
     */
    public function setSuperAttribute($super_attribute);
    
    /**
     * Returns the item supper attribute.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] super attribute. Otherwise, null.
     */
    public function getSuperAttribute();
    
    /**
     * Sets the item supper group.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $super_group
     * @return $this
     */
    public function setSuperGroup($super_group);
    
    /**
     * Returns the item supper group.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] super group. Otherwise, null.
     */
    public function getSuperGroup();
    
    /**
     * Sets the item custom options.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $options
     * @return $this
     */
    public function setOptions($options);
    
    /**
     * Returns the item custom options.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] options. Otherwise, null.
     */
    public function getOptions();
    
    /**
     * Sets the item bundle option.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $bundle_option
     * @return $this
     */
    public function setBundleOption($bundle_option);
    
    /**
     * Returns the item bundle option.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] bundle option. Otherwise, null.
     */
    public function getBundleOption();
    
    /**
     * Sets the item bundle option qty.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $bundle_option_qty
     * @return $this
     */
    public function setBundleOptionQty($bundle_option_qty);
    
    /**
     * Returns the item bundle option qty.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] bundle option qty. Otherwise, null.
     */
    public function getBundleOptionQty();
    
    /**
     * Returns the item unit price.
     *
     * @return string.
     */
    public function getUnitPrice();

    /**
     * Sets the item unit price.
     *
     * @param string
     * @return $this
     */
    public function setUnitPrice($unitPrice);
    
    /**
     * Returns the item base unit price.
     *
     * @return string.
     */
    public function getBaseUnitPrice();

    /**
     * Sets the item base unit price.
     *
     * @param string
     * @return $this
     */
    public function setBaseUnitPrice($baseUnitPrice);
    
    /**
     * Returns the item original price.
     *
     * @return string.
     */
    public function getOriginalPrice();

    /**
     * Sets the item original price.
     *
     * @param string
     * @return $this
     */
    public function setOriginalPrice($originalPrice);
    
    /**
     * Returns the item base original price.
     *
     * @return string.
     */
    public function getBaseOriginalPrice();

    /**
     * Sets the item base original price.
     *
     * @param string
     * @return $this
     */
    public function setBaseOriginalPrice($baseOriginalPrice);
    
    /**
     * Returns the item options label.
     *
     * @return string.
     */
    public function getOptionsLabel();

    /**
     * Sets the item options label.
     *
     * @param string
     * @return $this
     */
    public function setOptionsLabel($optionsLabel);
    
    /**
     * Returns the custom sales information
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CustomSalesInfoInterface[]
     */
    public function getCustomSalesInfo();
    
    /**
     * Sets the custom sales information.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CustomSalesInfoInterface[]
     * @return $this
     */
    public function setCustomSalesInfo($customSalesInfo);

    /**
     * @return string
     */
    public function getHasCustomPrice();

    /**
     * @param string
     * @return $this
     */
    public function setHasCustomPrice($hasCustomPrice);
}
