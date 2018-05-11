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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Header
    extends Mage_Adminhtml_Block_Template
{
    CONST SUPPLIER = 1;
    CONST WAREHOUSE = 2;

    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    /**
     * @var Magestore_Suppliersuccess_Model_Supplier
     */
    protected $supplier;

    /**
     * @var Magestore_Inventorysuccess_Model_Warehouse
     */
    protected $warehouse;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->returnRequest = Mage::registry('current_return_request');
        $this->supplier = Mage::getModel('suppliersuccess/supplier')->load($this->returnRequest->getSupplierId());
        $this->warehouse = Mage::getModel('inventorysuccess/warehouse')->load($this->returnRequest->getWarehouseId());
    }

    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/return/edit/tab/return_summary/header.phtml';

    public function getSupplierInformation()
    {
        $html = $this->supplier->getSupplierName() . ' (' . $this->supplier->getSupplierCode() . ')';
        $address = $this->getFormatedAddress(self::SUPPLIER);
        if ($address != '')
            $html .= '<br/>' . $address;
        return $html;
    }

    public function getSupplierData($field)
    {
        return $this->supplier->getData($field);
    }

    public function getWarehouseInformation()
    {
        $html = $this->warehouse->getWarehouseName() . ' (' . $this->warehouse->getWarehouseCode() . ')';
        $address = $this->getFormatedAddress(self::WAREHOUSE);
        if ($address != '')
            $html .= '<br/>' . $address;
        return $html;
    }

    public function getWarehouseData($field)
    {
        return $this->warehouse->getData($field);
    }

    /**
     * Get formatted address
     *
     * @return string
     */
    public function getFormatedAddress($type)
    {
        $address = '';
        $region = $this->getRegion($type);
        $postCode = $this->getSupplierData('postcode');
        $city = $this->getSupplierData('city');
        $street = $this->getSupplierData('street');

        if($type == self::WAREHOUSE) {
            $postCode = $this->getWarehouseData('postcode');
            $city = $this->getWarehouseData('city');
            $street = $this->getWarehouseData('street');
        }
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
        if ($street)
            $address .= $street . '<br/>';
        if (!empty($cityRegionZip))
            $address .= implode(', ', $cityRegionZip) . '<br/>';
        $address .= $this->getCountry($type);

        return $address;
    }

    public function getStreetCity($type)
    {
        $result = array();
        $street = $this->getSupplierData('street');
        $city = $this->getSupplierData('city');

        if($type == self::WAREHOUSE) {
            $street = $this->getWarehouseData('street');
            $city = $this->getWarehouseData('city');
        }

        if ($street)
            $result[] = $street;
        if ($city)
            $result[] = $city;
        if (!empty($result))
            return '<br/>' . implode(', ', $result);
        return '';
    }

    /**
     * @return string
     */
    public function getPostCodeRegionCountry($type)
    {
        $result = array();
        $postCode = $this->getSupplierData('postcode');
        if($type == self::WAREHOUSE) {
            $postCode = $this->getWarehouseData('postcode');
        }
        $region = $this->getRegion($type);
        $country = $this->getCountry($type);
        if ($postCode)
            $result[] = $postCode;
        if ($region)
            $result[] = $region;
        if ($country)
            $result[] = $country;
        if (!empty($result))
            return '<br/>' . implode(', ', $result);
        return '';
    }

    /**
     * @return string
     */
    public function getCountry($type)
    {
        $countryId = $this->getSupplierData('country_id');
        if($type == self::WAREHOUSE) {
            $countryId = $this->getWarehouseData('country_id');
        }

        if ($countryId)
            return Mage::getModel('directory/country')
                ->loadByCode($countryId)
                ->getName();

        return '';
    }

    /**
     * @return string
     */
    public function getRegion($type)
    {
        $countryId = $this->getSupplierData('country_id');
        $regionId = $this->getSupplierData('region_id');
        $region = $this->getSupplierData('region');

        if($type == self::WAREHOUSE) {
            $countryId = $this->getWarehouseData('country_id');
            $regionId = $this->getWarehouseData('region_id');
            $region = $this->getWarehouseData('region');
        }

        if (!$countryId)
            return '';
        if ($regionId)
            return Mage::getModel('directory/region')
                ->load($regionId)
                ->getName();
        return $region;
    }

    /**
     * Get purchase date of PO
     *
     * @return string
     */
    public function getReturnDate()
    {
        $date = new DateTime($this->returnRequest->getReturnedAt());
        return $date->format('F j, Y');
    }
}