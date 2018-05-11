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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Stock Movement Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_StockMovement extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const STOCK_MOVEMENT_ID = 'stock_movement_id';

    const PRODUCT_ID = 'product_id';

    const PRODUCT_SKU = 'product_sku';

    const QTY = 'qty';

    const ACTION_CODE = 'action_code';

    const ACTION_ID = 'action_id';

    const REFERENCE_NUMBER = 'reference_number';

    const SOURCE_WAREHOUSE_ID = 'source_warehouse_id';

    const SOURCE_WAREHOUSE_CODE = 'source_warehouse_code';

    const DES_WAREHOUSE_ID = 'des_warehouse_id';

    const DES_WAREHOUSE_CODE = 'des_warehouse_code';

    const EXTERNAL_LOCATION = 'external_location';
    
    const STOCK_TRANSFER_ID = 'stock_transfer_id';

    const CREATED_AT = 'created_at';

    /**#@-*/

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/stockMovement');
    }
    
    /**
     * Stock movement id
     *
     * @return int|null
     */
    public function getStockMovementId(){
        return $this->_getData(self::STOCK_MOVEMENT_ID);
    }

    /**
     * Set warehouse id
     *
     * @param int|null $stockMovementId
     * @return $this
     */
    public function setStockMovementId($stockMovementId){
        return $this->setData(self::STOCK_MOVEMENT_ID, $stockMovementId);
    }

    /**
     * Product id
     *
     * @return int
     */
    public function getProductId(){
        return $this->_getData(self::PRODUCT_ID);
    }

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId){
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Product SKU
     *
     * @return string|null
     */
    public function getProductSku(){
        return $this->_getData(self::PRODUCT_SKU);
    }

    /**
     * Set product sku
     *
     * @param string|null $productSku
     * @return $this
     */
    public function setProductSku($productSku){
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * Product qty
     *
     * @return int
     */
    public function getQty(){
        return $this->_getData(self::QTY);
    }

    /**
     * Set product qty
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty){
        return $this->setData(self::QTY, $qty);
    }

    /**
     * Action code
     *
     * @return string
     */
    public function getActionCode(){
        return $this->_getData(self::ACTION_CODE);
    }

    /**
     * Set action code
     *
     * @param string $actionCode
     * @return $this
     */
    public function setActionCode($actionCode){
        return $this->setData(self::ACTION_CODE, $actionCode);
    }

    /**
     * Action ID
     *
     * @return int
     */
    public function getActionId(){
        return $this->_getData(self::ACTION_ID);
    }

    /**
     * Set action id
     *
     * @param int $actionId
     * @return $this
     */
    public function setActionId($actionId){
        return $this->setData(self::ACTION_ID, $actionId);
    }

    /**
     * Reference number
     *
     * @return string|null
     */
    public function getReferenceNumber(){
        return $this->_getData(self::REFERENCE_NUMBER);
    }

    /**
     * Set reference number
     *
     * @param string|null $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber){
        return $this->setData(self::REFERENCE_NUMBER, $referenceNumber);
    }

    /**
     * Source warehouse Id
     *
     * @return int|null
     */
    public function getSourceWarehouseId(){
        return $this->_getData(self::SOURCE_WAREHOUSE_ID);
    }

    /**
     * Set source warehouse id
     *
     * @param int|null $sourceWarehouseId
     * @return $this
     */
    public function setSourceWarehouseId($sourceWarehouseId){
        return $this->setData(self::SOURCE_WAREHOUSE_ID, $sourceWarehouseId);
    }

    /**
     * Source warehouse code
     *
     * @return string|null
     */
    public function getSourceWarehouseCode(){
        return $this->_getData(self::SOURCE_WAREHOUSE_CODE);
    }

    /**
     * Set source warehouse code
     *
     * @param string|null $sourceWarehouseCode
     * @return $this
     */
    public function setSourceWarehouseCode($sourceWarehouseCode){
        return $this->setData(self::SOURCE_WAREHOUSE_CODE, $sourceWarehouseCode);
    }

    /**
     * Destination warehouse id
     *
     * @return int|null
     */
    public function getDesWarehouseId(){
        return $this->_getData(self::DES_WAREHOUSE_ID);
    }

    /**
     * Set destination warehouse id
     *
     * @param int|null $desWarehouseId
     * @return $this
     */
    public function setDesWarehouseId($desWarehouseId){
        return $this->setData(self::DES_WAREHOUSE_ID, $desWarehouseId);
    }

    /**
     * Destination warehouse code
     *
     * @return string|null
     */
    public function getDesWarehouseCode(){
        return $this->_getData(self::DES_WAREHOUSE_CODE);
    }

    /**
     * Set sestination warehouse code
     *
     * @param string|null $desWarehouseCode
     * @return $this
     */
    public function setDesWarehouseCode($desWarehouseCode){
        return $this->setData(self::DES_WAREHOUSE_CODE, $desWarehouseCode);
    }

    /**
     * External location
     *
     * @return string|null
     */
    public function getExternalLocation(){
        return $this->_getData(self::EXTERNAL_LOCATION);
    }

    /**
     * Set external location
     *
     * @param string|null $externalLocation
     * @return $this
     */
    public function setExternalLocation($externalLocation){
        return $this->setData(self::EXTERNAL_LOCATION, $externalLocation);
    }

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}