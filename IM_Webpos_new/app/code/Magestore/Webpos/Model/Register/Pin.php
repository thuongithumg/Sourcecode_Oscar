<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Register;

/**
 * Class Pos
 * @package Magestore\Webpos\Model\Pos
 */
class Pin extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Register\PinInterface
{

    /**
     *  Get Staff Id
     * @return string|null
     */
    public function getStaffId()
    {
        return $this->getData(self::STAFF_ID);
    }

    /**
     * Set Staff Id
     *
     * @param string $posId
     * @return $this
     */
    public function setStaffId($staffId)
    {
        $this->setData(self::STAFF_ID, $staffId);
        return $this;
    }

    /**
     *  Get Pin
     * @return string|null
     */
    public function getPinCode()
    {
        return $this->getData(self::PIN_CODE);
    }

    /**
     * Set Pin
     *
     * @param string $pinCode
     * @return $this
     */
    public function setPinCode($pinCode)
    {
        $this->setData(self::PIN_CODE, $pinCode);
        return $this;
    }
    /**
     *  Get Password
     * @return string|null
     */
    public function getPassword()
    {
        return $this->getData(self::PASSWORD);
    }

    /**
     * Set Password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->setData(self::PASSWORD, $password);
        return $this;
    }

    /**
     *  Get Pos Id
     * @return string|null
     */
    public function getPosId(){
        return $this->getData(self::POS_ID);
    }

    /**
     * Set Pos Id
     *
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId){
        $this->setData(self::POS_ID, $posId);
        return $this;
    }
}