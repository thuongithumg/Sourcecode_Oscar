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
 * @package     Magestore_Coresuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Coresuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
class Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
{
    /**
     * Query process name
     */
    const QUERY_PROCESS = 'product_selection';
    
    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService 
     */
    protected $queryProcessorService;
    
    
    /**
     * 
     */
    public function __construct()
    {
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
    }
    
    /**
     * Create new Product Selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param type $data
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface
     */
    public function createSelection(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $data)
    {
        $selection->getResource()->save($selection);
        if(isset($data['products'])) {
            $this->setProducts($selection, $data['products']);
        }
        return $selection;
    }
    

    /**
     * @inheritdoc
     */
    public function addProduct(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $productId, $data)
    {
        $this->addProducts($selection, array($productId => $data));
        return $this;
    }

    /**
     * Add products to selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $data
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
     */
    public function addProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $data)
    {
        /* start queries processing */
        $this->queryProcessorService->start(self::QUERY_PROCESS);

        /* update existed products in Selection */
        $prepareData = $this->getResource()->prepareUpdateExistedProducts($selection, $data);
        $this->queryProcessorService->addQuery($prepareData['query_data'], self::QUERY_PROCESS);

        /* add new products to Selection */
        $queryData = $this->getResource()->prepareAddNewProducts($selection, $prepareData['new_products']);
        $this->queryProcessorService->addQuery($queryData, self::QUERY_PROCESS);

        /* process queries in Processor */
        $this->queryProcessorService->process(self::QUERY_PROCESS);

        return $this;        
    }

    /**
     * get product from selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param int $productId
     * @return Mage_Core_Model_Abstract
     */
    public function getProduct(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $productId)
    {
        $selectionProduct = $this->getProducts($selection, array($productId))
                                    ->setPageSize(1)
                                    ->setCurPage(1)
                                    ->getFirstItem();

        return $selectionProduct;
    }

    /**
     * Get products from Selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $productIds
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $productIds = array())
    {
        return $this->getResource()->getProducts($selection, $productIds);
    }

    /**
     * remove product from selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param int $productId
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
     */
    public function removeProduct(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $productId)
    {
        $this->removeProducts($selection, array($productId));
        return $this;
    }

    /**
     * remove products from selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $productIds
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
     */
    public function removeProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $productIds)
    {
        /* start queries processing */
        $this->queryProcessorService->start(self::QUERY_PROCESS);

        /* remove products from Selection */
        $queryData = $this->getResource()->prepareRemoveProducts($selection, $productIds);
        $this->queryProcessorService->addQuery($queryData, self::QUERY_PROCESS);

        /* process queries in Processor */
        $this->queryProcessorService->process(self::QUERY_PROCESS);
 
        return $this;
    }
    
    /**
     * remove all products from selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
     */
    public function removeAllProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection)
    {
        $this->getResource()->removeProducts($selection, array());
        return $this;        
    }    

    /**
     * set products to selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $data
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
     */
    public function setProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $data , $fromCatalog = false)
    {
        /* start queries processing */
        $this->queryProcessorService->start(self::QUERY_PROCESS);

        /* remove existed products not in $data
         * update existed products in $data
         * add new products
         * add queries to Processor 
         */
        $queries = $this->getResource()->prepareSetProducts($selection, $data , $fromCatalog);
        $this->queryProcessorService->addQueries($queries, self::QUERY_PROCESS);
        
        /* process queries in Processor */
        $this->queryProcessorService->process(self::QUERY_PROCESS);

        return $this;
    }
    

    /**
     * Get resource model
     * 
     * @return Magestore_Coresuccess_Model_Mysql4_ProductSelection
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('coresuccess/productSelection');
    }
    
    /**
     * Get resource model of Selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @return Mage_Core_Resource_Model_Abstract
     */
    public function getSelectionResource(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection)
    {
        return $selection->getResource();
    }    
}