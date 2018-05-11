<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Inventory;

/**
 * @api
 */
interface LocationInterface
{      
    /**
     * Location Id
     *
     * @return int
     */
    public function getLocationId();    
    
    /**
     * 
     * @param int $id
     * @return $this
     */
    public function setLocationId($id);
    
    /**
     * Location Name
     *
     * @return string|null
     */
    public function getDisplayName();    
    
    /**
     * 
     * @param string $name
     * @return $this
     */
    public function setDisplayName($name);
    
    
    /**
     * Location Address
     *
     * @return string|null
     */
    public function getAddress();    
    
    /**
     * 
     * @param string $address
     * @return $this
     */
    public function setAddress($address);
    

}