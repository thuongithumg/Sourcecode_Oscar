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
class Magestore_Coresuccess_Model_Mysql4_QueryProcessor extends Magestore_Coresuccess_Model_Mysql4_Base
{
    /**
     * Define query types
     */
    CONST QUERY_TYPE_UPDATE = 'update';
    CONST QUERY_TYPE_INSERT = 'insert';
    CONST QUERY_TYPE_DELETE = 'delete';
    
    /**
     * Process queries
     * 
     * @param array $queries
     * @return Magestore_Coresuccess_Model_Mysql4_QueryProcessor
     */
    public function processQueries($queries)
    {
        if(!count($queries)) {
            return $this;
        }
        $connection = $this->_getWriteAdapter();
        try{
            $connection->beginTransaction();
            foreach($queries as $queryData) {
                if(!isset($queryData['type'])) {
                    continue;
                }
                switch($queryData['type']) {
                    case self::QUERY_TYPE_INSERT:
                        $connection->insertOnDuplicate($queryData['table'], $queryData['values']);
                        break;
                    case self::QUERY_TYPE_UPDATE:
                        $connection->update($queryData['table'], $queryData['values'], $queryData['condition']);
                        break;
                    case self::QUERY_TYPE_DELETE:
                        $connection->delete($queryData['table'], $queryData['condition']);
                        break;
                }
            }
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            throw new Exception($e->getMessage());
        }
        return $this;        
    }

}