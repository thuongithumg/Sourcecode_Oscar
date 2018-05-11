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

$connection = $installer->getConnection();

/* add warehouse_id to sales_flat_order */
if(!$connection->tableColumnExists($installer->getTable('sales_flat_order'), 'warehouse_id')) {
    $connection->addColumn($installer->getTable('sales_flat_order'),'warehouse_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => true,
        'length'    => '11',
        'unsigned' => true,
        'comment'   => 'Warehouse Id'
    ));
}
/* add warehouse_id to sales_flat_order_grid */
if(!$connection->tableColumnExists($installer->getTable('sales_flat_order_grid'), 'warehouse_id')) {
    $connection->addColumn($installer->getTable('sales_flat_order_grid'),'warehouse_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => true,
        'length'    => '11',
        'unsigned' => true,
        'comment'   => 'Warehouse Id'
    ));
}

/** create os_stock_transfer table */
if(!$connection->isTableExists($installer->getTable('os_stock_transfer'))) {
    $table = $connection
        ->newTable($installer->getTable('os_stock_transfer'))
        ->addColumn(
            'stock_transfer_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
            'Stock Transfer Id'
        )->addColumn(
            'transfer_code',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array('default' => null),
            'Transfer Code'
        )->addColumn(
            'qty',
            Varien_Db_Ddl_Table::TYPE_DECIMAL,
            '12,4',
            array('default' => 0),
            'Total Item'
        )->addColumn(
            'total_sku',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array('default' => 0),
            'Total SKU'
        )->addColumn(
            'action_code',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array('default' => null),
            'Action Code'
        )->addColumn(
            'action_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array('default' => null),
            'Action Id'
        )->addColumn(
            'action_number',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array('default' => ''),
            'Reference Number'
        )->addColumn(
            'warehouse_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array('nullable' => true, 'default' => null),
            'Warehouse Id'
        )->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
            'Created At'
        );

    $connection->createTable($table);
}

/** create os_stock_transfer table */
if (!$connection->tableColumnExists($installer->getTable('os_stock_movement'), 'stock_transfer_id')) {
    $connection->addColumn($installer->getTable('os_stock_movement'), 'stock_transfer_id', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'length' => 11,
            'unsigned' => true,
            'comment' => 'Stock Tranfer ID'
        )
    );
}

$installer->endSetup();


