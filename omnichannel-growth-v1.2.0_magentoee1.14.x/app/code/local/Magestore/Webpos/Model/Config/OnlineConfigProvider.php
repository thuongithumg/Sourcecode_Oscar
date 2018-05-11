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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Config_OnlineConfigProvider extends Magestore_Webpos_Model_Abstract
{
    public function getConfig()
    {


        $sections = Mage::getStoreConfig('webpos/online/sections');
        $useOnlineDefault = Mage::getStoreConfig('webpos/online/use_online_default');
        $useCustomOrderId = Mage::getStoreConfig('webpos/online/use_custom_order_id');
        $defaultShipping = Mage::getStoreConfig('webpos/shipping/defaultshipping');
        $defaultPayment = Mage::getStoreConfig('webpos/payment/defaultpayment');
        $requireSessions = Mage::getStoreConfig('webpos/general/enable_session');
        $data['sections'] = $sections;
        $data['use_online_default'] = $useOnlineDefault;
        $data['use_custom_order_id'] = $useCustomOrderId;
        $data['is_session_required'] = $requireSessions;
        $data['available_pos'] = $this->getCurrentAvailablePos();
        $data['cash_values'] = $this->getCurrentPosDenominations();

        $output = array(
            'online_data' => $data,
            'default_shipping' => $defaultShipping,
            'default_payment' => $defaultPayment
        );
        $configObject = new \Varien_Object();
        $configObject->setData($output);
        $output = $configObject->getData();

        return $output;
    }

    /**
     * @return array
     */
    public function getCurrentAvailablePos(){
        $posCollection = Mage::getModel('webpos/pos')->getCollection();
        $availablePosData = array();
        $session = Mage::helper('webpos/permission')->getCurrentSession();
        if($session){
            $staffId = Mage::helper('webpos/permission')->getCurrentUser();
            if($staffId){
                $availablePos = $posCollection->getAvailablePos($staffId);
                foreach ($availablePos as $pos){
                    $data = $pos->getData();
                    $data['denominations'] = $pos->getDenominations();
                    $availablePosData[] = $data;
                }
            }
        }
        return $availablePosData;
    }

    /**
     * @return array
     */
    public function getCurrentPosDenominations(){
        $posCollection = Mage::getModel('webpos/pos')->getCollection();
        $denominations = array();
        $staffModel = Mage::helper('webpos/permission')->getCurrentStaffModel();
        if($staffModel->getId()){
            $staffId = $staffModel->getId();
            if($staffId){
                $posCollection->addFieldToFilter('user_id', array('eq' => $staffId));
                $pos = $posCollection->getFirstItem();
                if($pos->getId()){
                    $denominations = $pos->getDenominations();
                }
            }
        }
        return $denominations;
    }
}
