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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Coresuccess Status Model
 * 
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
class Magestore_Coresuccess_Model_Service_QueryProcessorService
{
    
    /**
     * Define query types
     */
    CONST QUERY_TYPE_UPDATE = 'update';
    CONST QUERY_TYPE_INSERT = 'insert';
    CONST QUERY_TYPE_DELETE = 'delete';
    
    /**
     * @var array
     */
    private $_queryQueue = array();

    /**
     * @var Magestore_Coresuccess_Model_Mysql4_QueryProcessor
     */
    protected $_resource;    
    
    /*
     * @var string
     */
    protected $defaultProcess = 'default';

    /**
     * Add query to processor
     * 
     * @param array $queryData
     * @param string $process
     * @return Magestore_Coresuccess_Model_Service_QueryProcessorService
     */   
    public function addQuery($queryData, $process = null)
    {
        $process = $process ? $process : $this->defaultProcess;
        if(isset($this->_queryQueue[$process])) {
            $this->_queryQueue[$process][] = $queryData;
        } else {
            $this->_queryQueue[$process] = array($queryData);
        }
        return $this;
    }
    
    /**
     * Add queries to processor
     * 
     * @param array $queries
     * @param string $process
     * @return Magestore_Coresuccess_Model_Service_QueryProcessorService
     */   
    public function addQueries($queries, $process = null)
    {
        if(count($queries)) {
            foreach($queries as $query) {
                $this->addQuery($query, $process);
            }
        }
        return $this;
    }    
    
    /**
     * Get queries in queue
     * 
     * @param string $process
     * @return array
     */   
    public function getQueryQueue($process = null)
    {
        $process = $process ? $process : $this->defaultProcess;
        return isset($this->_queryQueue[$process]) ? $this->_queryQueue[$process] : array();
    }
    
    /**
     * Start processing
     * 
     * @param string $process
     * @return Magestore_Coresuccess_Model_Service_QueryProcessorService
     */  
    public function start($process = null)
    {
        $this->resetQueue($process);
        return $this;
    }    

    /**
     * Process queries in queue
     * 
     * @param string $process
     * @return Magestore_Coresuccess_Model_Service_QueryProcessorService
     */  
    public function process($process = null)
    {
        $this->getResource()->processQueries($this->getQueryQueue($process));
        $this->resetQueue($process);
    }

    /**
     * Remove queries in the queue
     * 
     * @param string $process
     * @return Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    public function resetQueue($process = null)
    {
        $process = $process ? $process : $this->defaultProcess;        
        $this->_queryQueue[$process] = array();
        return $this;
    }
    
    /**
     * 
     * @return Magestore_Coresuccess_Model_Mysql4_QueryProcessor
     */
    public function getResource()
    {
        return Mage::getResourceModel('coresuccess/queryProcessor');
    }
    
}