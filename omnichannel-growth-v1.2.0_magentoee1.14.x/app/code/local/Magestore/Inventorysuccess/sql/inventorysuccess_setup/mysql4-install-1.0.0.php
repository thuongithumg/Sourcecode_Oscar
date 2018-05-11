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


$installer->getConnection()->dropTable($installer->getTable('os_warehouse'));
$installer->getConnection()->dropTable($installer->getTable('os_warehouse_product'));
$installer->getConnection()->dropTable($installer->getTable('os_warehouse_location'));
$installer->getConnection()->dropTable($installer->getTable('os_permission'));
$installer->getConnection()->dropTable($installer->getTable('os_stock_movement'));
$installer->getConnection()->dropTable($installer->getTable('os_adjuststock'));
$installer->getConnection()->dropTable($installer->getTable('os_adjuststock_product'));
$installer->getConnection()->dropTable($installer->getTable('os_stocktaking'));
$installer->getConnection()->dropTable($installer->getTable('os_stocktaking_product'));
$installer->getConnection()->dropTable($installer->getTable('os_transferstock'));
$installer->getConnection()->dropTable($installer->getTable('os_transferstock_product'));
$installer->getConnection()->dropTable($installer->getTable('os_transferstock_product'));
$installer->getConnection()->dropTable($installer->getTable('os_transferstock_activity'));
$installer->getConnection()->dropTable($installer->getTable('os_transferstock_activity_product'));

$installer->getConnection()->dropTable($installer->getTable('os_warehouse_order_item'));
$installer->getConnection()->dropTable($installer->getTable('os_warehouse_shipment_item'));
$installer->getConnection()->dropTable($installer->getTable('os_warehouse_creditmemo_item'));

$installer->getConnection()->dropTable($installer->getTable('os_increment_id'));

$installer->getConnection()->dropTable($installer->getTable('os_lowstock_notification_rule'));
$installer->getConnection()->dropTable($installer->getTable('os_lowstock_notification_rule_product'));
$installer->getConnection()->dropTable($installer->getTable('os_lowstock_notification'));
$installer->getConnection()->dropTable($installer->getTable('os_lowstock_notification_product'));

$installer->getConnection()->dropTable($installer->getTable('os_installation'));

$connection = $installer->getConnection();

