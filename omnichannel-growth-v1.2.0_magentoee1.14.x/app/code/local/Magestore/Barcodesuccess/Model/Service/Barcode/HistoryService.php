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
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Barcodesuccess_Model_Service_Barcode_HistoryService
 */
class Magestore_Barcodesuccess_Model_Service_Barcode_HistoryService
{
    /**
     * @return array
     */
    public function getTypeOptionArray()
    {
        $availableOptions = $this->getOptionHash();
        $options          = array();
        foreach ( $availableOptions as $key => $value ) {
            $options[] = array(
                'label' => $value,
                'value' => $key,
            );
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return array(
            Magestore_Barcodesuccess_Model_History::TYPE_GENERATED => Mage::helper('barcodesuccess')->__('Generated'),
            Magestore_Barcodesuccess_Model_History::TYPE_IMPORTED  => Mage::helper('barcodesuccess')->__('Imported'),
        );
    }

    /**
     * @param $totalQty
     * @param $type
     * @param string $reason
     * @return string
     */
    public function saveHistory(
        $totalQty,
        $type,
        $reason = ''
    ) {
        $historyId       = '';
        $history         = Mage::getModel('barcodesuccess/history');
        $historyResource = Mage::getResourceModel('barcodesuccess/history');
        try {
            $admin   = Mage::getSingleton('admin/session')->getUser();
            $adminId = ($admin) ? $admin->getId() : 0;
            $history->setData('type', $type);
            $history->setData('reason', $reason);
            $history->setData('created_by', $adminId);
            $history->setData('total_qty', $totalQty);
            $historyResource->save($history);
            $historyId = $history->getId();
        } catch ( \Exception $e ) {
            $this->_getSession()->addError($e->getMessage());
        }
        return $historyId;
    }

    /**
     * remove history
     * @param $historyId
     */
    public function removeHistory( $historyId )
    {
        $history         = Mage::getModel('barcodesuccess/history');
        $historyResource = Mage::getResourceModel('barcodesuccess/history');
        try {
            $historyResource->load($history, $historyId);
            if ( $history->getId() ) {
                $historyResource->delete($history);
            }
        } catch ( \Exception $e ) {
            $this->_getSession()->addError($e->getMessage());
        }
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    public function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }


}