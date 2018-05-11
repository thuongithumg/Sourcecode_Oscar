<?php

/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Reoportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */
class Magestore_Reportsuccess_Model_Observer
{
    const ENTITY = 'historics_product';

    /**
     * @param $observer
     */
    public function adminSessionUserLoginSuccess($observer){

        if (Mage::getStoreConfig('reportsuccess/general/use_cron')) {
            return;
        }
        /* Registry Historics report */
        $historics = new Varien_Object();
        $historics->setAttributesData(array('reportsuccess_match_result' => 0));
        $historics->setProductIds(array(1,2,3));
        Mage::getSingleton('index/indexer')->logEvent(
            $historics, self::ENTITY, Mage_Index_Model_Event::TYPE_REINDEX
        );

    }

    /**
     * @param $observer
     */
    public function upadteMacAfterTranferPO($observer){
        $data = $observer->getEvent()->getData('productInfo');
        if($data){
            $service = Mage::helper('reportsuccess')->service();
            $service->updateMacService($data);
        }
    }
    
}