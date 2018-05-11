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
 * Coresuccess Status Model
 *
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
class Magestore_Debugsuccess_Model_Service
{
    /**
     * get warehouse service
     *
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public static function debugInventoryService()
    {
        return self::getService('debugsuccess/service_debug_debugService');
    }

    /**
     * get service model
     *
     * @param string $servicePath
     * @throws Exception
     */
    public static function getService( $servicePath )
    {
        $service = Mage::getSingleton($servicePath);
        if ( $service == false ) {
            throw new Exception('There is no available service: ' . $servicePath);
        }
        return $service;
    }

}