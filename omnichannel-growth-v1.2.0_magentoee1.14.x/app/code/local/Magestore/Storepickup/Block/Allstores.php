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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Block_Allstores
 */
class Magestore_Storepickup_Block_Allstores extends Mage_Core_Block_Template {

    const URL = "https://maps.googleapis.com/maps/api/distancematrix/xml?";
    const DEF = "&mode=driving&sensor=true&key=";

    /**
     * Magestore_Storepickup_Block_Allstores constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return $this
     */
    public function _prepareLayout() {
        parent::_prepareLayout();

        $data = $this->getAllStores();

        //$pager = $this->getLayout()->createBlock('page/html_pager', 'storepickup.allstores.pager')->setCollection($data);
        // $this->setChild('pager', $pager);
        $data->load();
        return $this;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStoreById($id) {
        $data = Mage::getModel('storepickup/store')->load($id);
        return $data;
    }

    /**
     * @param $id
     * @return array
     */
    public function getImagebyStore($id) {
        $collection = Mage::getModel('storepickup/image')
                    ->getCollection()
                    ->addFieldToFilter('store_id', $id);
                    
        $url = array();
        foreach ($collection as $item) {
            if ($item->getData('name')) {
                $url[] = Mage::getBaseUrl('media') . 'storepickup/images' . $item->getData('name');
            }
        }
        return $url;
    }

    /**
     * @return mixed
     */
    public function getAllStores() {
        if (!$this->hasData('allstores')) {
            $collection = Mage::getModel('storepickup/store')->getCollection()
                    ->addFieldToFilter('status', 1);
            //->setOrder('store_name','ASC');

            if ($this->getRequest()->getParam('viewstore')) {
                $collection = $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('viewstore'));
            }
            if ($this->getRequest()->getParam('country')) {
                $country = $this->getRequest()->getParam('country');
                $collection = $collection->addFieldToFilter('country', array('like' => '%' . $country . '%'));
            }
            if ($this->getRequest()->getParam('state')) {
                $state = $this->getRequest()->getParam('state');
                $state = trim($state);
                $collection = $collection->addFieldToFilter('state', array('like' => '%' . $state . '%'));
            }
            if ($this->getRequest()->getParam('city')) {
                $city = $this->getRequest()->getParam('city');
                $city = trim($city);
                $collection = $collection->addFieldToFilter('city', array('like' => '%' . $city . '%'));
            }
            if ($this->getRequest()->getParam('name')) {
                $name = $this->getRequest()->getParam('name');
                $name = trim($name);
                $collection = $collection->addFieldToFilter('store_name', array('like' => '%' . $name . '%'));
            }
            if ($this->getRequest()->getParam('search_state_id')) {
                $state_id = $this->getRequest()->getParam('search_state_id');
                $collection = $collection->addFieldToFilter('state_id', array('like' => '%' . $state_id . '%'));
            }
            if ($this->getRequest()->getParam('zipcode')) {
                $zipcode = $this->getRequest()->getParam('zipcode');
                $collection = $collection->addFieldToFilter('zipcode', array('like' => '%' . $zipcode . '%'));
            }
            $this->setData('allstores', $collection);
        }
        return $this->getData('allstores');
    }

    /**
     * @param $dpoint
     * @return bool
     */
    public function getDistanceCustomer($dpoint)
    {
        $shippingAddress = Mage::helper('storepickup')->getCustomerAddress(true);
        if(!$shippingAddress) return false;
		$oGmap = Mage::getModel('storepickup/gmap');
        
        if(!$shippingAddress)
            return false;            
		$street = $shippingAddress->getStreet();        
        if (strrpos($street[0], ','))
            $address['street'] = trim(substr($street[0], 0, strrpos($street[0], ',')));
        else
            $address['street'] = $street[0];
        
        $address['city'] = $shippingAddress->getCity();
        $address['region'] = $shippingAddress->getRegion();
        $address['zipcode'] = $shippingAddress->getPostcode();
        $address['country'] = $shippingAddress->getCountryId();

        $coordinates = $oGmap->getCoordinates($address);
        
         if (!$coordinates) {
            $address['street'] = trim(substr($street[0], strrpos($street[0], ',') + 1));
            $coordinates = $oGmap->getCoordinates($address);
        }

        if (!$coordinates)
            return false;

        $spoint['lat'] = $coordinates['lat'];
        $spoint['lng'] = $coordinates['lng'];

        $distance_store = Mage::getSingleton('storepickup/gmap')->getDistanceCustomer($dpoint, $spoint);
 
        return $distance_store;
    }

