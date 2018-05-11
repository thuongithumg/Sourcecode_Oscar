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

class Magestore_Webpos_Model_Pos extends Mage_Core_Model_Abstract {

    const DENOMINATION_IDS = "denomination_ids";
    const DENOMINATION_ID = "denomination_id";
    const SORT_ORDER = "sort_order";
    const ALL = "all";
    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_pos';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'data_object';

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/pos');
    }

    public function toOptionArray() {
        $options = array();
        $posCollection = $this->getCollection();
        if ($posCollection->getSize() > 1) {
            $options = array('' => '--- Select Pos ---');
        }
        foreach ($posCollection as $pos) {
            $key = $pos->getLocationId();
            $value = $pos->getDisplayName();
            $options [$key] = $value;
        }
        return $options;
    }

    public function getOptionArray()
    {
        $options = array();
        $posCollection = $this->getCollection();
        foreach ($posCollection as $pos) {
            $options[] = array(
                'value' => $pos->getId(),
                'label' => $pos->getPosName()
            );
        }
        return $options;
    }

    /**
     * Denominations
     * @return string
     */
    public function getDenominationIds(){
        return $this->getData(self::DENOMINATION_IDS);
    }

    /**
     * Denominations
     */
    public function getDenominations()
    {
        $denominationIds = $this->getDenominationIds();
        $denominations = Mage::getModel('webpos/denomination')->getCollection();
        $denominations->setOrder(self::SORT_ORDER, 'ASC');
        if($denominationIds){
            $denominationIds = explode(',',$denominationIds);
            if(!empty($denominationIds) && !in_array(self::ALL, $denominationIds)){
                $denominations->addFieldToFilter(self::DENOMINATION_ID, array('in' => $denominationIds));
            }
        }
        return $denominations->getData();
    }


    /**
     * assign staff for pos
     *
     * @param string $posId
     * @param string $staffId
     * @return boolean
     * @throws Exception
     */
    public function assignStaff($posId, $staffId)
    {
        $posList = $this->getCollection()
            ->addFieldToFilter('user_id', $staffId);
        if($posList->getSize()) {
            foreach ($posList as $pos) {
                $pos->setUserId(null);
                try {
                    $pos->save();
                }catch (\Exception $e) {

                }
            }
        }
        $pos = $this->load($posId);
        if(Mage::getStoreConfig('webpos/general/enable_session')) {
            if ($pos->getUserId() && $pos->getUserId() != $staffId) {
                throw new Exception(Mage::helper('webpos')->__('Can not connect to the pos'));
            }
        }
        $pos->setUserId($staffId);
        try {
            $pos->save();
        }catch (\Exception $e) {
            throw new Exception(Mage::helper('webpos')->__('Can not connect to the pos'));
        }
        if($pos->getId() || $pos->getId() == 0) {

        }
        return true;
    }

    /**
     * unassign staff in pos
     * @param string $posId
     *
     * @return mixed
     */
    public function unassignStaff($posId)
    {
        $pos = $this->load($posId);
        $pos->setUserId(null);
        try {
            $pos->save();
        }catch (\Exception $e) {

        }
    }

    public function getAvailableStaff($shiftId = 0)
    {
        $options = array();
        $options[] = array('value' => null, 'label' => ' ');
        $collection = $this->getCollection()->getAvailabeStaff($shiftId);
        $optionsArr = array();
        if ($collection->getSize() > 0) {
            foreach ($collection as $staff) {
                $optionsArr[] = array('value' => $staff->getId(), 'label' => $staff->getData('display_name'));
            }
        }
        $options = array_merge($options, $optionsArr);
        return $options;
    }
}
