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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('os_supplier'));
$installer->getConnection()->dropTable($installer->getTable('os_supplier_product'));
$installer->getConnection()->dropTable($installer->getTable('os_supplier_pricelist'));

/**
 * create os_supplier table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_supplier'))
    ->addColumn(
        'supplier_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Supplier Id'
    )->addColumn(
        'supplier_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Supplier Code'
    )->addColumn(
        'supplier_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Supplier Name'
    )->addColumn(
        'contact_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Contact Name'
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
        'fax',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        50,
        array('default' => null),
        'Fax'
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
        'region_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Region Id'
    )->addColumn(
        'region',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Region'
    )->addColumn(
        'postcode',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Postcode'
    )->addColumn(
        'website',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Website'
    )->addColumn(
        'description',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('default' => null),
        'Description'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('default' => null),
        'Status'
    )->addColumn(
        'password',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => null),
        'Password'
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
        $installer->getIdxName(
            'os_supplier',
            array('supplier_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('supplier_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    );
$installer->getConnection()->createTable($table);

/**
 * create os_supplier_product table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_supplier_product'))
    ->addColumn(
        'supplier_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Supplier Product Id'
    )->addColumn(
        'supplier_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Supplier Id'
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
        'Product sku'
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
        'Product Supplier Sku'
    )->addColumn(
        'cost',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Cost'
    )->addColumn(
        'tax',
        Varien_Db_Ddl_Table::TYPE_FLOAT,
        null,
        array('nullable' => false, 'default' => 0),
        'Tax'
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
        $installer->getIdxName('os_supplier_product', array('supplier_id')),
        array('supplier_id')
    )->addIndex(
        $installer->getIdxName('os_supplier_product', array('product_id')),
        array('product_id')
    )->addIndex(
        $installer->getIdxName(
            'os_supplier_product',
            array('supplier_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('supplier_id', 'product_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->addForeignKey(
        $installer->getFkName(
            'os_supplier_product',
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
            'os_supplier_product',
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
 * create os_supplier_pricelist table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('os_supplier_pricelist'))
    ->addColumn(
        'supplier_pricelist_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Supplier Pricelist Id'
    )->addColumn(
        'supplier_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => null, 'unsigned' => true),
        'Supplier Id'
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
        'Product sku'
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
        'Product Supplier Sku'
    )->addColumn(
        'minimal_qty',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Minimum Qty'
    )->addColumn(
        'cost',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false, 'default' => 0),
        'Cost'
    )->addColumn(
        'start_date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('default' => null),
        'Created At'
    )->addColumn(
        'end_date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('default' => null),
        'Created At'
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
        $installer->getIdxName(
            'os_supplier_pricelist',
            array('supplier_id', 'product_id', 'minimal_qty'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('supplier_id', 'product_id', 'minimal_qty'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    );
$installer->getConnection()->createTable($table);



$installer->endSetup();

