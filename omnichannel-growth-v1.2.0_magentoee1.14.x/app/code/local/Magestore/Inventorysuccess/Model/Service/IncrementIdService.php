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
 * Adjuststock Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_IncrementIdService
{
    CONST DEFAULT_ID = 1;
    CONST CODE_LENGTH = 8;
   

    /**
     * Generate next code number
     * 
     * @param string $prefixCode
     * @return string
     */    
    public function getNextCode($prefixCode)
    {
        $nextId = $this->getNextId($prefixCode);
        
        /* generate the increment id */
        $formatId = pow(10, self::CODE_LENGTH + 1) + $nextId;
        $formatId = (string) $formatId;
        $formatId = substr($formatId, 0-self::CODE_LENGTH);
        
        /* update current Id */
        $this->updateId($prefixCode, $nextId);
        
        return $prefixCode . $formatId;
    }
    
    /**
     * Get next increment Id
     * 
     * @param string $prefixCode
     * @return int
     */
    public function getNextId($prefixCode)
    {
        $model = Mage::getModel('inventorysuccess/incrementId');
        $model->load($prefixCode, 'code');
        $nextId = $model->getCurrentId() + 1;
        return $nextId;
    }

    /**
     * Update current increment Id
     * 
     * @param string $prefixCode
     * @param int $id
     */    
    public function updateId($prefixCode, $id = null)
    {
        $model = Mage::getModel('inventorysuccess/incrementId');
        $model->load($prefixCode, 'code'); 

        if($id && $id > $model->getCurrentId()) {
            $model->setCode($prefixCode);
            $model->setCurrentId($id);
        } else {
            $model->setCode($prefixCode);
            $model->setCurrentId($model->getCurrentId() + 1);
        }
        $model->save();
    }    
}