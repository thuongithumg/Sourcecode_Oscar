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
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();


$installer->getConnection()->dropTable($installer->getTable('barcodesuccess/barcode'));
$installer->getConnection()->dropTable($installer->getTable('barcodesuccess/history'));
$installer->getConnection()->dropTable($installer->getTable('barcodesuccess/template'));
/**
 * Create table 'os_barcode'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('barcodesuccess/barcode'))
    ->addColumn(
        'barcode_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Barcode ID'
    )
    ->addColumn(
        'barcode',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false, 'default' => ''),
        'Barcode'
    )
    ->addColumn(
        'qty',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Qty'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Barcode ID'
    )
    ->addColumn(
        'product_sku',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Barcode SKU'
    )
    ->addColumn(
        'supplier_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Supplier ID'
    )
    ->addColumn(
        'supplier_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Supplier Code'
    )
    ->addColumn(
        'purchased_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Purchased ID'
    )
    ->addColumn(
        'purchased_time',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false),
        'Purchased Time'
    )
    ->addColumn(
        'history_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'History ID'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false),
        'Created At'
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/barcode', array("barcode"), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        "barcode",
        array("type" => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/barcode', array('barcode_id')),
        array('barcode_id')
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/barcode', array('product_id')),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/barcode', array('product_sku')),
        array('product_sku')
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/barcode', array('history_id')),
        array('history_id')
    )
    ->addForeignKey(
        $installer->getFkName(
            'barcodesuccess/barcode',
            'product_id',
            'catalog/product',
            'entity_id'
        ),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'barcodesuccess/barcode',
            'product_sku',
            'catalog/product',
            'sku'
        ),
        'product_sku',
        $installer->getTable('catalog/product'),
        'sku',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('BarcodeSuccess Barcode Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'os_barcode_created_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('barcodesuccess/history'))
    ->addColumn(
        'history_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'History ID'
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
    ->addColumn(
        'reason',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('nullable' => false, 'default' => ''),
        'Reason'
    )
    ->addColumn(
        'total_qty',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Total Qty'
    )
    ->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(),
        'Type'
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/history', array('history_id')),
        array('history_id')
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/history', array('created_by')),
        array('created_by')
    )
    ->addForeignKey(
        $installer->getFkName(
            'barcodesuccess/history',
            'created_by',
            'admin_user',
            'user_id'
        ),
        'created_by',
        $installer->getTable('admin/user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('BarcodeSuccess History Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'os_barcode_template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('barcodesuccess/template'))
    ->addColumn(
        'template_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Template ID'
    )
    ->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Type'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Name'
    )
    ->addColumn(
        'priority',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(),
        'Priority'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Status'
    )
    ->addColumn(
        'symbology',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Symbology'
    )
    ->addColumn(
        'extra_field',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        128,
        array(),
        'Extra Field'
    )
    ->addColumn(
        'measurement_unit',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array('nullable' => false),
        'Measurement Unit'
    )
    ->addColumn(
        'label_per_row',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(),
        'Label Per Row'
    )
    ->addColumn(
        'paper_width',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Paper Width'
    )
    ->addColumn(
        'paper_height',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Paper Height'
    )
    ->addColumn(
        'label_width',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Label Width'
    )
    ->addColumn(
        'label_height',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Label Height'
    )
    ->addColumn(
        'font_size',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Font Size'
    )
    ->addColumn(
        'top_margin',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Top Margin'
    )
    ->addColumn(
        'left_margin',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Left Margin'
    )
    ->addColumn(
        'right_margin',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Right Margin'
    )
    ->addColumn(
        'bottom_margin',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        64,
        array(),
        'Bottom Margin'
    )
    ->addColumn(
        'rotate',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Rotate'
    )
    ->addColumn(
        'product_attribute_show_on_barcode',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Product Attribute'
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/template', array('template_id')),
        array('template_id')
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/template', array('name')),
        array('name')
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/template', array('type')),
        array('type')
    )
    ->addIndex(
        $installer->getIdxName('barcodesuccess/template', array('symbology')),
        array('symbology')
    )
    ->setComment('BarcodeSuccess Barcode Template Table');

    $installer->getConnection()->createTable($table);
    $installer->run("
           INSERT INTO {$installer->getTable('barcodesuccess/template')} (`type`, `name`, `status`, `symbology`, `measurement_unit`, `label_per_row`, `paper_width`, `paper_height`, `label_width`, `label_height`, `font_size`, `top_margin`, `left_margin`, `right_margin`, `bottom_margin`, `rotate`, `product_attribute_show_on_barcode`)
           VALUES ('standard','Standard',1,'code128','mm',3,109,24,35,16,16,1,1,1,1,0,'sku');
           INSERT INTO {$installer->getTable('barcodesuccess/template')} (`type`, `name`, `status`, `symbology`, `measurement_unit`, `label_per_row`, `paper_width`, `paper_height`, `label_width`, `label_height`, `font_size`, `top_margin`, `left_margin`, `right_margin`, `bottom_margin`, `rotate`, `product_attribute_show_on_barcode`)
           VALUES ('a4','A4',1,'code128','mm',4,210,20,48.25,12,16,0,2,2,2,0,'sku');
           INSERT INTO {$installer->getTable('barcodesuccess/template')} (`type`, `name`, `status`, `symbology`, `measurement_unit`, `label_per_row`, `paper_width`, `paper_height`, `label_width`, `label_height`, `font_size`, `top_margin`, `left_margin`, `right_margin`, `bottom_margin`, `rotate`, `product_attribute_show_on_barcode`)
           VALUES ('jewelry','Jewelry',1,'code128','mm',1,88,15,25,11,24,1,1,1,1,0,'sku');
        ");

$installer->endSetup();
