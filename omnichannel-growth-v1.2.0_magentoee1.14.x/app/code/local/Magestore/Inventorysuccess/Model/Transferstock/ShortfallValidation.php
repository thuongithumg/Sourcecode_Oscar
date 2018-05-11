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
 * Transferstock Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
use Magestore_Inventorysuccess_Model_Transferstock as Transferstock;
class Magestore_Inventorysuccess_Model_Transferstock_ShortfallValidation

{

    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/transferstock_product');
    }

    /**
     * @param $transferstock_id
     * @return bool
     */
    public function _checkShortFallStatus($transferstock_id){
        $transferStockManagement = Mage::getModel('inventorysuccess/transferstock')->load($transferstock_id);
        if($transferStockManagement->getStatus() == Transferstock::STATUS_PROCESSING){
                       return true;
        }
        return false;
    }

    /**
     * @param $transferstock_id
     * @param $type
     * @return bool
     */
    protected function _checkShortfallList($transferstock_id,$type){
        $transferStockProduct = Mage::getModel('inventorysuccess/transferstock_product')->getCollection();
        /* for type = request */
        if($type == TransferStock::TYPE_REQUEST) {
            $transferStockProduct->addFieldToFilter('transferstock_id', $transferstock_id);
            $transferStockProduct->getSelect()->where('(qty - qty_delivered) > ? OR (qty_delivered - qty_received - qty_returned) > ? ', 0, 0);
            if ($transferStockProduct->getSize() > 0) {
                return true;
            }
            return false;
        }
        /* for type = send */
        if($type == TransferStock::TYPE_SEND) {
            $transferStockProduct->addFieldToFilter('transferstock_id', $transferstock_id);
            $transferStockProduct->getSelect()->where('(qty - qty_received - qty_returned) > ? ', 0);
            if ($transferStockProduct->getSize() > 0) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @param $transferstock_id
     * @param $type
     * @return $this
     */
    public function _showNoticeShortfall($transferstock_id,$type)
    {
        if($this->_checkShortfallList($transferstock_id,$type) && $this->_checkShortFallStatus($transferstock_id)) {
            Mage::getSingleton('core/session')->addNotice(
                __('Some stock are in shortfall list ! Please continue delivery then receive or return stocks before COMPLETE this transaction.')
            );
        }
        return $this;
    }


}