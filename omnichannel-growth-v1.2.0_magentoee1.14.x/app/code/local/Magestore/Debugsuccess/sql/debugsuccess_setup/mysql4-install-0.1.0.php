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
 * @category    Magestore
 * @package     Magestore_ReportSuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('os_debug_movement'));
$installer->getConnection()->dropTable($installer->getTable('os_debug_pending_orders_items'));

/**
 * Create table 'os_debug_pending_orders_items'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_debug_pending_orders_items'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'ID'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )
    ->addColumn(
        'on_hold_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'On Hold Qty'
    )
    ->addIndex(
        $installer->getIdxName('os_debug_pending_orders_items', array('product_id')),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName('os_debug_pending_orders_items', array('on_hold_qty')),
        array('on_hold_qty')
    )
    ->setComment('Report Debug Success On Hold');
$installer->getConnection()->createTable($table);



/**
 * Create table 'os_debug_movement'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_debug_movement'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'ID'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )
    ->addColumn(
        'sku',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array('default' => null),
        'SKU'
    )
    ->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Warehouse Id'
    )
    ->addColumn(
        'old_total_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Old Total Qty'
    )
    ->addColumn(
        'old_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Old Qty'
    )
    ->addColumn(
        'total_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Total Qty'
    )
    ->addColumn(
        'qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Qty'
    )
    ->addColumn(
        'reason',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array('default' => null),
        'Reason'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Updated At'
    )
    ->addIndex(
        $installer->getIdxName('os_debug_movement', array('product_id')),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName('os_debug_movement', array('warehouse_id')),
        array('warehouse_id')
    )
    ->addIndex(
        $installer->getIdxName('os_debug_movement', array('updated_at')),
        array('updated_at')
    )
    ->setComment('Report Debug Success');
$installer->getConnection()->createTable($table);

$installer->endSetup();

