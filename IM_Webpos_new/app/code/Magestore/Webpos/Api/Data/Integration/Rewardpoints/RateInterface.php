<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Integration\Rewardpoints;

/**
 * @api
 */
interface RateInterface
{      
    /**
     * Location Id
     *
     * @return int
     */
    public function getRateId();    
    
    /**
     * 
     * @param int $id
     * @return $this
     */
    public function setRateId($id);
    
    /**
     * Location Name
     *
     * @return string|null
     */
    public function getCustomerGroupIds();    
    
    /**
     * 
     * @param string $name
     * @return $this
     */
    public function setCustomerGroupIds($name);
    
    
    /**
     * Location Address
     *
     * @return string|null
     */
    public function getDirection();    
    
    /**
     * 
     * @param string $address
     * @return $this
     */
    public function setDirection($address);
    
    
    /**
     * Location Address
     *
     * @return string|null
     */
    public function getPoints();    
    
    /**
     * 
     * @param string $address
     * @return $this
     */
    public function setPoints($address);


    
    /**
     * Location Address
     *
     * @return string|null
     */
    public function getMoney();    
    
    /**
     * 
     * @param string $address
     * @return $this
     */
    public function setMoney($address);    
    
    
    /**
     * Location Address
     *
     * @return string|null
     */
    public function getMaxPriceSpendedType();    
    
    /**
     * 
     * @param string $address
     * @return $this
     */
    public function setMaxPriceSpendedType($address);    


    /**
     * Location Address
     *
     * @return string|null
     */
    public function getMaxPriceSpendedValue();    
    
    /**
     * 
     * @param string $address
     * @return $this
     */
    public function setMaxPriceSpendedValue($address); 
    
    /**
     * Location Address
     *
     * @return string|null
     */
    public function getSortOrder();   
    
    /**
     * 
     * @param string $address
     * @return $this
     */
    public function setSortOrder($address);
    
    
    

}