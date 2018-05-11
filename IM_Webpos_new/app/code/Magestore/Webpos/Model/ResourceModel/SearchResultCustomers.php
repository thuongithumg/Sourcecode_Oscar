<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SearchResultCustomers extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('os_search_result_customers_index','entity_id');
    }

    /**
     * @param $tableName
     * @param $select
     * @param bool|false $temporary
     */
    public function createTable($tableName,$select,$temporary = false){
        $connection = $this->_getConnection('read');
        $this->createTableFromSelect($tableName,$select,$temporary);
    }

    /**
     * @param $tableName
     * @param Zend_Db_Select $select
     * @param bool $temporary
     */
    public function createTableFromSelect($tableName, $select, $temporary = false)
    {
        $connection = $this->_getConnection('read');
        $query = sprintf(
            'CREATE' . ($temporary ? ' TEMPORARY' : '') . ' TABLE `%s` AS (%s)',
            $tableName,
            (string)$select
        );
        $connection->query($query);
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
}
