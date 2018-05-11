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
 * Class Magestore_Inventorysuccess_Model_Stocktaking_Product
 */
class Magestore_Inventorysuccess_Model_Stocktaking_Product extends Mage_Core_Model_Abstract
{
    CONST ID = 'stocktaking_product_id';
    CONST STOCKTAKING_ID = 'stocktaking_id';
    CONST PRODUCT_ID = 'product_id';
    CONST PRODUCT_NAME = 'product_name';
    CONST PRODUCT_SKU = 'product_sku';
    CONST OLD_QTY = 'old_qty';
    CONST STOCKTAKING_QTY = 'stocktaking_qty';
    CONST TYPE = 'type';

    /**
     * Magestore_Inventorysuccess_Model_Stocktaking_Product constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/stocktaking_product');
    }

    /**
     * @inheritDoc
     */
    public function getStocktakingProductId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setStocktakingProductId($id)
    {
        return $this->setData(self::ID, $id);
    }


    /**
     * @inheritDoc
     */
    public function getStocktakingId()
    {
        return $this->getData(self::STOCKTAKING_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStocktakingId($STOCKTAKINGId)
    {
        return $this->setData(self::STOCKTAKING_ID, $STOCKTAKINGId);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setProductName($productName)
    {
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * @inheritDoc
     */
    public function getProductSku()
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setProductSku($productSku)
    {
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * @inheritDoc
     */
    public function getOldQty()
    {
        return $this->getData(self::OLD_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setOldQty($qty)
    {
        return $this->setData(self::OLD_QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getStocktakingQty()
    {
        return $this->getData(self::STOCKTAKING_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setStocktakingQty($qty)
    {
        return $this->setData(self::STOCKTAKING_QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }


}
