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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Email_Header
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Header
{
    protected $_template = 'purchaseordersuccess/return/email/header.phtml';

    public function getSupplierName()
    {
        return $this->getSupplierData('supplier_name');
    }

    public function getWarehouseName()
    {
        return $this->getWarehouseData('warehouse_name');
    }

    public function getStreet($type)
    {
        $street = $this->getSupplierData('street');
        if($type == self::WAREHOUSE) {
            $street = $this->getWarehouseData('street');
        }

        if ($street)
            return '<br/>' . $street;
        return '';
    }

    public function getReturnData($attr)
    {
        return $this->returnRequest->getData($attr);
    }

//    /**
//     * Get formatted address
//     *
//     * @return string
//     */
//    public function getFormatedAddress($type)
//    {
//        $address = '';
//        $region = $this->getRegion($type);
//        $postCode = $this->getSupplierData('postcode');
//        $city = $this->getSupplierData('city');
//        $street = $this->getSupplierData('street');
//
//        if($type == self::WAREHOUSE) {
//            $postCode = $this->getWarehouseData('postcode');
//            $city = $this->getWarehouseData('city');
//            $street = $this->getWarehouseData('street');
//        }
//        $cityRegionZip = array();
//        if ($city) {
//            $cityRegionZip[] = $city;
//        }
//        if ($region) {
//            $cityRegionZip[] = $region;
//        }
//        if ($postCode) {
//            $cityRegionZip[] = $postCode;
//        }
//        if ($street)
//            $address .= '<tr><td>' . $street . '</td></tr>';
//        if (!empty($cityRegionZip))
//            $address .= '<tr><td>' . implode(', ', $cityRegionZip) . '</td></tr>';
//        $address .= '<tr><td>' . $this->getCountry($type) . '</td></tr>';
//
//        return $address;
//    }

    public function getReturnCode()
    {
        return $this->returnRequest->getReturnCode();
    }

    public function getReturnDate()
    {
        return $this->formatDate(
            $this->returnRequest->getReturnedAt(),
            Mage_Core_Model_Locale::FORMAT_TYPE_LONG
        );
    }

    public function getReturnStatus()
    {
        $status = $this->returnRequest->getStatus();
        $options = Mage::getModel('purchaseordersuccess/return_options_status')->getOptionHash();
        return $options[$status];
    }
}