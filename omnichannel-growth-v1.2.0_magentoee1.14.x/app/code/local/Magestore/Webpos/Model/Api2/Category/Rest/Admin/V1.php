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
class Magestore_Webpos_Model_Api2_Category_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{
    /**
     *
     */
    const OPERATION_GET_CATEGORY_LIST = 'get';

    /**
     *
     */
    const OPERATION_GET_BREADCRUMBS = 'getpath';


    /**
     * @throws Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::OPERATION_GET_CATEGORY_LIST:
                $result = $this->getCategoryList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_BREADCRUMBS:
                $result = $this->getBreadCrumbs();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function getCategoryList()
    {
        $collection = Mage::getResourceModel('catalog/category_collection');
        $storeId = Mage::helper('webpos/permission')->getCurrentStoreId();
        $collection->setStoreId($storeId);
        $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('image');
        $collection->addAttributeToSelect('path');
        $collection->addAttributeToSelect('parent_id');
        $collection->addAttributeToSelect('is_active');
        $filters = $this->getRequest()->getParam('filter');
        $collection->addFieldToFilter('path', array('like'=> "1/$rootCategoryId/%"));
        if(!Mage::getStoreConfig('webpos/product_search/show_all_category')){
            $collection->addAttributeToFilter('is_active', 1);
        }
        $collection->getSelect()
            ->columns('entity_id AS id')
        ;

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

        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);


        /* @var Varien_Data_Collection_Db $customerCollection */
        $this->_applyFilter($collection);
        $this->_applyFilterOr($collection);

        $categoryArray = array();
        foreach ($collection as $category) {
            $categoryNormalData = $category->getData();
            if ($category->getImageUrl()) {
                $categoryNormalData['image'] = $category->getImageUrl();
            } else {
                $categoryNormalData['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/webpos/catalog/category/image.jpg';
            }

            if ($categoryNormalData['level'] == 0) {
                $categoryNormalData['first_category'] = 1;
            } else {
                $categoryNormalData['first_category'] = 0;
            }
            if ($category->getChildren()) {
                $categoryNormalData['children'] = explode(',', $category->getChildren());
            } else {
                $categoryNormalData['children'] = array();
            }


            $categoryArray[] = $categoryNormalData;
        }

        $result['items'] = $categoryArray;
        $result['total_count'] = count($categoryArray);

        return $result;

    }

    /**
     * @return array
     * @throws Exception
     */
    public function getBreadCrumbs() {
        $result = array();
        $path = $this->getRequest()->getParam('path');
        $pathArray = explode('_', $path);
        foreach ($pathArray as $pathId) {
            $categoryModel = Mage::getModel('catalog/category')->load($pathId);
            if ($categoryModel->getLevel() > 1) {
                $result[] = array(
                    'name' => $categoryModel->getName(),
                    'id' => $categoryModel->getId()
                );
            }
        }
        return $result;
    }
}
