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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('os_purchase_order'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_code'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_item'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_item_received'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_item_transferred'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_item_returned'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_invoice'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_invoice_item'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_invoice_payment'));
$installer->getConnection()->dropTable($installer->getTable('os_purchase_order_invoice_refund'));

/**
 * create os_purchase_order table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order'))
    ->addColumn(
        'purchase_order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Id'
    )->addColumn(
        'purchase_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Purchase Code'
    )->addColumn(
        'supplier_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Supplier Id'
    )->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 1),
        'Type'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 0),
        'Status'
    )->addColumn(
        'send_email',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 0),
        'Send Email To Supplier'
    )->addColumn(
        'is_sent',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => 0),
        'Is Sent Email To Supplier'
    )->addColumn(
        'comment',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Comment'
    )->addColumn(
        'shipping_address',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => ''),
        'Shipping Address'
    )->addColumn(
        'shipping_method',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Shipping Method'
    )->addColumn(
        'shipping_cost',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Shipping Cost'
    )->addColumn(
        'payment_term',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Payment Term'
    )->addColumn(
        'placed_via',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        4,
        array('default' => 0),
        'Sales Placed Via'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Created By'
    )->addColumn(
        'user_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'User Id'
    )->addColumn(
        'total_qty_orderred',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'unsigned' => true, 'default' => 0),
        'Total Qty Orderred'
    )->addColumn(
        'total_qty_received',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'unsigned' => true, 'default' => 0),
        'Total Qty Received'
    )->addColumn(
        'total_qty_transferred',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'unsigned' => true, 'default' => 0),
        'Total Qty Transferred'
    )->addColumn(
        'total_qty_returned',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'unsigned' => true, 'default' => 0),
        'Total Qty Returned'
    )->addColumn(
        'total_qty_billed',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'unsigned' => true, 'default' => 0),
        'Total Qty Billed'
    )->addColumn(
        'subtotal',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => null),
        'Subtotal'
    )->addColumn(
        'total_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => null),
        'Total Tax'
    )->addColumn(
        'total_discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => null),
        'Total Discount'
    )->addColumn(
        'grand_total_excl_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => null),
        'Grand Total Exclude Tax'
    )->addColumn(
        'grand_total_incl_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => null),
        'Grand Total Include Tax'
    )->addColumn(
        'total_billed',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => null),
        'Total Billed'
    )->addColumn(
        'total_due',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => null),
        'Total Due'
    )->addColumn(
        'currency_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        20,
        array('default' => null),
        'Currency Code'
    )->addColumn(
        'currency_rate',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 1),
        'Currency Rate'
    )->addColumn(
        'purchased_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Purchased Sales Date'
    )->addColumn(
        'started_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Started Shipping Date'
    )->addColumn(
        'expected_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Expected Delevery Date'
    )->addColumn(
        'canceled_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('default' => null),
        'Canceled Date'
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
        array(),    
        //array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE),
        'Updated At'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order',
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
            'os_purchase_order',
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

/**
 * create os_purchase_order_item table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_code'))
    ->addColumn(
        'purchase_order_code_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Code Id'
    )->addColumn(
        'code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        11,
        array('nullable' => false),
        'Code'
    )->addColumn(
        'current_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Current Id'
    );
$installer->getConnection()->createTable($table);

/**
 * create os_purchase_order_item table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_item'))
    ->addColumn(
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Item Id'
    )->addColumn(
        'purchase_order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Id'
    )->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Product Id'
    )->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product SKU'
    )->addColumn(
        'product_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product Name'
    )->addColumn(
        'product_supplier_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Product Supplier SKU'
    )->addColumn(
        'qty_orderred',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Orderred Qty'
    )->addColumn(
        'qty_received',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Received Qty'
    )->addColumn(
        'qty_transferred',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Transferred Qty'
    )->addColumn(
        'qty_returned',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Returned Qty'
    )->addColumn(
        'qty_billed',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Billed Qty'
    )->addColumn(
        'original_cost',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Original Cost'
    )->addColumn(
        'cost',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Cost'
    )->addColumn(
        'tax',
        Varien_Db_Ddl_Table::TYPE_FLOAT,
        null,
        array('unsigned' => true, 'default' => 0),
        'Tax'
    )->addColumn(
        'discount',
        Varien_Db_Ddl_Table::TYPE_FLOAT,
        null,
        array('unsigned' => true, 'default' => 0),
        'Discount'
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
        array(),
        //array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE),
        'Updated At'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_item', 'purchase_order_id'),
        'purchase_order_id'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_item', 'product_id'),
        'product_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_item',
            'purchase_order_id',
            'os_purchase_order',
            'purchase_order_id'
        ),
        'purchase_order_id',
        $installer->getTable('os_purchase_order'),
        'purchase_order_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_item',
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
 * create os_purchase_order_item_received table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_item_received'))
    ->addColumn(
        'purchase_order_item_received_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Item Received Id'
    )->addColumn(
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Item Id'
    )->addColumn(
        'qty_received',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Received Qty'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        '255',
        array('nullable' => false, 'default' => ''),
        'Created By'
    )->addColumn(
        'received_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('nullable' => false),
        'Received At'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_item_received', 'purchase_order_item_id'),
        'purchase_order_item_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_item_received',
            'purchase_order_item_id',
            'os_purchase_order_item',
            'purchase_order_item_id'
        ),
        'purchase_order_item_id',
        $installer->getTable('os_purchase_order_item'),
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create os_purchase_order_item_transferred table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_item_transferred'))
    ->addColumn(
        'purchase_order_item_transferred_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Item Transferred Id'
    )->addColumn(
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Item Id'
    )->addColumn(
        'qty_transferred',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Transferred Qty'
    )->addColumn(
        'warehouse_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        '12,4',
        array('nullable' => false, 'unsigned' => true),
        'Warehouse Id'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Created By'
    )->addColumn(
        'transferred_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('nullable' => false),
        'Transferred At'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_item_transferred', 'purchase_order_item_id'),
        'purchase_order_item_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_item_transferred',
            'purchase_order_item_id',
            'os_purchase_order_item',
            'purchase_order_item_id'
        ),
        'purchase_order_item_id',
        $installer->getTable('os_purchase_order_item'),
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create os_purchase_order_item_returned table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_item_returned'))
    ->addColumn(
        'purchase_order_item_returned_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Item Returned Id'
    )->addColumn(
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Item Id'
    )->addColumn(
        'qty_returned',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Transferred Qty'
    )->addColumn(
        'created_by',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Created By'
    )->addColumn(
        'returned_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('nullable' => false),
        'Returned At'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_item_returned', 'purchase_order_item_id'),
        'purchase_order_item_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_item_returned',
            'purchase_order_item_id',
            'os_purchase_order_item',
            'purchase_order_item_id'
        ),
        'purchase_order_item_id',
        $installer->getTable('os_purchase_order_item'),
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create os_purchase_order_invoice table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_invoice'))
    ->addColumn(
        'purchase_order_invoice_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Invoice Id'
    )->addColumn(
        'invoice_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Invoice Code'
    )->addColumn(
        'purchase_order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Id'
    )->addColumn(
        'billed_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('nullable' => false),
        'Billed At'
    )->addColumn(
        'total_qty_billed',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Total Qty Billed'
    )->addColumn(
        'subtotal',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Subtotal'
    )->addColumn(
        'total_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Total Tax'
    )->addColumn(
        'total_discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Total Discount'
    )->addColumn(
        'grand_total_excl_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Grand Total Exclude Tax'
    )->addColumn(
        'grand_total_incl_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Grand Total Include Tax'
    )->addColumn(
        'total_due',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Total Due'
    )->addColumn(
        'total_refund',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Total Refund'
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
        array(),
        //array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE),
        'Updated At'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_invoice', 'purchase_order_id'),
        'purchase_order_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_invoice',
            'purchase_order_id',
            'os_purchase_order',
            'purchase_order_id'
        ),
        'purchase_order_id',
        $installer->getTable('os_purchase_order'),
        'purchase_order_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create os_purchase_order_invoice_item table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_invoice_item'))
    ->addColumn(
        'purchase_order_invoice_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Invoice Item Id'
    )->addColumn(
        'purchase_order_invoice_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Invoice Id'
    )->addColumn(
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Item Id'
    )->addColumn(
        'qty_billed',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Billed Qty'
    )->addColumn(
        'unit_price',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Unit Price'
    )->addColumn(
        'tax',
        Varien_Db_Ddl_Table::TYPE_FLOAT,
        null,
        array('default' => 0),
        'Tax'
    )->addColumn(
        'discount',
        Varien_Db_Ddl_Table::TYPE_FLOAT,
        null,
        array('default' => 0),
        'Discount'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_invoice_item', 'purchase_order_invoice_id'),
        'purchase_order_invoice_id'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_invoice_item', 'purchase_order_item_id'),
        'purchase_order_item_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_invoice_item',
            'purchase_order_invoice_id',
            'os_purchase_order_invoice',
            'purchase_order_invoice_id'
        ),
        'purchase_order_invoice_id',
        $installer->getTable('os_purchase_order_invoice'),
        'purchase_order_invoice_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_invoice_item',
            'purchase_order_item_id',
            'os_purchase_order_item',
            'purchase_order_item_id'
        ),
        'purchase_order_item_id',
        $installer->getTable('os_purchase_order_item'),
        'purchase_order_item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create os_purchase_order_invoice_payment table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_invoice_payment'))
    ->addColumn(
        'purchase_order_invoice_payment_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Invoice Item Id'
    )->addColumn(
        'purchase_order_invoice_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Invoice Id'
    )->addColumn(
        'payment_method',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('nullable' => false),
        'Payment Method'
    )->addColumn(
        'payment_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Payment Amount'
    )->addColumn(
        'description',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Description'
    )->addColumn(
        'payment_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('nullable' => false),
        'Payment Date'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_invoice_payment', 'purchase_order_invoice_id'),
        'purchase_order_invoice_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_invoice_payment',
            'purchase_order_invoice_id',
            'os_purchase_order_invoice',
            'purchase_order_invoice_id'
        ),
        'purchase_order_invoice_id',
        $installer->getTable('os_purchase_order_invoice'),
        'purchase_order_invoice_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create os_purchase_order_invoice_refund table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('os_purchase_order_invoice_refund'))
    ->addColumn(
        'purchase_order_invoice_refund_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Purchase Sales Invoice Refund Id'
    )->addColumn(
        'purchase_order_invoice_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Purchase Sales Invoice Id'
    )->addColumn(
        'refund_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Refund Amount'
    )->addColumn(
        'reason',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Reason'
    )->addColumn(
        'refund_at',
        Varien_Db_Ddl_Table::TYPE_DATE,
        null,
        array('nullable' => false),
        'Refund Date'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addIndex(
        $installer->getIdxName('os_purchase_order_invoice_refund', 'purchase_order_invoice_id'),
        'purchase_order_invoice_id'
    )->addForeignKey(
        $installer->getFkName(
            'os_purchase_order_invoice_refund',
            'purchase_order_invoice_id',
            'os_purchase_order_invoice',
            'purchase_order_invoice_id'
        ),
        'purchase_order_invoice_id',
        $installer->getTable('os_purchase_order_invoice'),
        'purchase_order_invoice_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);


$installer->endSetup();