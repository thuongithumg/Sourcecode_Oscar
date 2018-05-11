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
class Magestore_Inventorysuccess_Block_Adminhtml_Import_Notice extends Mage_Adminhtml_Block_Template
{
    /**
     * get admin session
     *
     * @return mixed
     */
    public function getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }


    /**
     * get number of invalid skus
     *
     * @return mixed
     */
    public function getNumberSkuInvalid()
    {
        return $this->getAdminSession()->getData('sku_invalid', true);
    }

    /**
     * check error
     *
     * @return mixed
     */
    public function isHasError()
    {
        return $this->getAdminSession()->getData('error_import', true);
    }

    /**
     * get unvalid url
     *
     * @return string
     */
    public function getInvalidFileCsvUrl()
    {
        $type = $this->getAdminSession()->getData('import_type', true);
        switch ($type) {
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_ADJUST_STOCK:
                return $this->getUrl('adminhtml/inventorysuccess_adjuststock_product/downloadInvalidCsv');
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_STOCKTAKING:
                return $this->getUrl('adminhtml/inventorysuccess_stocktaking_product/downloadInvalidCsv');
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_SEND:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_sendstock/downloadInvalid');
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_SEND_RECEIVING:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_sendstock/downloadInvalidReceiving');
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_requeststock/downloadInvalid');
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST_DELIVERY:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_requeststock/downloadInvalidDelivery');
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST_RECEIVING:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_requeststock/downloadInvalidReceiving');
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_EXTERNAL_TO:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_external/downloadInvalid');
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_EXTERNAL_FROM:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_external/downloadInvalid');
        }
    }

    /**
     * get unvalid url
     *
     * @return string
     */
    public function getInvalidFileCsvFileName()
    {
        $type = $this->getAdminSession()->getData('import_type', true);
        switch ($type) {
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_ADJUST_STOCK:
                return '';
            case Magestore_Inventorysuccess_Model_ImportType::TYPE_STOCKTAKING:
                return '';
        }
    }
}