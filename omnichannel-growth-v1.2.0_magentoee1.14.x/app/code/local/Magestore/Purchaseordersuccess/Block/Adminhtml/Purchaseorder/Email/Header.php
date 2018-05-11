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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Email_Header
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Header
{
    protected $_template = 'purchaseordersuccess/purchaseorder/email/header.phtml';

    public function getSupplierName()
    {
        return $this->getSupplierData('supplier_name');
    }

    public function getStreet()
    {
        if ($this->getSupplierData('street'))
            return '<br/>' . $this->getSupplierData('street');
        return '';
    }

    public function getPurchaseOrderData($attr)
    {
        return $this->purchaseOrder->getData($attr);
    }

    /**
     * Get formatted address
     *
     * @return string
     */
    public function getFormatedAddress()
    {
        $address = '';
        $region = $this->getRegion();
        $postCode = $this->getSupplierData('postcode');
        $city = $this->getSupplierData('city');
        $cityRegionZip = array();
        if ($city) {
            $cityRegionZip[] = $city;
        }
        if ($region) {
            $cityRegionZip[] = $region;
        }
        if ($postCode) {
            $cityRegionZip[] = $postCode;
        }
        if ($this->getSupplierData('street'))
            $address .= '<tr><td>' . $this->getSupplierData('street') . '</td></tr>';
        if (!empty($cityRegionZip))
            $address .= '<tr><td>' . implode(', ', $cityRegionZip) . '</td></tr>';
        $country = $this->getCountry();
        if ($country)
            $address .= '<tr><td>' . $country . '</td></tr>';
        return $address;
    }

    public function getPurchaseOrderCode()
    {
        return $this->purchaseOrder->getPurchaseCode();
    }

    public function getPurchaseDate()
    {
        return $this->formatDate(
            $this->purchaseOrder->getPurchasedAt(),
            Mage_Core_Model_Locale::FORMAT_TYPE_LONG
        );
    }

    public function getPurchaseOrderStatus()
    {
        $status = $this->purchaseOrder->getStatus();
        $options = Mage::getModel('purchaseordersuccess/purchaseorder_options_status')->getOptionHash();
        return $options[$status];
    }
}