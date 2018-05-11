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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$installer->startSetup();

$setup->removeAttribute('catalog_product', 'gift_amount');
$attr = array(
    'group' => 'Prices',
    'type' => 'text',
    'input' => 'textarea',
    'label' => 'Gift amount',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 101,
);
$setup->addAttribute('catalog_product', 'gift_amount', $attr);

$giftAmount = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_amount'));
$giftAmount->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'apply_to' => array('giftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 1,
    'is_visible_in_advanced_search' => 1,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
    'backend_type' => 'text',
))->save();

$tax = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'tax_class_id'));
$applyTo = explode(',', $tax->getData('apply_to'));
$applyTo[] = 'giftvoucher';
$tax->addData(array('apply_to' => $applyTo))->save();

$installer->getConnection()->dropTable($installer->getTable('giftvoucher_history'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher'));

/**
 * create giftvoucher table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftvoucher'))
    ->addColumn(
        'giftvoucher_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Giftvoucher Id'
    )->addColumn(
        'gift_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        127,
        array('nullable' => false,'default' => null),
        'Gift Code'
    )->addColumn(
        'giftcard_template_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('nullable' => false,'default' => 0),
        'GiftCard Template Id'
    )->addColumn(
        'balance',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Balance'
    )->addColumn(
        'currency',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Currency'
    )->addColumn(
        'message',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Message'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('nullable' => false,'default' => '0'),
        'Status'
    )->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true),
        'Customer Id'
    )->addColumn(
        'customer_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        127,
        array('nullable' => false, 'default' => ''),
        'Customer Name'
    )->addColumn(
        'customer_email',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        127,
        array('nullable' => false, 'default' => ''),
        'Customer Email'
    )->addColumn(
        'recipient_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        127,
        array('nullable' => false, 'default' => ''),
        'Recipient Name'
    )->addColumn(
        'recipient_email',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        127,
        array('nullable' => false, 'default' => ''),
        'Recipient Email'
    )->addColumn(
        'recipient_address',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Recipient Address'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false, 'default' => 0),
        'Store ID'
    )->addColumn(
        'day_to_send',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('nullable' => true, 'default' => null),
        'Day To Send'
    )->addColumn(
        'expired_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('nullable' => true, 'default' => null),
        'Expired At'
    )->addIndex(
        $installer->getIdxName(
            'giftvoucher',
            array('gift_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('gift_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )->addIndex(
        $installer->getIdxName('giftvoucher', array('giftcard_template_id')),
        array('giftcard_template_id')
    )->addForeignKey(
        $installer->getFkName(
            'giftvoucher',
            'giftcard_template_id',
            'giftcard_template',
            'giftcard_template_id'
        ),
        'giftcard_template_id',
        $installer->getTable('giftcard_template'),
        'giftcard_template_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create giftvoucher_history table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftvoucher_history'))
    ->addColumn(
        'history_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'History Id'
    )
    ->addColumn(
        'giftvoucher_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Giftvoucher Id'
    )
    ->addColumn(
        'action',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        6,
        array('unsigned' => true, 'nullable' => false),
        'Action'
    )->addColumn(
        'amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Amount'
    )->addColumn(
        'currency',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Currency'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('nullable' => false,'default' => 0),
        'Status'
    )->addColumn(
        'comments',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Comments'
    )->addColumn(
        'order_increment_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        127,
        array('default' => ''),
        'Order Increment Id'
    )->addColumn(
        'order_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Order Item Id'
    )->addColumn(
        'order_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Order Amount'
    )->addColumn(
        'extra_content',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Extra Conent'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('nullable' => true, 'default' => null),
        'Created At'
    )->addIndex(
        $installer->getIdxName('giftvoucher_history', array('giftvoucher_id')),
        array('giftvoucher_id')
    )->addForeignKey(
        $installer->getFkName(
            'giftvoucher_history',
            'giftvoucher_id',
            'giftvoucher',
            'giftvoucher_id'
        ),
        'giftvoucher_id',
        $installer->getTable('giftvoucher'),
        'giftvoucher_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);


$installer->endSetup();