    /**
     * @param $coordinatesCustomer
     * @param $dpoint
     * @return bool
     */
    public function getDistanceCustomerVer2($coordinatesCustomer, $dpoint)
	{
		// $shippingAddress = Mage::helper('storepickup')->getCustomerAddress(true);
        // if(!$shippingAddress) return false;
		// $oGmap = Mage::getModel('storepickup/gmap');
        
        // if(!$shippingAddress)
            // return false;            
		// $street = $shippingAddress->getStreet();        
        // if (strrpos($street[0], ','))
            // $address['street'] = trim(substr($street[0], 0, strrpos($street[0], ',')));
        // else
            // $address['street'] = $street[0];
        
        // $address['city'] = $shippingAddress->getCity();
        // $address['region'] = $shippingAddress->getRegion();
        // $address['zipcode'] = $shippingAddress->getPostcode();
        // $address['country'] = $shippingAddress->getCountryId();

        // $coordinates = $oGmap->getCoordinates($address);
        
         // if (!$coordinates) {
            // $address['street'] = trim(substr($street[0], strrpos($street[0], ',') + 1));
            // $coordinates = $oGmap->getCoordinates($address);
        // }

        if (!$coordinatesCustomer)
            return false;

        $spoint['lat'] = $coordinatesCustomer['lat'];
        $spoint['lng'] = $coordinatesCustomer['lng'];

        $distance_store = Mage::getSingleton('storepickup/gmap')->getDistanceCustomer($dpoint, $spoint);
 
        return $distance_store;
	}

    /**
     * @return mixed
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * @param $store
     * @return mixed
     */
    public function getCoordinates($store) {
        //$store = $this->getStore();
        $address['street'] = $store->getSuburb();
        $address['street'] = '';
        $address['city'] = $store->getCity();
        $address['region'] = $store->getRegion();
        $address['zipcode'] = $store->getZipcode();
        $address['country'] = $store->getCountryName();

        $coordinates = Mage::getModel('storepickup/gmap')
                ->getCoordinates($address);
        if (!$coordinates) {
            $coordinates['lat'] = '0.000';
            $coordinates['lng'] = '0.000';
        }

        return $coordinates;
    }

    /**
     * @return mixed
     */
    public function getGKey() {
        if (!$this->hasData('g_key')) {
            $this->setData('g_key', Mage::getModel('storepickup/gmap')->getGKey());
        }

        return $this->getData('g_key');
    }

    /**
     * @param $country
     * @return mixed
     */
    public function getCountryName($country) {
        $country = Mage::getResourceModel('directory/country_collection')
                ->addFieldToFilter('country_id', $country)
                ->getFirstItem();
        return $country->getName();
    }

    /**
     * @return array
     */
    public function getSearchConfiguration() {
        $storeId = Mage::app()->getStore()->getId();
        $searchconfig = array();
        $searchconfig['country'] = Mage::getStoreConfig("carriers/storepickup/search_country", $storeId);
        $searchconfig['state'] = Mage::getStoreConfig("carriers/storepickup/search_state", $storeId);
        $searchconfig['city'] = Mage::getStoreConfig("carriers/storepickup/search_city", $storeId);
        $searchconfig['name'] = Mage::getStoreConfig("carriers/storepickup/search_name", $storeId);
        return $searchconfig;
    }

    /**
     * @param null $field
     * @return null
     */
    public function getFormData($field = null) {
        $formData = Mage::getSingleton('core/session')->getPickupFormData();
        if ($field)
            return isset($formData[$field]) ? $formData[$field] : null;
        return $formData;
    }
    
