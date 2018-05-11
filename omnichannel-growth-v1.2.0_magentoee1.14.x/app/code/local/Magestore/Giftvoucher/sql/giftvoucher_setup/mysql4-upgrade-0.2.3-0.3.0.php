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

$setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

/* add Gift Card product attribute */
$setup->removeAttribute('catalog_product', 'show_gift_amount_desc');
$setup->removeAttribute('catalog_product', 'gift_amount_desc');
$setup->removeAttribute('catalog_product', 'giftcard_description');
$attr = array(
    'group' => 'Prices',
    'type' => 'int',
    'input' => 'boolean',
    'label' => 'Show description of gift card value',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 3,
    'unique' => 0,
    'default' => '',
    'sort_order' => 102,
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'apply_to' => 'giftvoucher',
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
);
$setup->addAttribute('catalog_product', 'show_gift_amount_desc', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'show_gift_amount_desc'));
$attribute->addData($attr)->save();

$attr['type'] = 'text';
$attr['input'] = 'textarea';
$attr['label'] = 'Description of gift card value';
$attr['position'] = 5;
$attr['sort_order'] = 103;
$attr['backend_type'] = 'text';
$setup->addAttribute('catalog_product', 'gift_amount_desc', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_amount_desc'));
$attribute->addData($attr)->save();

$attr['label'] = 'Description of gift card conditions';
$attr['position'] = 7;
$attr['sort_order'] = 105;
$setup->addAttribute('catalog_product', 'giftcard_description', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'giftcard_description'));
$attribute->addData($attr)->save();

$connection = $installer->getConnection();
/* update Gift Card Database */
/* add balance to giftvoucher_history */
if(!$connection->tableColumnExists($this->getTable('giftvoucher_history'), 'balance')) {
    $connection->addColumn(
        $this->getTable('giftvoucher_history'),
        'balance',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Balance',
        )
    );
}

/* add customer_id to giftvoucher_history */
if(!$connection->tableColumnExists($this->getTable('giftvoucher_history'), 'customer_id')) {
    $connection->addColumn(
        $this->getTable('giftvoucher_history'),
        'customer_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 6,
            'nullable' => false,
            'comment' => 'Customer Id'
        )
    );
}

/* add customer_email to giftvoucher_history */
if(!$connection->tableColumnExists($this->getTable('giftvoucher_history'), 'customer_email')) {
    $connection->addColumn(
        $this->getTable('giftvoucher_history'),
        'customer_email',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'default' => '',
            'comment' => 'Customer Email',
        )
    );
}

/* add conditions_serialized to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'conditions_serialized')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'conditions_serialized',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'default' => '',
            'comment' => 'Conditions Serialized',
        )
    );
}
/* add day_to_send to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'day_to_send')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'day_to_send',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
            'length' => null,
            'nullable' => true,
            'default' => null,
            'comment' => 'Day To Send',
        )
    );
}
/* add is_sent to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'is_sent')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'is_sent',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length' => 1,
            'nullable' => true,
            'default' => 0,
            'comment' => 'Is Sent',
        )
    );
}

/* add shipped_to_customer to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'shipped_to_customer')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'shipped_to_customer',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length' => 1,
            'nullable' => false,
            'default' => 0,
            'comment' => 'Shipped to Customer',
        )
    );
}

/* add created_form to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'created_form')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'created_form',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'default' => '',
            'nullable' => true,
            'comment' => 'Created From',
        )
    );
}

/* add template_id to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'template_id')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'template_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 255,
            'nullable' => true,
            'comment' => 'Template Id',
        )
    );
}

/* add description to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'description')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'description',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'default' => '',
            'comment' => 'Description',
        )
    );
}

/* add giftvoucher_comments to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'giftvoucher_comments')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'giftvoucher_comments',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'default' => '',
            'comment' => 'Giftvoucher Comments',
        )
    );
}

/* add email_sender to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'email_sender')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'email_sender',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length' => 6,
            'nullable' => true,
            'default' => 0,
            'comment' => 'Email Sender',
        )
    );
}

/* add fields for invoice */
/* add base_gift_voucher_discount to sales_flat_invoice */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'base_gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'base_gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Base Gift Voucher Discount',
        )
    );
}
/* add gift_voucher_discount to sales_flat_invoice */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Gift Voucher Discount',
        )
    );
}
/* add base_use_gift_credit_amount to sales_flat_invoice */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'base_use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'base_use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Base Use Gift Credit Amount',
        )
    );
}
/* add use_gift_credit_amount to sales_flat_invoice */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Use Gift Credit Amount',
        )
    );
}

