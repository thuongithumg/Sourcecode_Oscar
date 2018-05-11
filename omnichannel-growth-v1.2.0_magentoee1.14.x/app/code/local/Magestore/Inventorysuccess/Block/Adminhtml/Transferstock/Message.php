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
 * Inventorysuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Message extends Mage_Adminhtml_Block_Abstract
{
    /**
     * @return mixed
     */
    public function getNumberSkuInvalid()
    {
        return Mage::getSingleton('adminhtml/session')->getData('sku_invalid',true);
    }

    /**
     * @return mixed
     */
    public function hasError()
    {
        return Mage::getSingleton('adminhtml/session')->getData('error_import',true);
    }

    /**
     * @return string
     */
    public function getInvalidFileCsvUrl()
    {
        $type = Mage::getSingleton('adminhtml/session')->getData('import_type',true);
        switch ($type) {
            case Magestore_Inventorysuccess_Model_Service_Transfer_ImportService::TYPE_TRANSFER_IMPORT_SEND:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_sendstock/downloadInvalid');
            case Magestore_Inventorysuccess_Model_Service_Transfer_ImportService::TYPE_TRANSFER_IMPORT_SEND_RECEIVING:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_sendstock/downloadInvalidReceiving');
            case Magestore_Inventorysuccess_Model_Service_Transfer_ImportService::TYPE_TRANSFER_IMPORT_REQUEST:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_requeststock/downloadInvalid');
            case Magestore_Inventorysuccess_Model_Service_Transfer_ImportService::TYPE_TRANSFER_IMPORT_REQUEST_DELIVERY:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_requeststock/downloadInvalidDelivery');
            case Magestore_Inventorysuccess_Model_Service_Transfer_ImportService::TYPE_TRANSFER_IMPORT_REQUEST_RECEIVING:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_requeststock/downloadInvalidReceiving');
            case Magestore_Inventorysuccess_Model_Service_Transfer_ImportService::TYPE_TRANSFER_IMPORT_EXTERNAL:
                return $this->getUrl('adminhtml/inventorysuccess_transferstock_external/downloadInvalid');
        }
    }
}