    //Edit by Tien
    /**
     * @param $store
     * @param $format
     * @return string
     */
    public function getWorkingTime($store, $format) {
        $week = array(
            'Sun'=>'sunday',
            'Mon' => 'monday',
            'Tue' => 'tuesday',
            'Wed' => 'wednesday',
            'Thur' => 'thursday',
            'Fri' => 'friday',
            'Sat' => 'saturday'
        );
        
        $html = '';
        foreach ($week as $label => $day) {
            $html .= '<tr>';
            $html .= '<td>'. $this->__("$label:").'</td>';
            if (($store->getData($day.'_open') != $store->getData($day.'_close')) 
                    &&($store->getData($day.'_open') != $store->getData($day.'_open_break'))
                    &&($store->getData($day.'_close') != $store->getData($day.'_close_break')) 
                    && ($store->getData($day.'_open_break') != $store->getData($day.'_close_break')) 
                    && $store->getData($day.'_status') == 1) {
                
                $html .= '<td>'.date($format, strtotime($store->getData($day.'_open'))) 
                        . ' - ' . date($format, strtotime($store->getData($day.'_open_break')))
                        .' && '.date($format, strtotime($store->getData($day.'_close_break'))) 
                        . ' - ' . date($format, strtotime($store->getData($day.'_close'))).'</td>';
            }
               else if(($store->getData($day.'_open') == $store->getData($day.'_open_break')) 
                    && ($store->getData($day.'_close_break') != $store->getData($day.'_close')) 
        && $store->getData($day.'_status') == 1){
                   
                $html.='<td>'.date($format, strtotime($store->getData($day.'_close_break'))) 
                        . ' - ' . date($format, strtotime($store->getData($day.'_close'))).'</td>';
        }
        else if(($store->getData($day.'_open') != $store->getData($day.'_open_break')) 
                    && ($store->getData($day.'_close_break') == $store->getData($day.'_close')) 
        && $store->getData($day.'_status') == 1){
            $html.='<td>'.date($format, strtotime($store->getData($day.'_open'))) 
                        . ' - ' . date($format, strtotime($store->getData($day.'_open_break'))).'</td>';
        }
            else if(($store->getData($day.'_open') != $store->getData($day.'_close')) 
                    &&($store->getData($day.'_open_break') == $store->getData($day.'_close_break'))){
                $html .= '<td>'.date($format, strtotime($store->getData($day.'_open'))) . ' - ' . date($format, strtotime($store->getData($day.'_close'))) .'</td>'; 
            } else {
                $html .= '<td>'.$this->__('Closed').'</td>';
            }
            $html .= '</tr>';
        }
        return $html;
    }
    //End edit by Tien
    
    // (06-02-2013)
    /**
     * @return mixed
     */
    public function getDefaultZoom() {
        $storeId = Mage::app()->getStore()->getId();
        return Mage::getStoreConfig("carriers/storepickup/storezoom", $storeId);
    }

    /**
     * @return bool
     */
    public function getLatLngCustomer(){
		$shippingAddress = Mage::helper('storepickup')->getCustomerAddress(true);
        if(!$shippingAddress) return false;
       $oGmap = Mage::getModel('storepickup/gmap');
        
        if(!$shippingAddress)
            return false;
            
        $street = $shippingAddress->getStreet();
             
        
         
        if (strrpos($street[0], ','))
            $address['street'] = trim(substr($street[0], 0, strrpos($street[0], ',')));
        else
            $address['street'] = $street[0];
        
        $address['city'] = $shippingAddress->getCity();
        $address['region'] = $shippingAddress->getRegion();
        $address['zipcode'] = $shippingAddress->getPostcode();
        $address['country'] = $shippingAddress->getCountryId();

        $coordinates = $oGmap->getCoordinates($address);
        
         if (!$coordinates) {
            $address['street'] = trim(substr($street[0], strrpos($street[0], ',') + 1));
            $coordinates = $oGmap->getCoordinates($address);
        }
		return $coordinates;
		
	}

}
