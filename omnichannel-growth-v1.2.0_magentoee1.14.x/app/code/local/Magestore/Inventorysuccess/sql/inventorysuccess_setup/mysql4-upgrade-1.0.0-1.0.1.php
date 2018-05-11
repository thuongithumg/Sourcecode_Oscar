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

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('os_warehouse_store'));

/**
 * create os_warehouse_store table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_warehouse_store'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Id'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        6,
        array('unsigned' => true, 'nullable' => false),
        'Store Id'
    )
    ->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Warehouse Id'
    )->addIndex(
        $installer->getIdxName('os_warehouse_store', array('warehouse_id')),
        array('warehouse_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_warehouse_store',
            'warehouse_id',
            'os_warehouse',
            'warehouse_id'
        ),
        'warehouse_id',
        $installer->getTable('os_warehouse'),
        'warehouse_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addIndex(
        $installer->getIdxName('os_warehouse_store', array('store_id')),
        array('store_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_warehouse_store',
            'store_id',
            'core_store',
            'store_id'
        ),
        'store_id',
        $installer->getTable('core_store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addIndex(
        $installer->getIdxName(
            'os_warehouse_store',
            array('warehouse_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('warehouse_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    );

$installer->getConnection()->createTable($table);

/* remove store_id from os_warehouse */
$connection = $installer->getConnection();
if($connection->tableColumnExists($this->getTable('os_warehouse'), 'store_id')) {
    
    Magestore_Coresuccess_Model_Service::warehouseStoreService()->transferStoreIdFromWarehouseTable();
    
    $connection->dropColumn(
        $this->getTable('os_warehouse'),
        'store_id'
    );
}

$installer->endSetup();
