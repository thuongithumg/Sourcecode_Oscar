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
 * Stock Transfer Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_StockMovement_StockTransfer extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const STOCK_TRANSFER_ID = 'stock_transfer_id';

    const TRANSFER_CODE = 'transfer_code';

    const QTY = 'qty';

    const TOTAL_SKU = 'total_sku';

    const ACTION_CODE = 'action_code';

    const ACTION_ID = 'action_id';

    const ACTION_NUMBER = 'action_number';

    const WAREHOUSE_ID = 'warehouse_id';

    const CREATED_AT = 'created_at';

    /**#@-*/

    const PREFIX_CODE = 'STR';

    /**#@-*/

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/stockMovement_stockTransfer');
    }

    /**
     * Stock transfer id
     *
     * @return int|null
     */
    public function getStockTransferId()
    {
        return $this->_getData(self::STOCK_TRANSFER_ID);
    }

    /**
     * Set stock transfer id
     *
     * @param int|null $stockTransferId
     * @return $this
     */
    public function setStockTransferId($stockTransferId)
    {
        return $this->setData(self::STOCK_TRANSFER_ID, $stockTransferId);
    }

    /**
     * Get Transfer Code
     *
     * @return string
     */
    public function getTransferCode()
    {
        return $this->_getData(self::TRANSFER_CODE);
    }

    /**
     * Set transfer code
     *
     * @param string $transferCode
     * @return $this
     */
    public function setProductId($transferCode)
    {
        return $this->setData(self::TRANSFER_CODE, $transferCode);
    }

    /**
     * Get qty
     *
     * @return int
     */
    public function getQty()
    {
        return $this->_getData(self::QTY);
    }

    /**
     * Set qty
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * Get total sku
     *
     * @return int
     */
    public function getTotalSku()
    {
        return $this->_getData(self::TOTAL_SKU);
    }

    /**
     * Set total sku
     *
     * @param int $totalSku
     * @return $this
     */
    public function setTotalSku($totalSku)
    {
        return $this->setData(self::TOTAL_SKU, $totalSku);
    }

    /**
     * Action code
     *
     * @return string
     */
    public function getActionCode()
    {
        return $this->_getData(self::ACTION_CODE);
    }

    /**
     * Set action code
     *
     * @param string $actionCode
     * @return $this
     */
    public function setActionCode($actionCode)
    {
        return $this->setData(self::ACTION_CODE, $actionCode);
    }

    /**
     * Action ID
     *
     * @return int
     */
    public function getActionId()
    {
        return $this->_getData(self::ACTION_ID);
    }

    /**
     * Set action id
     *
     * @param int $actionId
     * @return $this
     */
    public function setActionId($actionId)
    {
        return $this->setData(self::ACTION_ID, $actionId);
    }

    /**
     * Get Action number
     *
     * @return string|null
     */
    public function getActionNumber()
    {
        return $this->_getData(self::ACTION_NUMBER);
    }

    /**
     * Set action number
     *
     * @param string|null $actionNumber
     * @return $this
     */
    public function setActionNumber($actionNumber)
    {
        return $this->setData(self::ACTION_NUMBER, $actionNumber);
    }

    /**
     * Get warehouse Id
     *
     * @return int|null
     */
    public function getWarehouseId()
    {
        return $this->_getData(self::WAREHOUSE_ID);
    }

    /**
     * Set warehouse id
     *
     * @param int|null $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}