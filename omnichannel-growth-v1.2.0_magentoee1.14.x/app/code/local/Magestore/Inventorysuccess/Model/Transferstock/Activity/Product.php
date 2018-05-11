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
 * Transferstock Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Transferstock_Activity_Product
    extends
    Mage_Core_Model_Abstract
    implements
    Magestore_Inventorysuccess_Model_Service_ProductSelection_StockActivityProductInterface
{
    const ACTIVITY_PRODUCT_ID = 'activity_product_id';
    const ACTIVITY_ID         = 'activity_id';
    const PRODUCT_ID          = 'product_id';
    const PRODUCT_NAME        = 'product_name';
    const PRODUCT_SKU         = 'product_sku';
    const QTY                 = 'qty';

    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/transferstock_activity_product');
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setActivityProductId( $value )
    {
        return $this->setData(self::ACTIVITY_PRODUCT_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setActivityId( $value )
    {
        return $this->setData(self::ACTIVITY_ID, $value);
    }


    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductId( $value )
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductName( $value )
    {
        return $this->setData(self::PRODUCT_NAME, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductSku( $value )
    {
        return $this->setData(self::PRODUCT_SKU, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setQty( $value )
    {
        return $this->setData(self::QTY, $value);
    }


    /**
     * @return string
     */
    public function getActivityProductId()
    {
        return $this->getData(self::ACTIVITY_PRODUCT_ID);
    }

    /**
     * @return string
     */
    public function getActivityId()
    {
        return $this->getData(self::ACTIVITY_ID);
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    /**
     * @return string
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }
}