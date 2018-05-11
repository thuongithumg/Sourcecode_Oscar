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
 * Warehouse Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Warehouse extends Mage_Core_Model_Abstract
{
    
    /**#@+
     * Constants defined for keys of  data array
     */
    const WAREHOUSE_ID = 'warehouse_id';

    const WAREHOUSE_NAME = 'warehouse_name';

    const WAREHOUSE_CODE = 'warehouse_code';

    const CONTACT_EMAIL = 'contact_email';

    const TELEPHONE = 'telephone';

    const STREET = 'street';

    const CITY = 'city';

    const COUNTRY_ID = 'country_id';

    const REGION = 'region';

    const REGION_ID = 'region_id';

    const POSTCODE = 'postcode';

    const STATUS = 'status';

    const IS_PRIMARY = 'is_primary';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';
    
    const STOCK_ID = 'stock_id';
    
    const STORE_ID = 'store_id';
    
    /**
     * is_primary options
     */
    const PRIMARY_YES = 1;
    const PRIMARY_NO = 0;
    
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    /**
     * @var int
     */
    protected $_permissionType = 1;
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'inventorySuccess_warehouse';

    /**
     * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/warehouse');
    }
    
    /**
     * Warehouse id
     *
     * @return int|null
     */
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }

    /**
     * Set warehouse id
     *
     * @param int $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * Warehouse name
     *
     * @return string
     */
    public function getWarehouseName()
    {
        return $this->getData(self::WAREHOUSE_NAME);
    }

    /**
     * Set warehouse name
     *
     * @param int $warehouseName
     * @return $this
     */
    public function setWarehouseName($warehouseName)
    {
        return $this->setData(self::WAREHOUSE_NAME, $warehouseName);
    }

    /**
     * Warehouse code
     *
     * @return string
     */
    public function getWarehouseCode()
    {
        return $this->getData(self::WAREHOUSE_CODE);
    }

    /**
     * Set warehouse code
     *
     * @param int $warehouseCode
     * @return $this
     */
    public function setWarehouseCode($warehouseCode)
    {
        return $this->setData(self::WAREHOUSE_CODE, $warehouseCode);
    }

    /**
     * Contact email
     *
     * @return string|null
     */
    public function getContactEmail()
    {
        return $this->getData(self::CONTACT_EMAIL);
    }

    /**
     * Set contact email
     *
     * @param int $contactEmail
     * @return $this
     */
    public function setContactEmail($contactEmail)
    {
        return $this->setData(self::CONTACT_EMAIL, $contactEmail);
    }

    /**
     * telephone
     *
     * @return int|null
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * Set telephone
     *
     * @param int $telephone
     * @return $this
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * Street
     *
     * @return string|null
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * Set street
     *
     * @param int $street
     * @return $this
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * City
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * Set city
     *
     * @param int $city
     * @return $this
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Country id
     *
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * Set country id
     *
     * @param int $countryId
     * @return $this
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * Region
     *
     * @return string|null
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * Set region
     *
     * @param int $region
     * @return $this
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * Region ID
     *
     * @return int|null
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * Set region id
     *
     * @param int $regionId
     * @return $this
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * Postcode
     *
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * Set postcode
     *
     * @param int $postcode
     * @return $this
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Is primary
     *
     * @return boolean
     */
    public function getIsPrimary()
    {
        return $this->getData(self::IS_PRIMARY);
    }

    /**
     * Set is primary
     *
     * @param int $isPrimary
     * @return $this
     */
    public function setIsPrimary($isPrimary)
    {
        return $this->setData(self::IS_PRIMARY, $isPrimary);
    }

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param int $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Updated at
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param int $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
    
    /**
     * Get stock id
     *
     * @return int
     */
    public function getStockId()
    {
        return $this->getData(self::STOCK_ID);
    }

    /**
     * Set stock id
     *
     * @param int $stockId
     * @return $this
     */
    public function setStockId($stockId)
    {
        return $this->setData(self::STOCK_ID, $stockId);
    }    
    
    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }
    
    
    /**
     * Get store id
     *
     * @return array
     */
    public function getStoreIds()
    {
        return Magestore_Coresuccess_Model_Service::warehouseStoreService()
                ->getStoreIdsFromWarehouseId($this->getWarehouseId());
    }    

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }     

    /**
     * @return int
     */
    public function getPermissionType()
    {
        return $this->_permissionType;
    }

    /**
     * @return int
     */
    public function checkWarehouseCode($warehouseId = null)
    {
        $collection = $this->getCollection()->addFieldToFilter(self::WAREHOUSE_CODE, $this->getWarehouseCode());
        if ($warehouseId) {
            $collection->addFieldToFilter('warehouse_id', array('neq' => $warehouseId));
        }
        return $collection->count();
    }
}