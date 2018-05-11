<?php

class Magestore_Purchaseordersuccess_Block_Purchaseorder_Pdf_Abstract extends Mage_Core_Block_Template
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    /**
     * @var Magestore_Suppliersuccess_Model_Supplier
     */
    protected $supplier;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $key = $this->getRequest()->getParam('key');
        $this->purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($key, 'purchase_key');
        $this->supplier = Mage::getModel('suppliersuccess/supplier')->load($this->purchaseOrder->getSupplierId());
    }

    /**
     * @var string
     */
    protected $_template = '';

    public function getSupplierInformation()
    {
        $html = $this->supplier->getSupplierName() . ' (' . $this->supplier->getSupplierCode() . ')';
        $address = $this->getFormatedAddress();
        if ($address != '')
            $html .= '<br/>' . $address;
        return $html;
    }

    public function getSupplierData($field)
    {
        return $this->supplier->getData($field);
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
            $address .= $this->getSupplierData('street') . '<br/>';
        if (!empty($cityRegionZip))
            $address .= implode(', ', $cityRegionZip) . '<br/>';
        $address .= $this->getCountry();

        return $address;
    }

    public function getStreetCity()
    {
        $result = array();
        $street = $this->getSupplierData('street');
        $city = $this->getSupplierData('city');
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
    public function getPostCodeRegionCountry()
    {
        $result = array();
        $postCode = $this->getSupplierData('postcode');
        $region = $this->getRegion();
        $country = $this->getCountry();
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
    public function getCountry()
    {
        if ($this->getSupplierData('country_id'))
            return Mage::getModel('directory/country')
                ->loadByCode($this->getSupplierData('country_id'))
                ->getName();
        return '';
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        if (!$this->getSupplierData('country_id'))
            return '';
        if ($this->getSupplierData('region_id'))
            return Mage::getModel('directory/region')
                ->load($this->getSupplierData('region_id'))
                ->getName();
        return $this->getSupplierData('region');
    }

    /**
     * Get purchase date of PO
     *
     * @return string
     */
    public function getPurchaseDate()
    {
        return $this->formatDate(
            $this->purchaseOrder->getPurchasedAt(),
            Mage_Core_Model_Locale::FORMAT_TYPE_LONG
        );
    }

    /**
     *
     * @return string
     */
    public function getPOPaymentTerm()
    {
        $paymentTerm = $this->purchaseOrder->getPaymentTerm();
        if ($paymentTerm &&
            $paymentTerm != Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_PaymentTerm::OPTION_NONE_VALUE
        )
            return $paymentTerm;
        return $this->__('N/A');
    }


    /**
     *
     * @return string
     */
    public function getPOShippingMethod()
    {
        $shippingMethod = $this->purchaseOrder->getShippingMethod();
        if ($shippingMethod &&
            $shippingMethod != Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_ShippingMethod::OPTION_NONE_VALUE
        )
            return $shippingMethod;
        return $this->__('N/A');
    }
}