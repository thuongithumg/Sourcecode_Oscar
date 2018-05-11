<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Api\Synchronization;

/**
 * Interface SynchronizationInterface
 * @package Magestore\Webpos\Api\Synchronization
 */
interface SynchronizationInterface
{
    /**
     * Check enable config use index table to synchronization
     *
     * @param string
     * @return boolean
     */
    public function isUseIndexTable();

    /**
     * Return valid table name with store id
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTableName($storeId = null);

    /**
     * @param string $name
     * @return bool
     */
    public function isTableExists($name);

    /**
     * @param string $name
     */
    public function removeTable($name);

    /**
     * @param null|int|string $storeId
     */
    public function createIndexTable($storeId = null);
    
    /**
     * Get data from source table and update to index table
     *
     * @param int|null $storeId
     * @return $this
     */
    public function addIndexTableData($storeId = null);

    /**
     * @param array $data
     * @param null|int|string $storeId
     */
    public function importDataToTmpTable($data, $storeId = null);

    /**
     * @param null|int|string $storeId
     * @return bool|string
     */
    public function getAllDataFromTable($storeId = null);

    /**
     * @param int $curPage
     * @param int $pageSize
     * @param null|int|string $storeId
     * @return array
     */
    public function getDataFromTmpTable($curPage, $pageSize, $storeId = null);

    /**
     * @param null|int|string $storeId
     * @return int|void
     */
    public function getTotalItemTmpTable($storeId = null);

    /**
     * @param array $data
     * @return array
     */
    public function prepareGetData($data);
}