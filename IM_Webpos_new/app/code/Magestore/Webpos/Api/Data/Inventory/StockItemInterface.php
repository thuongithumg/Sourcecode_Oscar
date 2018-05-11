<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Inventory;

/**
 * @api
 */
interface StockItemInterface
{ 
    
    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);
    
    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);    

    /**
     * @return int
     */
    public function getStockId();

    /**
     * @param int $stockId
     * @return $this
     */
    public function setStockId($stockId);    

    /**
     * Product SKU
     *
     * @return string|null
     */
    public function getSku();
    
    /**
     * 
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);
    
    /**
     * Product Name
     *
     * @return string|null
     */
    public function getName();    
    
    /**
     * 
     * @param string $name
     * @return $this
     */
    public function setName($name);
   

    /**
     * @return float
     */
    public function getQty();

    /**
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Retrieve Stock Availability
     *
     * @return bool|int
     */
    public function getIsInStock();

    /**
     * Set Stock Availability
     *
     * @param bool|int $isInStock
     * @return $this
     */
    public function setIsInStock($isInStock);
    
    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigManageStock();

    /**
     * @param bool $useConfigManageStock
     * @return $this
     */
    public function setUseConfigManageStock($useConfigManageStock);

    /**
     * Retrieve can Manage Stock
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getManageStock();

    /**
     * @param bool $manageStock
     * @return $this
     */
    public function setManageStock($manageStock);
    
    /**
     * Retrieve can backorder
     *
     * @return int
     */    
    public function getBackorders();
    
    /**
     * @param int $backorders
     * @return $this
     */
    public function setBackorders($backorders);  
    
    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigBackorders();

    /**
     * @param bool $useConfigBackorders
     * @return $this
     */
    public function setUseConfigBackorders($useConfigBackorders);   
    
    
    /**
     *
     * @return int|float
     */    
    public function getMinSaleQty();
    
    /**
     * @param int|float $minSaleQty
     * @return $this
     */
    public function setMinSaleQty($minSaleQty);      
    
    /**
     * @return bool
     */
    public function getUseConfigMinSaleQty();

    /**
     * @param bool $useConfigMinSaleQty
     * @return $this
     */
    public function setUseConfigMinSaleQty($useConfigMinSaleQty);       
    
    /**
     *
     * @return int|float
     */    
    public function getMaxSaleQty();
    
    /**
     * @param int|float $maxSaleQty
     * @return $this
     */
    public function setMaxSaleQty($maxSaleQty);         
    
    /**
     * @return bool
     */
    public function getUseConfigMaxSaleQty();

    /**
     * @param bool $useConfigMaxSaleQty
     * @return $this
     */
    public function setUseConfigMaxSaleQty($useConfigMaxSaleQty);  
    
    /**
     * @return string
     */
    public function getUpdatedTime();

    /**
     * @param string $updatedTime
     * @return $this
     */
    public function setUpdatedTime($updatedTime);

    /**
     * @return string
     */
    public function getQtyIncrements();

    /**
     * @param string $qtyIncrements
     * @return $this
     */
    public function setQtyIncrements($qtyIncrements);
    
    /**
     * @return bool
     */
    public function getIsQtyDecimal();

    /**
     * @param bool $isQtyDecimal
     * @return $this
     */
    public function setIsQtyDecimal($isQtyDecimal);    
    
}