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

class Magestore_Webpos_Model_User_Webpossession extends Mage_Core_Model_Abstract
{

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('webpos/user_webpossession');
    }

    /**
     * Load session model by token key
     * @param $session
     * @return bool
     */
    public function loadBySession($session){
        $model = $this->load($session, 'session_id');
        return ($model->getId())?$model:false;
    }

    /**
     * Get quote id by session
     * @param $session
     * @return string
     */
    public function getQuoteIdBySession($session){
        $sessionModel = $this->loadBySession($session);
        return ($sessionModel && $sessionModel->getId())?$sessionModel->getCurrentQuoteId():'';
    }

    /**
     * Get store id by session
     * @param $session
     * @return string
     */
    public function getStoreIdBySession($session){
        $sessionModel = $this->loadBySession($session);
        return ($sessionModel && $sessionModel->getId())?$sessionModel->getCurrentStoreId():'';
    }

    /**
     * Get till id by session
     * @param $session
     * @return string
     */
    public function getTillIdBySession($session){
        $sessionModel = $this->loadBySession($session);
        return ($sessionModel && $sessionModel->getId())?$sessionModel->getCurrentTillId():0;
    }

    /**
     * Get staff model
     * @return mixed
     */
    public function getStaff(){
        $staff = Mage::getModel('webpos/user');
        $staffId = $this->getStaffId();
        if($staffId){
            $staff->load($staffId);
        }
        return $staff;
    }

    /**
     * Get staff name
     * @return string
     */
    public function getStaffName(){
        $name = '';
        $staff = $this->getStaff();
        if($staff->getId()){
            $name = ($staff->getDisplayName())?$staff->getDisplayName():$staff->getUsername();
        }
        return $name;
    }

    /**
     * Get staff location id
     * @return string
     */
    public function getStaffLocationId(){
        $id = '';
        $staff = $this->getStaff();
        if($staff->getId()){
            $id = $staff->getLocationId();
        }
        return $id;
    }

    /**
     * Get available cash drawer
     * @param $session
     * @return array
     */
    public function getAvailableCashDrawer($session = false){
        $cashDrawer = array();
        $model = $this;
        if($session){
            $model = $this->loadBySession($session);
        }
        if($model){
            $staff = $model->getStaff();
            $tillIds = $staff->getTillIds();
            $locationId = $staff->getLocationId();
            $cashDrawer = Mage::getModel('webpos/till')->toOptionArray($locationId);
            unset($cashDrawer[Magestore_Webpos_Model_Till::VALUE_ALL_TILL]);
            if(!empty($cashDrawer)){
                foreach ($cashDrawer as $id => $name){
                    if(!in_array($id, $tillIds) && !in_array(Magestore_Webpos_Model_Till::VALUE_ALL_TILL, $tillIds)){
                        unset($cashDrawer[$id]);
                    }
                }
            }
        }
        return $cashDrawer;
    }
}