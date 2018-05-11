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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Class Magestore_Webpos_Model_Api2_Abstract
 */
abstract class Magestore_Webpos_Model_Api2_Abstract extends Mage_Api2_Model_Resource
{
    /**
     * @var Implement service
     */
    protected $_service = false;

    /**
     * @var bool
     */
    protected $_helper = false;

    /**
     *
     */
    const PAGE_SIZE = 'pageSize';

    /**
     *
     */
    const FIRST_INDEX = 0;

    /**
     *
     */
    const CURRENT_PAGE = 'currentPage';

    /**
     *
     */
    const FILTER_GROUP = 'filterGroups';


    const QUERY_PARAM_OR_FILTER = 'filter_or';

    const QUERY_PARAM_TO_FILTER = 'filter';

    const PAGE_SIZE_DEFAULT = 100;
    const PAGE_SIZE_MAX     = 9999999;

    /**
     * @return Implement service
     */
    public function getService(){
        return $this->_service;
    }

    /**
     * @return Implement helper
     */
    public function getHelper(){
        return $this->_helper;
    }

    /**
     * @param $name
     * @param array $arg
     * @return bool | Service instance
     */
    protected function _createService($name, $arg = array()){
        return (!empty($name))?Mage::getSingleton('magestore_webpos_service_'.$name, $arg):false;
    }

    /**
     * @param $scope
     * @param $name
     * @return mixed
     */
    protected function _getDataModel($scope, $name){
        $modelName = 'webpos/'.$scope.'_data_'.$name;
        return Mage::getModel($modelName);
    }

    /**
     * @return mixed
     */
    protected function getResponseDataModel(){
        return Mage::getModel('webpos/api2_response');
    }

    /**
     * @param array $data
     * @param array $messages
     * @param $status
     * @return mixed
     */
    protected function getResponseData($data = array(), $messages = array(), $status = ResponseInterface::STATUS_SUCCESS){
        $response = $this->getResponseDataModel();
        $response->setStatus($status);
        $response->setMessages($messages);
        $response->setResponseData($data);
        return $response->getData();
    }

    /**
     * @param array $keys
     * @return mixed
     */
    protected function _processRequestParams($keys = array()){
        $result = array();
        $request = $this->getRequest();
        $params = ($request->isPost())?$this->getRequest()->getBodyParams():$this->getRequest()->getParams();
        if(!empty($keys) && !empty($params)){
            if(is_array($keys)){
                foreach ($keys as $key){
                    if(isset($params[$key])){
                        $result[$key] = $params[$key];
                    }
                }
            }else{
                if(isset($params[$keys])){
                    $result = $params[$keys];
                }
            }
        }else{
            $result = $params;
        }
        return $result;
    }

    /**
     * Init frontend store
     */
    protected function _initStore(){
        $store = $this->_processRequestParams('store');
        if(!empty($store)){
            Mage::app()->setCurrentStore($store);
        }
        $session = $this->getRequest()->getParam('session');
        $storeId = Mage::getModel('webpos/user_webpossession')->getStoreIdBySession($session);
        if($storeId){
            Mage::app()->setCurrentStore($storeId);
        }
    }

