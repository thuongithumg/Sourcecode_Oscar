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
 * Class Magestore_Storepickup_Model_Store
 */
class Magestore_Storepickup_Model_Store extends Mage_Core_Model_Abstract
{
    /* @var $_store_id Support Multiple Store */

    protected $_store_id = null;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'storepickup';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'storepickup';


    /**
     * @param $value
     * @return $this
     */
    public function setStoreId($value)
    {
        $this->_store_id = $value;
        return $this;
    }

    /**
     * @return Support
     */
    public function getStoreId()
    {
        return $this->_store_id;
    }

    /**
     * @return array
     */
    public function getStoreAttributes()
    {
        //return array();
        return array(
            'store_name',
            'status',
            'description',
            'address',
            'city',
        );

    }

    /**
     * @param $id
     * @param null $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        parent::load($id, $field);
        if ($this->getStoreId()) {
            $this->loadStoreValue();
        }
        return $this;
    }

    /**
     * @param null $storeId
     * @return $this
     */
    public function loadStoreValue($storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }
        if (!$storeId) {
            return $this;
        }
        $storeValues = Mage::getModel('storepickup/value')->getCollection()
            ->addFieldToFilter('storepickup_id', $this->getId())
            ->addFieldToFilter('store_id', $storeId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }
        return $this;
    }

    /**
     * @return mixed
     */
    protected function _beforeSave()
    {
        if ($storeId = $this->getStoreId()) {
            $defaultStore = Mage::getModel('storepickup/store')->load($this->getId());
            $storeAttributes = $this->getStoreAttributes();
            foreach ($storeAttributes as $attribute) {
                if ($this->getData($attribute . '_default')) {
                    $this->setData($attribute . '_in_store', false);
                } else {
                    $this->setData($attribute . '_in_store', true);
                    $this->setData($attribute . '_value', $this->getData($attribute));
                }
                $this->setData($attribute, $defaultStore->getData($attribute));
            }
        }
        return parent::_beforeSave();
    }

    /**
     * @return mixed
     */
    protected function _afterSave()
    {
        if ($storeId = $this->getStoreId()) {
            $storeAttributes = $this->getStoreAttributes();
            foreach ($storeAttributes as $attribute) {
                $attributeValue = Mage::getModel('storepickup/value')
                    ->loadAttributeValue($this->getId(), $storeId, $attribute);
                if ($this->getData($attribute . '_in_store')) {
                    try {
                        $attributeValue->setValue($this->getData($attribute . '_value'))->save();
                    } catch (Exception $e) {

                    }
                } elseif ($attributeValue && $attributeValue->getId()) {
                    try {
                        $attributeValue->delete();
                    } catch (Exception $e) {

                    }
                }
            }
        }

        // $this->setId($storeId);

//                         if($this->_count==3)
//        if($this->getStoreName()==''){
//            $this->setData(Mage::getSingleton('core/session')->getData('storeData'))->save();
//        }
        return parent::_afterSave();
//        Mage::getSingleton('core/session')->setData('storeData',null);
    }

    /* end code for multiple store */

    public function _construct()
    {
        parent::_construct();
        if ($storeId = Mage::app()->getStore()->getId()) {
            $this->setStoreId($storeId);
        }
        $this->_init('storepickup/store');
    }

    /**
     * @return string
     */
    public function getFormatedAddressforMap()
    {
        $address = $this->getAddress();
        $city = '';
        $region = '';
        //$zipcode = '';
        $country = '';

        if ($this->getCity())
            $city = ', <br>' . $this->getCity();
        if ($this->getRegion())
            $region = ', ' . $this->getRegion();
        if ($this->getZipcode())
            //$zipcode = ', ' . $this->getZipcode();
            if ($this->getCountryName())
                $country = ', <br>' . $this->getCountryName();

        return $address . $city . $region . $country;
    }

    /**
     * @return mixed
     */
    public function getCountryName()
    {
        if ($this->getCountry())
            if (!$this->hasData('country_name')) {
                $country = Mage::getModel('directory/country')
                    ->loadByCode($this->getCountry());
                $this->setData('country_name', $country->getName());
            }

        return $this->getData('country_name');
    }

    //Edit by Tien
    /**
     * @return $this
     */
    public function prepareToJSON()
    {
        $this->getImageSrc();
        $this->getCountryName();
        $this->getStoreViewUrl();
        $this->getHolidayDays();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoreViewUrl()
    {
        if (!$this->hasData('store_view_url')) {
            $this->setData('store_view_url', Mage::helper('storepickup/url')->getStoreViewUrl($this->getUrlIdPath(), $this->getId()));
        }
        return $this->getData('store_view_url');
    }

    /**
     * @return mixed
     */
    public function getImageSrc()
    {
        if (!$this->hasData('image_src')) {
            $image = Mage::getModel('storepickup/image')->getCollection()
                ->addFieldToFilter('store_id', $this->getId())
                ->addFieldToFilter('statuses', 1);

            $link = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'storepickup/images/';
            if (count($image)) {
                $folderName = $image->getData();
                $this->setData('image_src', $link . '/' . $folderName[0]['name']);
            } else {
                $this->setData('image_src', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'storepickup/market-store-icon.jpg');
            }
        }
        return $this->getData('image_src');
    }

    /**
     * @return mixed
     */
    public function getHolidayDays()
    {
        if (!$this->hasData('holidays')) {
            $this->setData('holidays', Mage::helper('storepickup')->getHolidayDays($this->getId()));
        }
        return $this->getData('holidays');
    }

    //End by Tien
    /**
     * @return mixed
     */
    public function getRegion()
    {
        if (!$this->getData('region')) {
            $this->setData('region', $this->getState());
        }

        return $this->getData('region');
    }

//   public function getCity() {
//       if (!$this->getData('city')) {
//           $this->setData('city', $this->getCity());
//      }
//
//       return $this->getData('city');
//   }

    /**
     * @return mixed
     */
    public function getSuburb()
    {
        if (!$this->getData('suburb')) {
            $this->setData('suburb', $this->getName());
        }

        return $this->getData('suburb');
    }

    /**
     * @return bool
     */
    public function import()
    {
        $data = $this->getData();
//        Mage::getSingleton('core/session')->setData('storeData', $data);
        //prepare status
        $data['status'] = 1;
        //check exited store
        $collection = $this->getCollection()
            ->addFieldToFilter('store_name', $data['store_name'])
//                ->addFieldToFilter('store_manager', $data['store_manager'])
//                ->addFieldToFilter('store_phone', $data['store_phone'])
//                ->addFieldToFilter('state', $data['state'])
//                ->addFieldToFilter('city', $data['city'])
//                ->addFieldToFilter('suburb', $data['suburb'])
            ->addFieldToFilter('address', $data['address']);
        if (count($collection))
            return false;

        if (!isset($data['store_name']) || $data['store_name'] == '')
            return FALSE;
        if (!isset($data['address']) || $data['address'] == '')
            return FALSE;
        if (!isset($data['city']) || $data['city'] == '')
            return FALSE;
        if (!isset($data['country']) || $data['country'] == '')
            return FALSE;
        if (!isset($data['zipcode']) || $data['zipcode'] == '')
            return FALSE;


        $storeName = strtolower(trim($data['store_name'], ' '));

        $storeName = Mage::helper('storepickup/url')->characterSpecial($storeName);
        $check = 1;
        while ($check == 0) {
            $stores = $this->getCollection()
                ->addFieldToFilter('url_id_path', $storeName)
                ->getFirstItem();

            if ($stores->getId()) {
                $storeName = $storeName . '-1';
            } else {
                $check = 0;
            }
        }


        $data['url_id_path'] = $storeName;

        $this->setData($data);
        $this->save();
//        $allStores = Mage::app()->getStores();
//        $url_suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix', Mage::app()->getStore()->getStoreId());
//        foreach ($allStores as $_eachStoreId => $val) {
//            $rewrite = Mage::getModel('core/url_rewrite')->getCollection()
//                            ->addFieldToFilter('id_path', $data['url_id_path'])
//                            ->addFieldToFilter('store_id', $_eachStoreId)->getFirstItem();
//
//            if (!$rewrite->getId()) {
//                $rewrite->setStoreId($_eachStoreId)
//                        ->setData('is_system', 0)
//                        ->setIdPath($data['url_id_path'])
//                        ->setRequestPath('storepickup/' . $data['url_id_path'] . $url_suffix)
//                        ->setTargetPath('storepickup/index/index/viewstore/' . $this->getId());
//                try {
//                    $rewrite->save();
//                } catch (Exception $e) {
//                    return false;
//                }
//            }
//
//        }

        $Allstores = Mage::app()->getStores();
        foreach ($Allstores as $store) {

            $this->setStoreId($store->getStoreId())
                ->updateUrlKey();
        }


        return $this->getId();
    }

    /**
     * @return mixed
     */
    public function save()
    {
        if ($this->getStoreLatitude() == 0 && $this->getStoreLongitude() == 0) {
            $address['street'] = $this->getAddress();
            $address['city'] = $this->getCity();
            $address['region'] = $this->getRegion();
            $address['zipcode'] = $this->getZipcode();
            $address['country'] = $this->getCountryName();

            $coordinates = Mage::getModel('storepickup/gmap')
                ->getCoordinates($address);
            if ($coordinates) {
                $this->setStoreLatitude($coordinates['lat']);
                $this->setStoreLongitude($coordinates['lng']);
            } else {
                $this->setStoreLatitude('0.000');
                $this->setStoreLongitude('0.000');
            }
        } elseif ($this->getLongtitude() && $this->getLatitude()) {
            $this->setStoreLatitude($this->getLatitude());
            $this->setStoreLongitude($this->getLongtitude());
        }
        return parent::save();
    }

    /**
     * @return array
     */
    public function getListStoreByCustomerAddress()
    {
        $options = array();

        //$cart = Mage::getSingleton('checkout/cart');
        $shippingAddress = Mage::helper('storepickup')->getCustomerAddress();

        $collection = $this->getCollection()
            ->addFieldToFilter('country', $shippingAddress->getCountryId());
        if ($shippingAddress->getPostcode()) {
            $collection->addFieldToFilter('zipcode', $shippingAddress->getPostcode());
        }

        if (is_array($shippingAddress->getStreet())) {
            $street = $shippingAddress->getStreet();
            $suburb = trim(substr($street[0], strrpos($street[0], ',') + 1));
            $collection->addFieldToFilter('suburb', $suburb);
        } else if ($shippingAddress->getCity()) {
            $collection->addFieldToFilter('city', $shippingAddress->getCity());
        } else if ($shippingAddress->getRegion()) {
            $collection->addFieldToFilter('state', $shippingAddress->getRegion());
        }

        if (count($collection))
            foreach ($collection as $store) {
                $options[$store->getId()] = $store->getStoreName();
            }
        return $options;
    }

    /**
     * @return array
     */
    public function getStoresUseGAPI()
    {
        $options = array();

        //$cart = Mage::getSingleton('checkout/cart');
        $shippingAddress = Mage::helper('storepickup')->getCustomerAddress();

        $collection = $this->getCollection()
            ->addFieldToFilter('country', $shippingAddress->getCountryId());

        if ($shippingAddress->getPostcode()) {
            $collection->addFieldToFilter('zipcode', $shippingAddress->getPostcode());
        }
        if ($shippingAddress->getCity()) {
            $collection->addFieldToFilter('city', $shippingAddress->getCity());
        }

//        $stores = $this->filterStoresUseGAPI($collection);
        $stores = $this->filterStoresUseGAPI();

        if (count($stores))
            foreach ($stores as $store) {
                $options[$store->getId()] = $store->getStoreName() . ' (' . number_format($store->getDistance()) . ' m)';
            }
        return $options;
    }

    /**
     * @return array
     */
    public function convertToList()
    {
        $options = array();
        $stores = $this->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('city', array("neq" => ''))
            ->addFieldToFilter('address', array("neq" => ''))
            ->addFieldToFilter('zipcode', array("neq" => ''))
            ->setOrder('store_name', 'ASC');
        if (count($stores))
            foreach ($stores as $store) {
                $options[$store->getId()]['label'] = $store->getStoreName();
                $options[$store->getId()]['info'] = $store;
            }
        return $options;
    }

    /**
     * @return array
     */
    public function filterStoresUseGAPI()
    {
        $stores = array();
        //$temp_array = array();
        //$storeID = Mage::app()->getStore()->getId();
        //$size = Mage::getStoreConfig('carriers/storepickup/num_store_real_distance', $storeID);
        //$size = $size ? $size : 10;
        $_storecollection = $this->getCollection()
            ->addFieldToFilter('status', 1);

        if (!count($_storecollection))
            return $stores;

        $shippingAddress = Mage::helper('storepickup')->getCustomerAddress();
        $oGmap = Mage::getModel('storepickup/gmap');

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
            return $this->convertToList();

        $spoint['lat'] = $coordinates['lat'];
        $spoint['lng'] = $coordinates['lng'];


        $distance_store = Mage::getSingleton('storepickup/gmap')->getDistanceStore($spoint, $_storecollection);

        $options = array();


        $i = 1;
        foreach ($_storecollection as $store) {
            $distance = $distance_store[$i];
            $storeTitle = ($distance['text']) ? $store->getStoreName() . ' (' . $distance['text'] . ')' : $store->getStoreName();
            $options[$store->getId()]['distance'] = $distance['value'] ? $distance['value'] : 9999999999;
            $options[$store->getId()]['label'] = $storeTitle;
            $store->setData('distance', $distance['text']);
            $options[$store->getId()]['info'] = $store;
            $i++;
        }

        usort($options, array(__CLASS__, 'cmp'));
        $storeID = Mage::app()->getStore()->getId();
        $top_n = Mage::getStoreConfig('carriers/storepickup/num_top_store', $storeID);
        $top_n = $top_n ? $top_n : 5;

        $result = array();
        $i = 0;
        foreach ($options as $key => $option) {
            $i++;
            $result[$key]['label'] = $option['label'];
            $result[$key]['info'] = $option['info'];

            if ($i == $top_n)
                break;
        }

        return $result;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    private function cmp($a, $b)
    {
        if ($a["distance"] == $b["distance"]) {
            return 0;
        }
        return ($a["distance"] < $b["distance"]) ? -1 : 1;
    }

    /**
     * @param $spoint
     * @param $dpoint
     * @return float
     */
    public function loadDistance($spoint, $dpoint)
    {
        return sqrt(($spoint['lat'] - $dpoint['lat']) * ($spoint['lat'] - $dpoint['lat']) + ($spoint['lng'] - $dpoint['lng']) * ($spoint['lng'] - $dpoint['lng']));
    }

    /**
     * @return array
     */
    public function convertToDefault()
    {
        $id_default = Mage::getStoreConfig("carriers/storepickup/storedefault");
        $options = array();
        $stores = $this->load($id_default, 'store_id'); //->addFieldToFilter('status',1)->setOrder('store_name','ASC');
        if (count($stores))
//		foreach($stores as $store)
//		{
            $options[$stores->getId()]['label'] = $stores->getStoreName();
        $options[$stores->getId()]['info'] = $stores;
//		}
        return $options;
    }

    /**
     * @return array
     */
    public function filterdefaultStoresUseGAPI()
    {

        $id_default = Mage::getStoreConfig("carriers/storepickup/storedefault");
        $stores = array();
        $_storecollection = $this->load($id_default, 'store_id'); //->addFieldToFilter('status',1);
        if (!count($_storecollection))
            return $stores;

        $shippingAddress = Mage::helper('storepickup')->getCustomerAddress();
        $oGmap = Mage::getModel('storepickup/gmap');

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
            return $this->convertToDefault();

        $spoint['lat'] = $coordinates['lat'];
        $spoint['lng'] = $coordinates['lng'];

//            foreach($_storecollection as $_store)
//            {
        $dpoint['lat'] = $_storecollection->getStoreLatitude();
        $dpoint['lng'] = $_storecollection->getStoreLongitude();
        $distance = $oGmap->getDistance($spoint, $dpoint);
        $distance = $distance ? $distance : 999999999;
        $_storecollection->setData('distance', $distance);
        $stores[] = $_storecollection;
//            }

        $storeID = Mage::app()->getStore()->getId();
        $top_n = Mage::getStoreConfig('carriers/storepickup/num_top_store', $storeID);
        $top_n = $top_n ? $top_n : 5;

        $stores = Mage::helper('storepickup/location')->getTopStore($stores, $top_n);

        $options = array();

        if (count($stores))
            foreach ($stores as $store) {
                $storeTitle = ($store->getDistance() && $store->getDistance() != 999999999) ? $store->getStoreName() . ' (' . number_format($store->getDistance()) . ' m)' : $store->getStoreName();
                $options[$store->getId()]['label'] = $storeTitle;
                $options[$store->getId()]['info'] = $store;
            }


        return $options;
    }


    /**
     * @param string $rewriteRequestPath
     */
    public function updateUrlKey($rewriteRequestPath = '')
    {
//        if ((version_compare(Mage::getVersion(), '1.13', '>=')) && (version_compare(Mage::getVersion(), '1.14.0.1', '<='))) {
//        }
        $id = $this->getId();
        $store_id = $this->_store_id;
        if (!$store_id) {
            $store_id = 0;
        }
        //$allStores = Mage::app()->getStores();
        $url_key = $rewriteRequestPath ? $rewriteRequestPath : $this->getData('url_id_path');
        $url_suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix', Mage::app()->getStore()->getStoreId());

        $urlrewrite = $this->loadByIdpath($url_key, $store_id);
        $urlrewrite->setData('id_path', $url_key);
        $urlrewrite->setData('request_path', 'storepickup/' . $url_key . $url_suffix);
        $urlrewrite->setData('target_path', 'storepickup/index/index/viewstore/' . $id);
        $urlrewrite->setData('store_id', $store_id);


        try {
            $urlrewrite->save();
        } catch (Exception $e) {

        }
    }

    /**
     * @param $idPath
     * @param $storeId
     * @return mixed
     */
    public function loadByIdpath($idPath, $storeId)
    {

        $model = Mage::getModel('core/url_rewrite')->getCollection()
            ->addFieldToFilter('id_path', $idPath)
            ->addFieldToFilter('store_id', $storeId)
            ->getFirstItem();

        return $model;
    }

    public function delete()
    {
        if($this->getWarehouseId()){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storepickup')->__('Unable to delete %s store',$this->getStoreName()));
            return $this;
        }
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    /* End by Son */

    public function convertWarehouseToStore(Magestore_Inventorysuccess_Model_Warehouse $warehouse){
        $array = array(
            'warehouse_id' => 'warehouse_id',
            'warehouse_name' => 'store_name',
            'contact_email' => 'store_email',
            'telephone' => 'store_phone',
            'street' => 'address',
            'city' => 'city',
            'country_id' => 'country',
            'region' => 'state',
            'region_id' => 'state_id',
            'postcode' => 'zipcode',
//            'status' => 'status',
        );
        foreach ($array as $key => $value){
            $this->setData($value,$warehouse->getData($key));
        }
        $address = $warehouse->getData('street').', '.$warehouse->getData('city').' '.$warehouse->getData('region').' '.$warehouse->getData('country_id').' '.$warehouse->getData('postcode');
        $location = Mage::helper('storepickup')->geocode($address);
        if ($location){
            $this->setData('store_latitude',$location[0]);
            $this->setData('store_longitude',$location[1]);
        }
        $this->setData('zoom_level',15); //why 5, because it's look good
        $storeManager = explode('@',$warehouse->getData('contact_email'));
        if (count($storeManager))
            $this->setData('store_manager',$storeManager[0]); //why 5, because it's look good
        $this->setData('pin_color','f75448');
        $this->setData('minimum_gap',45);
        $this->setData('shipping_price',0);
        $this->setData('status',2);

        return $this;
    }
}