/* add fields for creditmemo */
/* add base_gift_voucher_discount to sales_flat_creditmemo */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'base_gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'base_gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Base Gift Voucher Discount',
        )
    );
}
/* add gift_voucher_discount to sales_flat_creditmemo */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Gift Voucher Discount',
        )
    );
}
/* add base_use_gift_credit_amount to sales_flat_creditmemo */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'base_use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'base_use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Base Use Gift Credit Amount',
        )
    );
}
/* add use_gift_credit_amount to sales_flat_creditmemo */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Use Gift Credit Amount',
        )
    );
}
/* add giftcard_refund_amount to sales_flat_creditmemo */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'giftcard_refund_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'giftcard_refund_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'default' => '0.0000',
            'comment' => 'Gift Card Refund Amount',
        )
    );
}

$installer->getConnection()->dropTable($installer->getTable('giftvoucher_credit'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_credit_history'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_customer_voucher'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_product'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_template'));

/* add gift card credit database */
/**
 * create giftvoucher_credit table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftvoucher_credit'))
    ->addColumn(
        'credit_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Credit Id'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Customer Id'
    )
    ->addColumn(
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
    )->addIndex(
        $installer->getIdxName('giftvoucher_credit', array('customer_id')),
        array('customer_id')
    )->addForeignKey(
        $installer->getFkName(
            'giftvoucher_credit',
            'customer_id',
            'customer_entity',
            'entity_id'
        ),
        'customer_id',
        $installer->getTable('customer_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create giftvoucher_credit_history table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftvoucher_credit_history'))
    ->addColumn(
        'history_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'History Id'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Customer Id'
    )
    ->addColumn(
        'action',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        45,
        array('default' => ''),
        'Action'
    )->addColumn(
        'currency_balance',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Currency Balance'
    )->addColumn(
        'giftcard_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false,'default' => ''),
        'Giftcard Code'
    )->addColumn(
        'balance_change',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Balance Change'
    )->addColumn(
        'currency',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Currency'
    )->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        255,
        array('unsigned' => true, 'nullable' => true),
        'Order Id'
    )->addColumn(
        'order_number',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Order Number'
    )->addColumn(
        'base_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Base Amount'
    )->addColumn(
        'amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => 0),
        'Amount'
    )->addColumn(
        'created_date',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('nullable' => true, 'default' => null),
        'Created Date'
    )->addIndex(
        $installer->getIdxName('giftvoucher_credit_history', array('customer_id')),
        array('customer_id')
    )->addForeignKey(
        $installer->getFkName(
            'giftvoucher_credit_history',
            'customer_id',
            'customer_entity',
            'entity_id'
        ),
        'customer_id',
        $installer->getTable('customer_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create giftvoucher_customer_voucher table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftvoucher_customer_voucher'))
    ->addColumn(
        'customer_voucher_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Customer Voucher Id'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Customer Id'
    )
    ->addColumn(
        'voucher_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Voucher Id'
    )->addColumn(
        'added_date',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('nullable' => true, 'default' => null),
        'Added Date'
    )->addIndex(
        $installer->getIdxName('giftvoucher_customer_voucher', array('customer_id')),
        array('customer_id')
    )->addIndex(
        $installer->getIdxName('giftvoucher_customer_voucher', array('voucher_id')),
        array('voucher_id')
    )->addForeignKey(
        $installer->getFkName(
            'giftvoucher_customer_voucher',
            'customer_id',
            'customer_entity',
            'entity_id'
        ),
        'customer_id',
        $installer->getTable('customer_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName(
            'giftvoucher_customer_voucher',
            'voucher_id',
            'giftvoucher',
            'giftvoucher_id'
        ),
        'voucher_id',
        $installer->getTable('giftvoucher'),
        'giftvoucher_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->createTable($table);

/**
 * create giftvoucher_template table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftvoucher_template'))
    ->addColumn(
        'template_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Template Id'
    )
    ->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Type'
    )
    ->addColumn(
        'template_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Template Name'
    )->addColumn(
        'pattern',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Pattern'
    )->addColumn(
        'balance',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => '0'),
        'Balance'
    )->addColumn(
        'currency',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Currency'
    )->addColumn(
        'expired_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('nullable' => true, 'default' => null),
        'Expired At'
    )->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        6,
        array('unsigned' => true, 'nullable' => false),
        'Store Id'
    )->addColumn(
        'amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('default' => '0.00'),
        'Amount'
    )->addColumn(
        'day_to_send',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('nullable' => true, 'default' => null),
        'Day to Send'
    )->addColumn(
        'conditions_serialized',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Conditions Serialized'
    )->addColumn(
        'is_generated',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        1,
        array('unsigned' => true, 'nullable' => false),
        'Is generated'
    );
$installer->getConnection()->createTable($table);

/**
 * create giftvoucher_product table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftvoucher_product'))
    ->addColumn(
        'giftcard_product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Giftcard Product Id'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('unsigned' => true, 'nullable' => false),
        'Product Id'
    )
    ->addColumn(
        'conditions_serialized',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Conditions Serialized'
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();

