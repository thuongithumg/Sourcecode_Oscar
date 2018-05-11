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
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('reportsuccess/costofgood'));
$installer->getConnection()->dropTable($installer->getTable('reportsuccess/historics'));
$installer->getConnection()->dropTable($installer->getTable('reportsuccess/editcolumns'));
$installer->getConnection()->dropTable($installer->getTable('reportsuccess/flagtag'));
$installer->getConnection()->dropTable($installer->getTable('reportsuccess/salesreport'));

$check_column = $installer->getConnection()->tableColumnExists($installer->getTable('sales/order_item'),'os_mac');
if($check_column){
    $installer->getConnection()->dropColumn($installer->getTable('sales/order_item'),'os_mac');
}



/**
 * Create table 'os_report_flagtag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('reportsuccess/flagtag'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'ID'
    )
    ->addColumn(
        'report_type',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array('default' => null),
        'Report Type'
    )
    ->addColumn(
        'start_count',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Start'
    )
    ->addColumn(
        'end_count',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'End'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'updated_at'
    )
    ->setComment('Flag tag');
$installer->getConnection()->createTable($table);

/**
 * Create table 'os_report_editcolumns'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('reportsuccess/editcolumns'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'ID'
    )
    ->addColumn(
        'grid',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array('default' => null),
        'Grid Id'
    )
    ->addColumn(
        'value',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Value'
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/editcolumns', array('id')),
        array('id')
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/editcolumns', array('grid')),
        array('grid')
    )
    ->setComment('Edit Columns');
$installer->getConnection()->createTable($table);

/**
 * Create table 'os_report_cog'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('reportsuccess/costofgood'))
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
        'mac',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'MAC'
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/costofgood', array('id')),
        array('id')
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/costofgood', array('product_id')),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/costofgood', array('mac')),
        array('mac')
    )
    ->addForeignKey(
        $installer->getFkName(
            'reportsuccess/costofgood',
            'product_id',
            'catalog/product',
            'entity_id'
        ),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Report Cost Of Good Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'os_report_historics'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('reportsuccess/historics'))
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
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Warehouse Id'
    )
    ->addColumn(
        'total_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Total Qty'
    )
    ->addColumn(
        'mac',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'MAC'
    )
    ->addColumn(
        'inv_value',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Inventory Value'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Updated At'
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/historics', array('product_id')),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/historics', array('warehouse_id')),
        array('warehouse_id')
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/historics', array('updated_at')),
        array('updated_at')
    )
    ->setComment('Report Historics Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'os_salesreport_report'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('reportsuccess/salesreport'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'ID'
    )->addColumn(
        'item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'item_id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'product_id'
    )
    ->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'order_id'
    )

    ->addColumn(
        'increment_id',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array('default' => '', 'unsigned' => false),
        'increment_id'
    )

    ->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'warehouse_id'
    )->addColumn(
        'supplier_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'supplier_id'
    )
    ->addColumn(
        'customer_email',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        '255',
        array('nullable' => false, 'default' => ''),
        'customer_email'
    )
    ->addColumn(
        'customer_firstname',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        '255',
        array('nullable' => false, 'default' => ''),
        'customer_firstname'
    )
    ->addColumn(
        'customer_lastname',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        '255',
        array('nullable' => false, 'default' => ''),
        'customer_lastname'
    )
    ->addColumn(
        'customer_middlename',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        '255',
        array('nullable' => false, 'default' => ''),
        'customer_middlename'
    )
    ->addColumn(
        'customer_group_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        '11',
        array('nullable' => false, 'default' => 0),
        'customer_group_id'
    )

    ->addColumn(
        'realized_sold_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'realized_sold_qty'
    )
    ->addColumn(
        'potential_sold_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'potential_sold_qty'
    )

    ->addColumn(
        'unit_cost',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'unit_cost'
    )
    ->addColumn(
        'unit_price',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'unit_price'
    )
    ->addColumn(
        'unit_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'unit_tax'
    )
    ->addColumn(
        'unit_discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'unit_discount'
    )
    ->addColumn(
        'unit_profit',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'unit_profit'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        '255',
        array('nullable' => false, 'default' => ''),
        'status'
    )
    ->addColumn(
        'shipping_method',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        '255',
        array('nullable' => false, 'default' => ''),
        'shipping_method'
    )
    ->addColumn(
        'payment_method',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        '255',
        array('nullable' => false, 'default' => ''),
        'payment_method'
    )
    ->addColumn(
        'shipping_description',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        '255',
        array('nullable' => false, 'default' => ''),
        'shipping_description'
    )

    ->addColumn(
        'realized_cogs',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'realized_cogs'
    )
    ->addColumn(
        'potential_cogs',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'potential_cogs'
    )
    ->addColumn(
        'cogs',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'cogs'
    )
    ->addColumn(
        'realized_profit',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'realized_profit'
    )
    ->addColumn(
        'potential_profit',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'potential_profit'
    )
    ->addColumn(
        'profit',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'profit'
    )
    ->addColumn(
        'realized_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'realized_tax'
    )
    ->addColumn(
        'potential_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'potential_tax'
    )
    ->addColumn(
        'tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'tax'
    )
    ->addColumn(
        'realized_discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'realized_discount'
    )->addColumn(
        'potential_discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'potential_discount'
    )->addColumn(
        'total_sale',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'total_sale'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'created_at'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'updated_at'
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/salesreport', array('product_id')),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/salesreport', array('item_id')),
        array('item_id')
    )
    ->addIndex(
        $installer->getIdxName('reportsuccess/salesreport', array('updated_at')),
        array('updated_at')
    )
    ->setComment('Sales Order Report Table');
$installer->getConnection()->createTable($table);

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_item'),'os_mac', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'nullable'  => false,
        'length'    => '12,4',
        'after'     => null, // column name to insert new column after
        'comment'   => 'os_mac'
    ));
$installer->endSetup();