/**
 * create os_warehouse table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_warehouse'))
    ->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Warehouse Id'
    )->addColumn(
        'warehouse_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Warehouse Name'
    )->addColumn(
        'warehouse_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Warehouse Code'
    )->addColumn(
        'contact_email',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Contact Email'
    )->addColumn(
        'telephone',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        50,
        array('default' => null),
        'Telephone'
    )->addColumn(
        'street',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Street'
    )->addColumn(
        'city',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'City'
    )->addColumn(
        'country_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        3,
        array('default' => null),
        'Country Id'
    )->addColumn(
        'region',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Region'
    )->addColumn(
        'region_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Region Id'
    )->addColumn(
        'postcode',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Postcode'
    )
    ->addColumn(
        'is_primary',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => '0'),
        'Is Primary'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        //array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE),
        array(),
        'Updated At'
    )->addIndex(
        $installer->getIdxName(
            'os_warehouse',
            array('warehouse_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('warehouse_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    );
$installer->getConnection()->createTable($table);

/**
 * create os_warehouse_location table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_warehouse_location'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Id'
    )
    ->addColumn(
        'location_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Location Id'
    )
    ->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Warehouse Id'
    )->addIndex(
        $installer->getIdxName('os_warehouse_location', array('warehouse_id')),
        array('warehouse_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_warehouse_location',
            'warehouse_id',
            'os_warehouse',
            'warehouse_id'
        ),
        'warehouse_id',
        $installer->getTable('os_warehouse'),
        'warehouse_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create os_warehouse_product table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_warehouse_product'))
    ->addColumn(
        'warehouse_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Warehouse Product Id'
    )->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Warehouse Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'total_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Total Qty'
    )->addColumn(
        'qty_to_ship',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Qty to Ship'
    )->addColumn(
        'shelf_location',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Shelf Location'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        //array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE),
        array(),
        'Updated At'
    )->addIndex(
        $installer->getIdxName('os_warehouse_product', array('warehouse_id')),
        array('warehouse_id')
    )->addIndex(
        $installer->getIdxName('os_warehouse_product', array('product_id')),
        array('product_id')
    )->addIndex(
        $installer->getIdxName(
            'os_warehouse_product',
            array('warehouse_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('warehouse_id', 'product_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->addForeignKey(
        $installer->getFkName(
            'os_warehouse_product',
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
            'os_warehouse_product',
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

/**
 * create os_stock_movement table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_stock_movement'))
    ->addColumn(
        'stock_movement_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Stock Movement Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'product Id'
    )->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product SKU'
    )->addColumn(
        'qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty'
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
        'Action Number'
    )->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('nullable' => true, 'default' => null),
        'Warehouse Id'
    )
//            ->addColumn(
//                'source_warehouse',
//                Varien_Db_Ddl_Table::TYPE_TEXT,
//                255,
//                ['default' => ''],
//                'Source Warehouse'
//            )->addColumn(
//                'des_warehouse_id',
//                Varien_Db_Ddl_Table::TYPE_INTEGER,
//                11,
//                ['nullable' => true, 'default' => null],
//                'Destination Warehouse Id'
//            )->addColumn(
//                'des_warehouse',
//                Varien_Db_Ddl_Table::TYPE_TEXT,
//                255,
//                ['default' => ''],
//                'Destination Warehouse'
//            )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addIndex(
        $installer->getIdxName('os_stock_movement', array('product_id')),
        array('product_id')
    )->addIndex(
        $installer->getIdxName('os_stock_movement', array('product_sku')),
        array('product_sku')
    );
$installer->getConnection()->createTable($table);

/**
 * create os_adjuststock table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_adjuststock'))
    ->addColumn(
        'adjuststock_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Adjuststock Id'
    )->addColumn(
        'adjuststock_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Adjuststock Code'
    )
    ->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Warehouse Id'
    )->addColumn(
        'warehouse_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Warehouse Name'
    )->addColumn(
        'warehouse_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Warehouse Code'
    )->addColumn(
        'reason',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Reason'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Created By'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Created At'
    )->addColumn(
        'confirmed_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Confirmed By'
    )->addColumn(
        'confirmed_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Confirmed At'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => null),
        'Status'
    )->addIndex(
        $installer->getIdxName(
            'os_adjuststock_adjuststock_code',
            array('adjuststock_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('adjuststock_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->addIndex(
        $installer->getIdxName('os_adjuststock', array('warehouse_id')),
        array('warehouse_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_adjuststock',
            'warehouse_id',
            'os_warehouse',
            'warehouse_id'
        ),
        'warehouse_id',
        $installer->getTable('os_warehouse'),
        'warehouse_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create  os_adjuststock_product table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_adjuststock_product'))
    ->addColumn(
        'adjuststock_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Adjuststock Product Id'
    )
    ->addColumn(
        'adjuststock_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Adjuststock Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'product_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product Name'
    )->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product SKU'
    )->addColumn(
        'old_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Old Qty'
    )->addColumn(
        'adjust_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Adjust Qty'
    )->addColumn(
        'change_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Change Qty'
    )->addIndex(
        $installer->getIdxName('os_adjuststock_product', array('adjuststock_id')),
        array('adjuststock_id')
    )->addIndex(
        $installer->getIdxName('os_adjuststock_product', array('product_id')),
        array('product_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_adjuststock_product',
            'adjuststock_id',
            'os_adjuststock',
            'adjuststock_id'
        ),
        'adjuststock_id',
        $installer->getTable('os_adjuststock'),
        'adjuststock_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'os_adjuststock_product',
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

/**
 * create stocktaking table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_stocktaking'))
    ->addColumn(
        'stocktaking_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Stocktaking Id'
    )->addColumn(
        'stocktaking_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Stocktaking Code'
    )
    ->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Warehouse Id'
    )->addColumn(
        'warehouse_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Warehouse Name'
    )->addColumn(
        'warehouse_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Warehouse Code'
    )->addColumn(
        'participants',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Participants'
    )->addColumn(
        'stocktake_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Stocktake At'
    )->addColumn(
        'reason',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Reason'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Created By'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Created At'
    )->addColumn(
        'verified_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Verified By'
    )->addColumn(
        'verified_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Verified At'
    )->addColumn(
        'confirmed_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Confirmed By'
    )->addColumn(
        'confirmed_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Confirmed At'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => null),
        'Status'
    )->addIndex(
        $installer->getIdxName(
            'os_stocktaking_stocktaking_code',
            array('stocktaking_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('stocktaking_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->addIndex(
        $installer->getIdxName('os_stocktaking', array('warehouse_id')),
        array('warehouse_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_stocktaking',
            'warehouse_id',
            'os_warehouse',
            'warehouse_id'
        ),
        'warehouse_id',
        $installer->getTable('os_warehouse'),
        'warehouse_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create  os_stocktaking_product table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_stocktaking_product'))
    ->addColumn(
        'stocktaking_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Stocktaking Product Id'
    )
    ->addColumn(
        'stocktaking_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Stocktaking Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'product_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product Name'
    )->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product SKU'
    )->addColumn(
        'old_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Old Qty'
    )->addColumn(
        'stocktaking_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => true),
        'Stocktaking Qty'
    )->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => null),
        'Type'
    )->addIndex(
        $installer->getIdxName('os_stocktaking_product', array('stocktaking_id')),
        array('stocktaking_id')
    )->addIndex(
        $installer->getIdxName('os_stocktaking_product', array('product_id')),
        array('product_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_stocktaking_product',
            'stocktaking_id',
            'os_stocktaking',
            'stocktaking_id'
        ),
        'stocktaking_id',
        $installer->getTable('os_stocktaking'),
        'stocktaking_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'os_stocktaking_product',
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
/**
 * create os_transferstock table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_transferstock'))
    ->addColumn(
        'transferstock_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Transfer Stock Id'
    )->addColumn(
        'transferstock_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Transfer Stock Code'
    )
    ->addColumn(
        'source_warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Soruce Warehouse Id'
    )->addColumn(
        'source_warehouse_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Source Warehouse Code'
    )->addColumn(
        'des_warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Destination Warehouse Id'
    )->addColumn(
        'des_warehouse_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Destination Warehouse Code'
    )->addColumn(
        'reason',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Reason'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Created By'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('default' => null),
        'Created At'
    )->addColumn(
        'external_location',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'External Location'
    )->addColumn(
        'notifier_emails',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Notification recipients'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Status'
    )->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Transfer Type'
    )->addColumn(
        'shipping_info',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Shipping Information'
    )->addColumn(
        'qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Transfer Qty'
    )->addColumn(
        'qty_delivered',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty Delivered'
    )->addColumn(
        'qty_received',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty Received'
    );
$installer->getConnection()->createTable($table);

/**
 * create  os_transferstock_product table
 */

