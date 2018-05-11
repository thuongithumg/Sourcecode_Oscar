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
 * Permission Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Permission extends Mage_Core_Model_Abstract
{
    
    /**#@+
     * Constants defined for keys of  data array
     */
    const ID = 'id';

    const USER_ID = 'user_id';

    const OBJECT_TYPE = 'object_type';

    const OBJECT_ID = 'object_id';

    const ROLE_ID = 'role_id';

    const CREATED_AT = 'created_at';

    const CREATED_BY = 'created_by';
    
    /**
     * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/permission');
    }

    /**
     * Get user id
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Set user id
     *
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get object type
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->getData(self::OBJECT_TYPE);
    }

    /**
     * Set object type
     *
     * @param int $objectType
     * @return $this
     */
    public function setObjectType($objectType)
    {
        return $this->setData(self::OBJECT_TYPE, $objectType);
    }

    /**
     * Get object id
     *
     * @return string
     */
    public function getObjectId()
    {
        return $this->getData(self::OBJECT_ID);
    }

    /**
     * Set object id
     *
     * @param int $objectId
     * @return $this
     */
    public function setObjectId($objectId)
    {
        return $this->setData(self::OBJECT_ID, $objectId);
    }

    /**
     * Get role id
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->getData(self::ROLE_ID);
    }

    /**
     * Set role id
     *
     * @param int $roleId
     * @return $this
     */
    public function setRoleId($roleId)
    {
        return $this->setData(self::ROLE_ID, $roleId);
    }

    /**
     * Get created at
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
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get created by
     *
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param int $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        return $this->setData(self::CREATED_BY, $createdBy);
    }
}