    /**
     * @param Varien_Data_Collection_Db $collection
     * @return $this
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    protected function _applyFilter(Varien_Data_Collection_Db $collection)
    {
        $filter = $this->getRequest()->getFilter();


        if (!$filter) {
            return $this;
        }
        if (!is_array($filter)) {
            $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
        }
        if (method_exists($collection, 'addAttributeToFilter')) {
            $methodName = 'addAttributeToFilter';
        } elseif (method_exists($collection, 'addFieldToFilter')) {
            $methodName = 'addFieldToFilter';
        } else {
            return $this;
        }

        foreach ($filter as $filterEntry) {
            if (isset($filterEntry['in'])) {
                $condition = $filterEntry['in'];
                $condition = explode(',', $condition);
                $filterEntry['in'] = $condition;
            }
            $attributeCode = $filterEntry['attribute'];
            unset($filterEntry['attribute']);

            try {
                $collection->$methodName($attributeCode, $filterEntry);
            } catch(Exception $e) {
                $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
            }
        }
        return $this;
    }

    protected function _applyFilterOr(Varien_Data_Collection_Db $collection) {
        $filter = $this->getRequest()->getQuery(self::QUERY_PARAM_OR_FILTER);

//        $searchAttribute = Mage::helper('webpos')->getStoreConfig('webpos/product_search/product_attribute');
//        $searchAttributeArray = explode(',', $searchAttribute);
//        $barcodeAttribute = Mage::getStoreConfig('webpos/product_search/barcode', Mage::app()->getStore()->getId());
//
//        if($filter && !in_array($barcodeAttribute,$searchAttributeArray)){
//            $likeCondition = $filter[self::FIRST_INDEX]['like'];
//            $filter[] = array(
//                'attribute' => $barcodeAttribute = Mage::getStoreConfig('webpos/product_search/barcode', Mage::app()->getStore()->getId()),
//                'like' => $likeCondition
//            );
//        }

        if (!$filter) {
            return $this;
        }
        if (!is_array($filter)) {
            $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
        }
        if (method_exists($collection, 'addAttributeToFilter')) {
            $methodName = 'addAttributeToFilter';
        } elseif (method_exists($collection, 'addFieldToFilter')) {
            $methodName = 'addFieldToFilter';
        } else {
            return $this;
        }
        $filterOrArray = array();

        $result = array();

        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Collection) {
            /* @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $visibleAttributes */
            $visibleAttributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter();
            $result = $visibleAttributes->getColumnValues('attribute_code');
        }

        foreach ($filter as $filterEntry) {
            if (isset($filterEntry['in'])) {
                return $this;
            }
            $attributeCode = $filterEntry['attribute'];
            //check attribute if visible
            if (!empty($result) && !in_array($attributeCode, $result)) {
                continue;
            }

            unset($filterEntry['attribute']);

            if ($methodName == 'addAttributeToFilter') {
                if(isset($filterEntry['like'])){
                    $realkeywords = explode("fixbug*bugfix", $filterEntry['like']);
                    if(!empty($realkeywords)){
                        $filterEntry['like'] = implode('', $realkeywords);
                    }
                }
                $filterOrArray[] = array(
                    'attribute'=> $attributeCode,'like' => $filterEntry
                );
            }

        }
        if ($methodName == 'addAttributeToFilter') {
            $collection->$methodName($filterOrArray, '', 'left');
        }
        else {
            $collection->$methodName($filterOrArray);
        }

        return $this;
    }

    protected function _applyFilterTo(Varien_Data_Collection_Db $collection)
    {
        $filter = $this->getRequest()->getFilter(self::QUERY_PARAM_TO_FILTER);


        if (!$filter) {
            return $this;
        }
        if (!is_array($filter)) {
            $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
        }
        if (method_exists($collection, 'addAttributeToFilter')) {
            $methodName = 'addAttributeToFilter';
        } elseif (method_exists($collection, 'addFieldToFilter')) {
            $methodName = 'addFieldToFilter';
        } else {
            return $this;
        }
        foreach ($filter as $filterEntry) {
            $attributeCode = $filterEntry['attribute'];
            if(isset($filterEntry['in'])){
                $condition = isset($filterEntry['in']) ? $filterEntry['in'] : '';
                $condition = explode(',', $condition);
                $filterEntry['in'] = $condition;
            }
            unset($filterEntry['attribute']);
            try {
                $collection->$methodName($attributeCode, $filterEntry);
            } catch(Exception $e) {
                $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
            }
        }
        return $this;
    }

    protected function _applyFilterOrder(Varien_Data_Collection_Db $collection) {
        $filter = $this->getRequest()->getQuery(self::QUERY_PARAM_OR_FILTER);

        $searchTerm = $filter[0]['like'];
        if (!$filter) {
            return $this;
        }
        if (!is_array($filter)) {
            $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
        }

        $filterAttributeArray = array();
        $filterConditonsArray = array();
        foreach ($filter as $filterEntry) {
            $attributeCode = $filterEntry['attribute'];
            unset($filterEntry['attribute']);
            $filterAttributeArray[] = $attributeCode;
            $filterConditonsArray[] = array(
                'like'=> $searchTerm
            );
        };

        $collection->addFieldToFilter($filterAttributeArray, $filterConditonsArray);
        $collection->getSelect()->orwhere("concat_ws(' ',customer_firstname,customer_lastname) like '$searchTerm'");
        return $this;
    }

}