$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_transferstock_product'))
    ->addColumn(
        'transferstock_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Transfer Stock Product Id'
    )
    ->addColumn(
        'transferstock_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Transfer Stock Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'product_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product Name'
    )->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product SKU'
    )->addColumn(
        'qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Transfer Qty'
    )->addColumn(
        'qty_delivered',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty Delivered'
    )->addColumn(
        'qty_received',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty Received'
    )->addColumn(
        'transfer_type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Transfer Stock Type'
    );
$installer->getConnection()->createTable($table);


/**
 * create os_transferstock_activity table
 */

$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_transferstock_activity'))
    ->addColumn(
        'activity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Activity Id'
    )->addColumn(
        'transferstock_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Transfer Stock ID'
    )->addColumn(
        'note',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Note'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Created By'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('default' => null),
        'Created At'
    )->addColumn(
        'activity_type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Activity Type'
    )->addColumn(
        'total_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Total Qty'
    );
$installer->getConnection()->createTable($table);

/**
 * create  os_transferstock_activity_product table
 */

$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_transferstock_activity_product'))
    ->addColumn(
        'activity_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Activity Product Id'
    )
    ->addColumn(
        'activity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Activity Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'product_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product Name'
    )->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product SKU'
    )->addColumn(
        'qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Transfer Qty'
    );
$installer->getConnection()->createTable($table);

