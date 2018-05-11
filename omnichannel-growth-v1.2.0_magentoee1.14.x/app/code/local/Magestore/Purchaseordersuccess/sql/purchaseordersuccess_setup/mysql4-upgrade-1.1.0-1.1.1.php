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

/* remove store_id from os_warehouse */
$connection = $installer->getConnection();
if(!$connection->tableColumnExists($installer->getTable('os_purchase_order'), 'purchase_key')) {
    $connection->addColumn($installer->getTable('os_purchase_order'),'purchase_key', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'comment'   => 'purchase_key'
    ));
}

$installer->getConnection()->dropTable($installer->getTable('os_return_order'));
$installer->getConnection()->dropTable($installer->getTable('os_return_order_item'));
$installer->getConnection()->dropTable($installer->getTable('os_return_order_item_transferred'));

$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_return_order'))
    ->addColumn(
        'return_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Return Id'
    )->addColumn(
        'return_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['default' => ''],
        'Return Code'
    )->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['unsigned' => true, 'nullable' => false],
        'Warehouse Id'
    )->addColumn(
        'supplier_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['unsigned' => true, 'nullable' => false],
        'Supplier Id'
    )->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        ['default' => 1],
        'Type'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        ['default' => 0],
        'Status'
    )->addColumn(
        'reason',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        ['default' => null],
        'Reason'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['default' => ''],
        'Created By'
    )->addColumn(
        'user_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['unsigned' => true, 'nullable' => false],
        'User Id'
    )->addColumn(
        'total_qty_returned',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        ['nullable' => false, 'unsigned' => true, 'default' => 0],
        'Total Qty Returned'
    )->addColumn(
        'total_qty_transferred',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        ['nullable' => false, 'unsigned' => true, 'default' => 0],
        'Total Qty Transferred'
    )->addColumn(
        'returned_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        ['default' => null],
        'Return Date'
    )->addColumn(
        'canceled_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        ['default' => null],
        'Canceled Date'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT],
        'Created At'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE],
        'Updated At'
    )->addForeignKey(
        $installer->getFkName(
            'os_return_order',
            'warehouse_id',
            'os_warehouse',
            'warehouse_id'
        ),
        'warehouse_id',
        $installer->getTable('os_warehouse'),
        'warehouse_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'os_return_order',
            'supplier_id',
            'os_supplier',
            'supplier_id'
        ),
        'supplier_id',
        $installer->getTable('os_supplier'),
        'supplier_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'os_return_order',
            'user_id',
            'admin_user',
            'user_id'
        ),
        'user_id',
        $installer->getTable('admin_user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_return_order_item'))
    ->addColumn(
        'return_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Return Item Id'
    )->addColumn(
        'return_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['unsigned' => true, 'nullable' => false],
        'Return Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['unsigned' => true, 'nullable' => false],
        'Product Id'
    )->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['default' => null],
        'Product SKU'
    )->addColumn(
        'product_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['default' => null],
        'Product Name'
    )->addColumn(
        'product_supplier_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['default' => null],
        'Product Supplier SKU'
    )->addColumn(
        'qty_returned',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        ['default' => 0],
        'Returned Qty'
    )->addColumn(
        'qty_transferred',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        ['default' => 0],
        'Transferred Qty'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT],
        'Created At'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE],
        'Updated At'
    )->addIndex(
        $installer->getIdxName('os_return_order_item', 'return_id'),
        'return_id'
    )->addIndex(
        $installer->getIdxName('os_return_order_item', 'product_id'),
        'product_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_return_order_item',
            'return_id',
            'os_return_order',
            'return_id'
        ),
        'return_id',
        $installer->getTable('os_return_order'),
        'return_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'os_return_order_item',
            'product_id',
            'catalog_product_entity',
            'entity_id'
        ),
        'product_id',
        $installer->getTable('catalog_product_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_return_order_item_transferred'))
    ->addColumn(
        'return_item_transferred_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Return Request Item Transferred Id'
    )->addColumn(
        'return_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['unsigned' => true, 'nullable' => false],
        'Return Request Item Id'
    )->addColumn(
        'qty_transferred',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        ['default' => 0],
        'Transferred Qty'
//            )->addColumn(
//                'warehouse_id',
//                Varien_Db_Ddl_Table::TYPE_INTEGER,
//                '12,4',
//                ['nullable' => false, 'unsigned' => true],
//                'Warehouse Id'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['default' => ''],
        'Created By'
    )->addColumn(
        'transferred_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        ['nullable' => false],
        'Transferred At'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT],
        'Created At'
    )->addIndex(
        $installer->getIdxName('os_return_order_item_transferred', 'return_item_id'),
        'return_item_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_return_order_item_transferred',
            'return_item_id',
            'os_return_order_item',
            'return_item_id'
        ),
        'return_item_id',
        $installer->getTable('os_return_order_item'),
        'return_item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();
