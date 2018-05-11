<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Salesreport_SqlService
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Model_Service_Salesreport_SqlService
{

    /**
     * @param $table
     * @param $dateleCondition
     * @return bool
     */
    public function deleteData($table,$dateleCondition){
        try{
            $string = '';
            foreach($dateleCondition as $key => $data){
                $string .=  $data.',';
            }
            $list_id_arr = explode(',',$string);

            $cr = Mage::getSingleton('core/resource');
            $connection = $cr->getConnection('core_write');
            $connection->beginTransaction();
            $connection->delete(
                $table,
                $connection->quoteInto($table . '.id in (?) ', $list_id_arr)
            );
            $connection->commit();
            return true;
        }catch (Exception $e){
            $connection->rollBack();
            zend_debug::dump($e->getMessage());
            Mage::log($e->getMessage(),null,'log.log');
        }
    }

    /**
     * @param $table
     * @param $items
     * @return bool
     */
    public function updateData($table,$items){
        try{
            $cr = Mage::getSingleton('core/resource');
            $connection = $cr->getConnection('core_write');
            $connection->beginTransaction();
            $connection->insertMultiple($table,$items);
            $connection->commit();
            return true;
        }catch (Exception $e){
            $connection->rollBack();
            zend_debug::dump($e->getMessage());
            Mage::log($e->getMessage(),null,'log.log');
        }
    }

    /**
     * @param $table
     * @param $updateValues
     * @param $where
     * @return bool
     */
    public function updateOne($table,$updateValues,$where){
        try{
            if($updateValues == null)return true;
            $cr = Mage::getSingleton('core/resource');
            $connection = $cr->getConnection('core_write');
            $connection->beginTransaction();
            //$connection->update($table,$updateValues,$connection->quoteInto($where));
            $connection->update($table,$updateValues,$where);
            $connection->commit();
            return true;
        }catch (Exception $e){
            $connection->rollBack();
            zend_debug::dump($e->getMessage());
            Mage::log($e->getMessage(),null,'log.log');
        }
    }
}