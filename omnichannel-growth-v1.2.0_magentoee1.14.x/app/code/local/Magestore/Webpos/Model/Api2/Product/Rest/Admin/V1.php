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
 * API2 for catalog_product (Admin)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Webpos_Model_Api2_Product_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{
    /**
     *
     */
    const OPERATION_GET_PRODUCT_LIST = 'get';

    /**
     *
     */
    const OPERATION_GET_PRODUCT_ALLLIST = 'list';

    const OPERATION_GET_OPTIONS = 'getoptions';

    const OPERATION_GET_STOCKS_IN_WAREHOUSES = 'get_stocks_in_warehouses';

    /**
     *
     */

    /* @var Magestore_Webpos_Helper_Product $productHelper */
    private $productHelper;

    /**
     * Magestore_Webpos_Model_Api2_Product_Rest_Admin_V1 constructor.
     */
    public function __construct()
    {
        $this->productHelper = Mage::helper('webpos/product');
    }

    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::OPERATION_GET_PRODUCT_LIST:
                $result = $this->getProductList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_PRODUCT_ALLLIST:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->getAllProductList($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_OPTIONS:
                $result = $this->getProductOptionsInformation();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_STOCKS_IN_WAREHOUSES:
                $result = $this->getStocksInWarehouses();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getAllProductList($params)
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $isShowProductOutStock = Mage::helper('webpos')->getStoreConfig('webpos/general/show_product_outofstock');
        $itemIds = $params['itemsId'];
        $paramsData = new Varien_Object(array(
            'product_ids' => $itemIds
        ));
        Mage::dispatchEvent('webpos_catalog_product_get_by_ids', array('params' => $paramsData));
        $productMedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product';
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('entity_id', array('in' => $itemIds));
        $store = $this->_getStore();
        $collection->setStoreId($store->getId());
        $collection->addStoreFilter($store);
        $collection->addAttributeToSelect('*')->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=' . Mage::getModel('cataloginventory/stock')->getId(),
            'left')
            ->getSelect()
            ->columns('entity_id AS id');

        /* allow to apply custom filters */
        Mage::dispatchEvent('webpos_catalog_product_collection_filter', array('collection' => $collection));

        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }

        /* @var Varien_Data_Collection_Db $customerCollection */
        $this->_applyFilterTo($collection);
        $result['total_count'] = $collection->getSize();
        $collection->load();
        $collection->addCategoryIds();

        $products = array();
        foreach ($collection as $productModel) {
            $productModel = Mage::getModel('webpos/catalog_product')->load($productModel->getId());
            $item = $productModel->getData();
            $item['category_ids'] = $productModel->getCategoryIds();
            $item['available_qty'] = $productModel->getStockItem()->getQty();
            $item['final_price'] = $productModel->getFinalPrice();
            if ($productModel->getImage() && $productModel->getImage() != 'no_selection') {
//                $item['image'] = $productMedia.$item['image'];
                $item['image'] = $productModel->getImageUrl();
            } else {
                $item['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                    'magestore/webpos/catalog/category/image.jpg';
            }



            if ($item['stock_item']['is_in_stock']) {
                $item['isShowOutStock'] = 0;
                $item['is_in_stock'] = 1;
            } else {
                $item['isShowOutStock'] = 1;
                $item['is_in_stock'] = 0;
            }

            $products[] = $item;

        }
        $result['items'] = $products;


        return $result;

    }

    /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function getProductList()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $productMedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product';
        $collection = Mage::getModel('catalog/product')->getCollection();
        $searchAttribute = Mage::helper('webpos')->getStoreConfig('webpos/product_search/product_attribute');
        $searchAttributeArray = explode(',', $searchAttribute);
        $collection->addAttributeToSelect($searchAttributeArray);

        if (in_array('visibility', $searchAttributeArray)) {
            $collection->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE));
        }

        $collection->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('type_id', array('in' => $this->getProductTypeIds()))
            ->addAttributeToFilter(array(
                array('attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'),
                array('attribute' => 'webpos_visible', 'eq' => Magestore_Webpos_Model_Source_Entity_Attribute_Source_Boolean::VALUE_YES, 'left'),
            ), '', 'left');
        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            $collection->addAttributeToSort($orderField, $this->getRequest()->getOrderDirection());
        }
        $session = $this->getRequest()->getParam('session');
        $storeId = Mage::getModel('webpos/user_webpossession')->getStoreIdBySession($session);
        Mage::app()->setCurrentStore($storeId);
        $collection->setStoreId($storeId);
        $collection->addStoreFilter(Mage::app()->getStore($storeId));

        $collection->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=' . Mage::getModel('cataloginventory/stock')->getId(),
            'left')
            ->getSelect()
            ->columns('entity_id AS id');

        /* allow to apply custom filters */
        Mage::dispatchEvent('webpos_catalog_product_collection_filter', array('collection' => $collection));

        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if ($pageSize) {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                $this->_critical(self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }


        $showOutOfStock = $this->getRequest()->getParam('show_out_stock');
        if (!$showOutOfStock) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        }

        /* @var Varien_Data_Collection_Db $customerCollection */

        $this->_applyFilter($collection);
        $this->_applyFilterOr($collection);

        //$this->_applyFilterTo($collection);

        /*Search product by barcode in barcode sucess table*/
        if(Mage::helper('core')->isModuleEnabled('Magestore_Barcodesuccess') && !$collection->getSize() && $this->getRequest()->getQuery(Magestore_Webpos_Model_Api2_Abstract::QUERY_PARAM_OR_FILTER)){
            /*Filter in barcode attribute table*/
            $filter = $this->getRequest()->getQuery(Magestore_Webpos_Model_Api2_Abstract::QUERY_PARAM_OR_FILTER);
            $realkeywords = explode("fixbug*bugfix", $filter[Magestore_Webpos_Model_Api2_Abstract::FIRST_INDEX]['like']);
            if(!empty($realkeywords)){
                $filter[Magestore_Webpos_Model_Api2_Abstract::FIRST_INDEX]['like'] = implode('', $realkeywords);
            }
            $keyword = trim($filter[Magestore_Webpos_Model_Api2_Abstract::FIRST_INDEX]['like'],"%");
            $barcodeProduct = Mage::getModel('barcodesuccess/barcode')->getCollection()->addFieldToFilter('barcode',$keyword);
            if($barcodeProduct->getSize()){
                $data = $barcodeProduct->getData();
                $productId = $data[Magestore_Webpos_Model_Api2_Abstract::FIRST_INDEX]['product_id'];
                $collection = Mage::getModel('catalog/product')->getCollection();
                $collection->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('type_id', array('in' => $this->getProductTypeIds()))
                    ->addAttributeToFilter(array(
                        array('attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'),
                        array('attribute' => 'webpos_visible', 'eq' => Magestore_Webpos_Model_Source_Entity_Attribute_Source_Boolean::VALUE_YES, 'left'),
                    ), '', 'left');
                $collection->joinField('qty',
                    'cataloginventory/stock_item',
                    'qty',
                    'product_id=entity_id',
                    '{{table}}.stock_id=' . Mage::getModel('cataloginventory/stock')->getId(),
                    'left')
                    ->getSelect()
                    ->columns('entity_id AS id');
                $collection->addAttributeToFilter('entity_id',$productId);

            }
        }

        if(!$collection->getSize() && $this->getRequest()->getQuery(Magestore_Webpos_Model_Api2_Abstract::QUERY_PARAM_OR_FILTER)){
            $filter = $this->getRequest()->getQuery(Magestore_Webpos_Model_Api2_Abstract::QUERY_PARAM_OR_FILTER);
            $realkeywords = explode("fixbug*bugfix", $filter[Magestore_Webpos_Model_Api2_Abstract::FIRST_INDEX]['like']);
            if(!empty($realkeywords)){
                $filter[Magestore_Webpos_Model_Api2_Abstract::FIRST_INDEX]['like'] = implode('', $realkeywords);
            }
            $keyword = trim($filter[Magestore_Webpos_Model_Api2_Abstract::FIRST_INDEX]['like'],"%");
            $barcodeAttr = Mage::helper('webpos')->getStoreConfig('webpos/product_search/barcode', $storeId);
            $barcodeAttr = ($barcodeAttr)?$barcodeAttr:'sku';
            $childs = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter($barcodeAttr, $keyword);
            if($childs->getSize() > 0){
                $child_id = $childs->getFirstItem()->getId();
                $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($child_id);
                if(!empty($parent_ids)){
                    $collection = Mage::getModel('catalog/product')->getCollection();
                    $collection->addAttributeToFilter('status', 1)
                        ->addAttributeToFilter('entity_id', array('in' => $parent_ids))
                        ->addAttributeToFilter('type_id', array('in' => $this->getProductTypeIds()))
                        ->addAttributeToFilter(array(
                            array('attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'),
                            array('attribute' => 'webpos_visible', 'eq' => Magestore_Webpos_Model_Source_Entity_Attribute_Source_Boolean::VALUE_YES, 'left'),
                        ), '', 'left');
                    $collection->joinField('qty',
                        'cataloginventory/stock_item',
                        'qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=' . Mage::getModel('cataloginventory/stock')->getId(),
                        'left')
                        ->getSelect()
                        ->columns('entity_id AS id');
                }
            }
        }

        $result['total_count'] = $collection->getSize();
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);
        $collection->load();
        $collection->addCategoryIds();


        $products = array();
        foreach ($collection as $productModel) {
            $formatCategories = array();
            $categories = $productModel->getCategoryIds();
            foreach ($categories as $category) {
                $formatCategories[] = "'" . $category . "'";
            }
            /* @var Magestore_Webpos_Model_Catalog_Product $productModel */
            $productModel = Mage::getModel('webpos/catalog_product')->load($productModel->getId());
            $stockItem = $productModel->getStockItem();
            $item = $productModel->getData();

            $item['is_salable'] = $productModel->getIsSalable();
            $item['category_ids'] = implode(' ', $formatCategories);
            $item['minimum_qty'] = $stockItem->getMinSaleQty() ? $stockItem->getMinSaleQty() : 0;
            $item['maximum_qty'] = $stockItem->getMaxSaleQty() ? $stockItem->getMaxSaleQty() : 0;
            $item['is_qty_decimal'] = $stockItem->getIsQtyDecimal();
            $item['qty_increment'] = $this->getQtyIncrements($stockItem);
            $item['enable_qty_increments'] = $stockItem->getEnableQtyIncrements();
            $item['backorders'] = $stockItem->getBackorders();
            $item['manage_stock'] = $stockItem->getManageStock();
            $item['json_config'] = null;
            $item['config_options'] = null;
            $item['price_config'] = null;
            $item['custom_options'] = null;
            $item['grouped_options'] = null;
            $item['bundle_options'] = null;
            $item['id'] = $productModel->getEntityId();
            if ($this->getRequest()->getParam('status') == 'sync') {
                $item['barcode_options'] = $productModel->getBarcodeOptions();
                $item['barcode_string'] = $productModel->getBarcodeString();
                $item['search_string'] = $productModel->getSearchString();
                $item['json_config'] = $productModel->getJsonConfig();
                $item['config_options'] = $productModel->getConfigOptions();
                $item['price_config'] = $productModel->getPriceConfig();
                $item['custom_options'] = null;

                if ($productModel->hasOptions()) {
                    $item['custom_options'] = $this->getProductHelper()->getOptions($productModel);
                }

                if (is_array($productModel->getGroupedOptions())) {
                    $item['grouped_options'] = array_values($productModel->getGroupedOptions());
                } else {
                    $item['grouped_options'] = null;
                }
                $item['bundle_options'] = $productModel->getBundleOptions();
                if (is_array($productModel->getBundleOptions())) {
                    $item['bundle_options'] = array_values($productModel->getBundleOptions());
                } else {
                    $item['bundle_options'] = null;
                }

                if ($productModel->getTypeId() == 'giftvoucher') {
                    /** @var Magestore_Giftvoucher_Model_Product $productModel */
                    $item['giftvoucher_options'] = $this->getProductHelper()->getGiftvoucherOption($productModel);
                }
            }

            if ($productModel->getCustomercreditValue()) {
                $item['customercredit_value'] = $productModel->getCustomercreditValue();
            }

            if ($productModel->getStorecreditType()) {
                $item['storecredit_type'] = $productModel->getStorecreditType();
            }

            if ($productModel->getStorecreditRate()) {
                $item['storecredit_rate'] = $productModel->getStorecreditRate();
            }

            if ($productModel->getStorecreditMin()) {
                $item['storecredit_min'] = $productModel->getStorecreditMin();
            }

            if ($productModel->getStorecreditMax()) {
                $item['storecredit_max'] = $productModel->getStorecreditMax();
            }


            if ($productModel->hasOptions()) {
                $item['options'] = 1;
            } else {
                $item['options'] = 0;
            }

            /* Save barcode string into IndexDB*/
            if(Mage::helper('core')->isModuleEnabled('Magestore_Barcodesuccess')){
                $barcodeProducts = Mage::getModel('barcodesuccess/barcode')->getCollection()->addFieldToFilter('product_id',$productModel->getEntityId());
                $barcodes = array();
                foreach ($barcodeProducts as $barcodeProduct) {
                    $barcodes[] = $barcodeProduct->getData('barcode');
                }
                if(!isset($item['barcode_string'])){
                    $item['barcode_string'] = '';
                }
                $item['barcode_string'] .= implode(',', $barcodes).',';
            }

            $item['available_qty'] = $productModel->getStockItem()->getQty();
            // $item['final_price'] = $productModel->getFinalPrice();

            $storeId = Mage::app()->getStore()->getId();
            $discountedPrice = Mage::getResourceModel('catalogrule/rule')->getRulePrice(
                Mage::app()->getLocale()->storeTimeStamp($storeId),
                Mage::app()->getStore($storeId)->getWebsiteId(),
                Mage::getSingleton('customer/session')->getCustomerGroupId(),
                $productModel->getId());

            if ($discountedPrice === false) { // if no rule applied for the product
                $item['final_price'] = $productModel->getFinalPrice();
            } else {
                $item['final_price'] = $discountedPrice;
            }
            $item['price_excluding_tax'] = Mage::helper('tax')->getPrice($productModel, $item['final_price'], false );

            if ($productModel->getImage() && $productModel->getImage() != 'no_selection') {
//                $item['image'] = $productMedia.$item['image'];
                $item['image'] = $productModel->getImageUrl();
            } else {
                $item['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
                    'magestore/webpos/catalog/category/image.jpg';
            }

            if(!count($productModel->getMediaGalleryImages())) {
                $item['images'] = array($item['image']);
            } else {
                $item['images'] = $this->getGalleryUrl($productModel->getMediaGalleryImages());
            }


            if ($item['stock_item']['is_in_stock']) {
                $item['isShowOutStock'] = 0;
                $item['is_in_stock'] = 1;
            } else {
                $item['isShowOutStock'] = 1;
                $item['is_in_stock'] = 0;
            }


            if (!$showOutOfStock && !$item['stock_item']['is_in_stock']) {
                $result['total_count'] = $result['total_count'] - 1;
            } else {
                $products[] = $item;
            }
        }

        $result['items'] = $products;
//        $result['query'] = $collection->getSelectSql(true);
        return $result;

    }

    /**
     *
     */
    public function getProductOptionsInformation()
    {
        $productId = $this->getRequest()->getParam('id');
        /** @var Magestore_Webpos_Model_Catalog_Product $productModel */
        $productModel = Mage::getModel('webpos/catalog_product')->load($productId);
        $item['json_config'] = $productModel->getJsonConfig();
        $item['config_options'] = $productModel->getConfigOptions();
        $item['price_config'] = $productModel->getPriceConfig();
        $item['custom_options'] = null;

        if ($productModel->hasOptions()) {
            $item['custom_options'] = $this->getProductHelper()->getOptions($productModel);
        }

        if (is_array($productModel->getGroupedOptions())) {
            $item['grouped_options'] = array_values($productModel->getGroupedOptions());
        } else {
            $item['grouped_options'] = null;
        }
        $item['bundle_options'] = $productModel->getBundleOptions();
        if (is_array($productModel->getBundleOptions())) {
            $item['bundle_options'] = array_values($productModel->getBundleOptions());
        } else {
            $item['bundle_options'] = null;
        }

        if ($productModel->getTypeId() == 'giftvoucher') {
            /** @var Magestore_Giftvoucher_Model_Product $productModel */
            $item['giftvoucher_options'] = $this->getProductHelper()->getGiftvoucherOption($productModel);
        }

        return $item;
    }


    /**
     * @param Varien_Data_Collection_Db $collection
     * @return $this
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
                return $this;
            }
            $attributeCode = $filterEntry['attribute'];
            unset($filterEntry['attribute']);

            if ($attributeCode != 'category_ids') {
                try {
                    $collection->$methodName($attributeCode, $filterEntry);
                } catch (Exception $e) {
                    $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
                }
            } else {
                $categoryId = preg_replace("/[^0-9]/", "", $filterEntry);
                $category = Mage::getModel('catalog/category')->load($categoryId);
                $collection->addCategoryFilter($category)->addAttributeToSelect('*');
            }
        }

        return $this;
    }

    /**
     * get product type ids to support
     * @return array
     */
    public function getProductTypeIds()
    {
        $types = array(
            'virtual', 'simple', 'grouped', 'bundle', 'configurable', 'customercredit', 'giftvoucher'
        );
        return $types;
    }

    /**
     * @return Magestore_Webpos_Helper_Product
     */
    public function getProductHelper()
    {
        return $this->productHelper;
    }

    /**
     * @param Magestore_Webpos_Helper_Product $productHelper
     */
    public function setProductHelper($productHelper)
    {
        $this->productHelper = $productHelper;
    }

    /** get qty increments
     * @param  array
     * @return int
     */
    public function getQtyIncrements($stockItem)
    {
        $qtyIncrement = 0;
        $stockData = $stockItem->getData();
        if(is_array($stockData) && array_key_exists('enable_qty_increments', $stockData) && $stockData['enable_qty_increments'] == 1){
            if(array_key_exists('qty_increments', $stockData) && $stockData['qty_increments']  > 0){
                $qtyIncrement = $stockData['qty_increments'];
            }
        }
        return $qtyIncrement;
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
     * @return array
     */
    public function getStocksInWarehouses(){
        $response = array();
        $helper = Mage::helper('webpos');
        if($helper->isInventorySuccessEnable()){
            $productId = $this->_processRequestParams('product_id');
            if($productId){
                $stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
                $response['stocks'] = $stockRegistryService->getStocksFromEnableWarehouses(array($productId))->getData();
            }
        }
        return $response;
    }


    /**
     * @param $productGallery
     * @return array
     */
    public function getGalleryUrl($productGallery)
    {
        $images = array();
        foreach ($productGallery as $image) {
            $images[] = $image->getUrl();
        }
        return $images;
    }

}
