<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 02/04/2017
 * Time: 21:40
 */


class Magestore_Debugsuccess_Model_Mysql4_Wrongqty extends
    Mage_Core_Model_Mysql4_Abstract
{
    protected $_correctService;

    /**
     * contruct
     */
    public function _construct()
    {
        $this->_correctService = Magestore_Debugsuccess_Model_Service_Debug_DebugService::correctService();
        $this->_init('debugsuccess/wrongqty', Magestore_Debugsuccess_Model_Wrongqty::ID);
    }

    /**
     * @param $table
     */
    public function deleteData($table){
        try {
            $connection = $this->_getConnection('read');
            $connection->truncate($table);
        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @param $table
     * @param $data
     */
    public function insertData($table, $data){
        try{
            $connection = $this->_getConnection('read');
            $connection->beginTransaction();
            $connection->insertMultiple($table,$data);
            $connection->commit();
        }catch (Exception $e){
            $connection->rollBack();
        }
    }

    /**
     * @param $tableName
     * @param $select
     * @param bool|false $temporary
     */
    public function createTable($tableName,$select,$temporary = false){
        $connection = $this->_getConnection('read');
        $connection->createTableFromSelect($tableName,$select,$temporary);
    }

    /**
     * @param $tableName
     * @param null $temporary
     */
    public function dropTable($tableName, $temporary = null)
    {
        $connection = $this->_getConnection('read');
        if($temporary){
            $connection->dropTemporaryTable($tableName);
        }else{
            $connection->dropTable($tableName);
        }
    }

    /**
     * @param $data
     * @return bool
     */
    public function updateTable($data){
        $tableinsert = Mage::getSingleton('core/resource')->getTableName('os_debug_movement');
        $table = $data['table'];
        $bin_data = $data['values'];
        $where = $data['condition'];
        $insert = $data['insert'];
        try{
            $connection = $this->_getConnection('read');
            $connection->beginTransaction();
            if($bin_data)
                $connection->update($table,$bin_data,$where);
            if($insert)
                $connection->insertMultiple($tableinsert,$insert);
            $connection->commit();
            return true;
        }catch (Exception $e){
            $connection->rollBack();
        }
    }

    /**
     * @param $product
     * @param $warehouse
     * @return mixed
     */
    public function correctWrongQty($product,$warehouse){
       return $this->_correctService->correctQty($product,$warehouse);
    }

}