/**
 * Create table 'os_permission'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_permission'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'History ID'
    )
    ->addColumn(
        'user_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'user_id'
    )
    ->addColumn(
        'object_type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        5,
        array(),
        'Object Type'
    )
    ->addColumn(
        'object_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Object Id'
    )
    ->addColumn(
        'role_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Role Id'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )
    ->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Created By'
    )
    ->addIndex(
        $installer->getIdxName('os_permission', array('id')),
        array('id')
    )
    ->addIndex(
        $installer->getIdxName('os_permission', array('role_id')),
        array('role_id')
    )
    ->addIndex(
        $installer->getIdxName('os_permission', array('user_id')),
        array('user_id')
    )
    ->addForeignKey(
        $installer->getFkName(
            'os_permission',
            'role_id',
            'admin_role',
            'role_id'
        ),
        'role_id',
        $installer->getTable('admin_role'),
        'role_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'os_permission',
            'user_id',
            'admin_user',
            'user_id'
        ),
        'user_id',
        $installer->getTable('admin_user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('OS Permission Table');
$installer->getConnection()->createTable($table);

/**
 * create os_warehouse_order_item table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_warehouse_order_item'))
    ->addColumn(
        'warehouse_order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Warehouse Sales Item Id'
    )->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Warehouse Id'
    )->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Sales Sales Id'
    )->addColumn(
        'item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Sales Item Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'qty_ordered',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty Ordered'
    )->addColumn(
        'qty_canceled',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty Canceled'
    )->addColumn(
        'subtotal',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Subtotal of order item'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        //array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE),
        array(),
        'Updated At'
    )->addIndex(
        $installer->getIdxName('os_warehouse_order_item', array('warehouse_order_item_id')),
        array('warehouse_order_item_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_warehouse_order_item',
            'item_id',
            'sales_flat_order_item',
            'item_id'
        ),
        'item_id',
        $installer->getTable('sales/order_item'),
        'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create os_warehouse_shipment_item table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_warehouse_shipment_item'))
    ->addColumn(
        'warehouse_shipment_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Warehouse Shipment Item Id'
    )->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Warehouse Id'
    )->addColumn(
        'shipment_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Shipment Id'
    )->addColumn(
        'item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Shipment Item Id'
    )->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Sales Id'
    )->addColumn(
        'order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Sales Item Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'qty_shipped',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty Shipped'
    )->addColumn(
        'subtotal',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Subtotal of shipment item'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        //array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE),
        array(),
        'Updated At'
    )->addIndex(
        $installer->getIdxName('os_warehouse_shipment_item', array('warehouse_shipment_item_id')),
        array('warehouse_shipment_item_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_warehouse_shipment_item',
            'item_id',
            'sales_shipment_item',
            'entity_id'
        ),
        'item_id',
        $installer->getTable('sales/shipment_item'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);


/**
 * create os_warehouse_creditmemo_item table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_warehouse_creditmemo_item'))
    ->addColumn(
        'warehouse_creditmemo_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Warehouse Creditmemo Item Id'
    )->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Warehouse Id'
    )->addColumn(
        'creditmemo_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Creditmemo Id'
    )->addColumn(
        'item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Creditmeno Item Id'
    )->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Sales Id'
    )->addColumn(
        'order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Sales Item Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'qty_refunded',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Qty Refunded'
    )->addColumn(
        'subtotal',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Subtotal of creditmemo item'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        //array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE),
        array(),
        'Updated At'
    )->addIndex(
        $installer->getIdxName('os_warehouse_creditmemo_item', array('warehouse_creditmemo_item_id')),
        array('warehouse_creditmemo_item_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_warehouse_creditmemo_item',
            'item_id',
            'sales_creditmemo_item',
            'entity_id'
        ),
        'item_id',
        $installer->getTable('sales/creditmemo_item'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);


/**
 * create os_lowstock_notification_rule table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_lowstock_notification_rule'))
    ->addColumn(
        'rule_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Low Stock Notification Rule Id'
    )->addColumn(
        'rule_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Description'
    )->addColumn(
        'description',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Description'
    )->addColumn(
        'from_date',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('default' => null),
        'Start use rule'
    )->addColumn(
        'to_date',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('default' => null),
        'End rule'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 0),
        'Status'
    )->addColumn(
        'conditions_serialized',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Conditions of rule'
    )->addColumn(
        'priority',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null),
        'Priority'
    )->addColumn(
        'update_time_type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => null),
        'Update time type'
    )->addColumn(
        'specific_time',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => '00'),
        'Hours to check rule'
    )->addColumn(
        'specific_day',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => '1'),
        'Days to check rule'
    )->addColumn(
        'specific_month',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => '1'),
        'Months to check rule'
    )->addColumn(
        'lowstock_threshold_type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 1),
        'Low stock threshold type'
    )->addColumn(
        'lowstock_threshold_qty',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0),
        'Low stock threshold by product Qty'
    )->addColumn(
        'lowstock_threshold',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0),
        'Low stock threshold by sale days'
    )->addColumn(
        'sales_period',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0),
        'Sales Period'
    )->addColumn(
        'update_type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => null),
        'Update type'
    )->addColumn(
        'warehouse_ids',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Warehouse ids'
    )->addColumn(
        'notifier_emails',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'List emails to notify'
    )->addColumn(
        'warning_message',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Message to notify'
    )->addColumn(
        'next_time_action',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('default' => null),
        'Next time to do action'
    )->addColumn(
        'apply',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 0),
        'Apply rule'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('default' => null),
        'Updated At'
    );
$installer->getConnection()->createTable($table);

/**
 * create os_lowstock_notification_rule_product table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_lowstock_notification_rule_product'))
    ->addColumn(
        'rule_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Low Stock Notification Rule Product Id'
    )->addColumn(
        'rule_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 1, 'unsigned' => true),
        'Rule Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 1, 'unsigned' => true),
        'Product Id'
    )->addIndex(
        $installer->getIdxName('os_lowstock_notification_rule_product', array('rule_id')),
        array('rule_id')
    )->addIndex(
        $installer->getIdxName('os_lowstock_notification_rule_product', array('product_id')),
        array('product_id')
    )->addForeignKey(
        $installer->getFkName(
            'os_lowstock_notification_rule_product',
            'rule_id',
            'os_lowstock_notification_rule',
            'rule_id'
        ),
        'rule_id',
        $installer->getTable('os_lowstock_notification_rule'),
        'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'os_lowstock_notification_rule_product',
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

/**
 * create os_lowstock_notification table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_lowstock_notification'))
    ->addColumn(
        'notification_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Low Stock Notification Id'
    )->addColumn(
        'rule_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 1, 'unsigned' => true),
        'Rule Id'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addColumn(
        'update_type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => null),
        'Update type'
    )->addColumn(
        'notifier_emails',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'List emails to notify'
    )->addColumn(
        'lowstock_threshold_type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 1),
        'Low stock threshold type'
    )->addColumn(
        'lowstock_threshold_qty',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0),
        'Low stock threshold by product Qty'
    )->addColumn(
        'lowstock_threshold',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0),
        'Low stock threshold by sale days'
    )->addColumn(
        'sales_period',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0),
        'Sales Period'
    )->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null),
        'Warehouse id'
    )->addColumn(
        'warehouse_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Warehouse name'
    )->addColumn(
        'warning_message',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Message to notify'
    );
$installer->getConnection()->createTable($table);

/**
 * create os_lowstock_notification_product table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_lowstock_notification_product'))
    ->addColumn(
        'notification_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Low Stock Notification Product Id'
    )->addColumn(
        'notification_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 1, 'unsigned' => true),
        'Low Stock Notification Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Product Id'
    )->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product Sku'
    )->addColumn(
        'product_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product Name'
    )->addColumn(
        'current_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        "12,4",
        array('default' => 0),
        'Current product qty'
    )->addColumn(
        'sold_per_day',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        "12,4",
        array('default' => 0),
        'Qty sold per day'
    )->addColumn(
        'total_sold',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        "12,4",
        array('default' => 0),
        'Total qty sold'
    )->addColumn(
        'availability_days',
        Varien_Db_Ddl_Table::TYPE_FLOAT,
        null,
        array('default' => 0),
        'Number days that product is available to sell'
    )->addColumn(
        'availability_date',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'The date that product is available to sell'
    );
$installer->getConnection()->createTable($table);

/**
 * create os_increment_id table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_increment_id'))
    ->addColumn(
        'increment_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Increment Id'
    )->addColumn(
        'code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('unique' => true, 'nullable' => false),
        'Entity Type Code'
    )->addColumn(
        'current_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 1, 'unsigned' => true),
        'Current Id'
    )->addIndex(
        $installer->getIdxName('os_increment_id', array('increment_id')),
        array('increment_id')
    );
$installer->getConnection()->createTable($table);

/**
 * create os_installation table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_installation'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Increment Id'
    )->addColumn(
        'step',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('unique' => true, 'nullable' => false),
        'Entity Type Code'
    )->addColumn(
        'current_index',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0, 'unsigned' => true),
        'Current Index'
    )->addColumn(
        'total',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0, 'unsigned' => true),
        'Total Items'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 0),
        'Status'
    )->addIndex(
        $installer->getIdxName('os_installation_id', array('id')),
        array('id')
    );
$installer->getConnection()->createTable($table);


/* add total_qty to cataloginventory_stock_item */
if(!$connection->tableColumnExists($this->getTable('cataloginventory_stock_item'), 'total_qty')) {
    $connection->addColumn(
        $this->getTable('cataloginventory_stock_item'),
        'total_qty',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Total Qty',
            'after' => 'qty'
        )
    );
}

/* add shelf_location to cataloginventory_stock_item */
if(!$connection->tableColumnExists($this->getTable('cataloginventory_stock_item'), 'shelf_location')) {
    $connection->addColumn(
        $this->getTable('cataloginventory_stock_item'),
        'shelf_location',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'comment' => 'Shelf Location',
            'after' => 'total_qty'
        )
    );
}

/* add store_id to os_warehouse */
if(!$connection->tableColumnExists($this->getTable('os_warehouse'), 'store_id')) {
    $connection->addColumn(
        $this->getTable('os_warehouse'),
        'store_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length' => 6,
            'comment' => 'Store ID'
        )
    );
}

/* setting-up initial data */
$installationService = Magestore_Coresuccess_Model_Service::installService();
$installationService->removeFirstWarehouseId();
$installationService->createDefaultLowStockNotificationRule();
    
$installer->endSetup();

