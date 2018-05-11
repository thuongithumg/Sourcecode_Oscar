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

$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('giftvoucher'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_credit'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_credit_history'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_customer_voucher'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_giftcodeset'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_history'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_product'));
$installer->getConnection()->dropTable($installer->getTable('giftvoucher_template'));
$installer->getConnection()->dropTable($installer->getTable('giftcard_template'));

$connection = $installer->getConnection();

/*
 * add column gift_amount for product
 */
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

$weight = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'weight'));
$applyTo = explode(',', $weight->getData('apply_to'));
$applyTo[] = 'giftvoucher';
$weight->addData(array('apply_to' => $applyTo))->save();

/**
 * create giftcard_template table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftcard_template'))
    ->addColumn(
        'giftcard_template_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Giftcard Template Id'
    )
    ->addColumn(
        'template_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false,'default' => ''),
        'Template Name'
    )->addColumn(
        'style_color',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false,'default' => ''),
        'Style Color'
    )->addColumn(
        'text_color',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false,'default' => ''),
        'Text Color'
    )->addColumn(
        'caption',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false,'default' => ''),
        'Caption'
    )
    ->addColumn(
        'notes',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Notes'
    )->addColumn(
        'images',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Images'
    )->addColumn(
        'design_pattern',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        5,
        array('default' => 0),
        'Design Pattern'
    )->addColumn(
        'background_img',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => ''),
        'Background Image'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('default' => 1),
        'Status'
    );
$installer->getConnection()->createTable($table);

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

/**
 * add more column for giftvoucher table
 */

/* add notify_success to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'notify_success')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'notify_success',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length' => 1,
            'default' => 0,
            'nullable' => true,
            'comment' => 'Notify Sucess',
        )
    );
}
/* add giftcard_custom_image to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'giftcard_custom_image')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'giftcard_custom_image',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length' => 1,
            'default' => 0,
            'nullable' => true,
            'comment' => 'Giftcard Custom Image',
        )
    );
}
/* add giftcard_template_id to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'giftcard_template_id')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'giftcard_template_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length' => 11,
            'default' => 0,
            'nullable' => false,
            'unsigned' => true,
            'comment' => 'GiftCard Template Id',
        )
    );
}
/* add giftcard_template_image to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'giftcard_template_image')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'giftcard_template_image',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'default' => '',
            'nullable' => true,
            'comment' => 'GiftCard Template Image',
        )
    );
}
/* add giftcard_description to giftvoucher_product */
if(!$connection->tableColumnExists($this->getTable('giftvoucher_product'), 'giftcard_description')) {
    $connection->addColumn(
        $this->getTable('giftvoucher_product'),
        'giftcard_description',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'default' => '',
            'nullable' => true,
            'comment' => 'GiftCard Description',
        )
    );
}
/* add actions_serialized to giftvoucher_product */
if(!$connection->tableColumnExists($this->getTable('giftvoucher_product'), 'actions_serialized')) {
    $connection->addColumn(
        $this->getTable('giftvoucher_product'),
        'actions_serialized',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 16777215,
            'default' => '',
            'nullable' => true,
            'comment' => 'Actions Serialized',
        )
    );
}
/* add actions_serialized to giftvoucher */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'actions_serialized')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'actions_serialized',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 16777215,
            'default' => '',
            'nullable' => true,
            'comment' => 'Actions Serialized',
        )
    );
}

/*
 * add more columns for sales/order table
 */
/* add base_giftvoucher_discount_for_shipping to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'base_giftvoucher_discount_for_shipping')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'base_giftvoucher_discount_for_shipping',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Giftvoucher Discount for Shipping',
        )
    );
}
/* add giftvoucher_discount_for_shipping to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftvoucher_discount_for_shipping')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftvoucher_discount_for_shipping',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Giftvoucher Discount for Shipping',
        )
    );
}
/* add base_giftcredit_discount_for_shipping to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'base_giftcredit_discount_for_shipping')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'base_giftcredit_discount_for_shipping',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Giftcredit Discount for Shipping',
        )
    );
}
/* add giftcredit_discount_for_shipping to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftcredit_discount_for_shipping')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftcredit_discount_for_shipping',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Giftcredit Discount for Shipping',
        )
    );
}

/*
 * add more fields for sales order item table
 */
/* add base_gift_voucher_discount to sales/order_item table */
if(!$connection->tableColumnExists($this->getTable('sales/order_item'), 'base_gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/order_item'),
        'base_gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Giftvoucher Discount',
        )
    );
}
/* add gift_voucher_discount to sales/order_item table */
if(!$connection->tableColumnExists($this->getTable('sales/order_item'), 'gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/order_item'),
        'gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Giftvoucher Discount',
        )
    );
}
/* add base_use_gift_credit_amount to sales/order_item table */
if(!$connection->tableColumnExists($this->getTable('sales/order_item'), 'base_use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order_item'),
        'base_use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Use GiftCredit Amount',
        )
    );
}
/* add use_gift_credit_amount to sales/order_item table */
if(!$connection->tableColumnExists($this->getTable('sales/order_item'), 'use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order_item'),
        'use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Use GiftCredit Amount',
        )
    );
}
/*
 * add fields to sales/invoice_item
 */
/* add base_gift_voucher_discount to sales/invoice_item table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice_item'), 'base_gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice_item'),
        'base_gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Giftvoucher Discount',
        )
    );
}
/* add gift_voucher_discount to sales/invoice_item table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice_item'), 'gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice_item'),
        'gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Giftvoucher Discount',
        )
    );
}
/* add base_use_gift_credit_amount to sales/invoice_item table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice_item'), 'base_use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice_item'),
        'base_use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Use GiftCredit Amount',
        )
    );
}
/* add use_gift_credit_amount to sales/invoice_item table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice_item'), 'use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice_item'),
        'use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Use GiftCredit Amount',
        )
    );
}

/*
 * add fields for creditmemo item
 */
/* add base_gift_voucher_discount to sales/creditmemo_item table */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo_item'), 'base_gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo_item'),
        'base_gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Giftvoucher Discount',
        )
    );
}
/* add gift_voucher_discount to sales/creditmemo_item table */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo_item'), 'gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo_item'),
        'gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Giftvoucher Discount',
        )
    );
}
/* add base_use_gift_credit_amount to sales/creditmemo_item table */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo_item'), 'base_use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo_item'),
        'base_use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Use GiftCredit Amount',
        )
    );
}
/* add use_gift_credit_amount to sales/creditmemo_item table */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo_item'), 'use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo_item'),
        'use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Use GiftCredit Amount',
        )
    );
}
/* add giftcard_refund_amount to creditmemo_item */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo_item'), 'giftcard_refund_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo_item'),
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




$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$installer->startSetup();
$setup->removeAttribute('catalog_product', 'gift_amount');
$setup->removeAttribute('catalog_product', 'gift_type');
$setup->removeAttribute('catalog_product', 'show_gift_amount_desc');
$setup->removeAttribute('catalog_product', 'gift_amount_desc');
$setup->removeAttribute('catalog_product', 'giftcard_description');
$setup->removeAttribute('catalog_product', 'gift_value');
$setup->removeAttribute('catalog_product', 'gift_from');
$setup->removeAttribute('catalog_product', 'gift_to');
$setup->removeAttribute('catalog_product', 'gift_dropdown');
$setup->removeAttribute('catalog_product', 'gift_price_type');
$setup->removeAttribute('catalog_product', 'gift_price');
$setup->removeAttribute('catalog_product', 'gift_template_ids');
/**
 * add gift template attribute
 */
$attGiftTemplate = array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'multiselect',
    'default' => 1,
    'label' => 'Select Gift Card templates ',
    'backend' => 'eav/entity_attribute_backend_array',
    'frontend' => '',
    'source' => 'giftvoucher/templateoptions',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 100,
    'apply_to' => array('giftvoucher'),
);
$setup->addAttribute('catalog_product', 'gift_template_ids', $attGiftTemplate);
$attGiftTemplate = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_template_ids'));
$attGiftTemplate->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 1,
    'apply_to' => array('giftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
))->save();
/**
 * add gift type attribute
 */
$attGifttype = array(
    'group' => 'Prices',
    'type' => 'int',
    'input' => 'select',
    'label' => 'Type of Gift Card value',
    'backend' => '',
    'frontend' => '',
    'source' => 'giftvoucher/gifttype',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 101,
    'apply_to' => array('giftvoucher'),
);
$setup->addAttribute('catalog_product', 'gift_type', $attGifttype);
$giftType = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_type'));
$giftType->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 1,
    'apply_to' => array('giftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
))->save();
/**
 * add gift_value attribute
 */
$attGiftValue = array(
    'group' => 'Prices',
    'type' => 'decimal',
    'input' => 'price',
    'class' => 'validate-number',
    'label' => 'Gift Card value',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 4,
    'unique' => 0,
    'default' => '',
    'sort_order' => 103,
);
$setup->addAttribute('catalog_product', 'gift_value', $attGiftValue);
$giftValue = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_value'));
$giftValue->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 1,
    'apply_to' => array('giftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
))->save();
/**
 * add gift_price attribute
 */
$attGiftPrice = array(
    'group' => 'Prices',
    'type' => 'text',
    'input' => 'text',
    'label' => 'Gift Card price',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 8,
    'unique' => 0,
    'default' => '',
    'sort_order' => 105,
    'is_required' => 1,
    'note' => 'Notes: ',
);
$setup->addAttribute('catalog_product', 'gift_price', $attGiftPrice);
$giftPrice = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_price'));
$giftPrice->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 1,
    'apply_to' => array('giftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
))->save();

/* add Gift Card product attribute */
//show description of giftcard
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
    'position' => 10,
    'unique' => 0,
    'default' => '',
    'sort_order' => 109,
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'apply_to' => 'giftvoucher',
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
);

/**
 * add gift from,to attribute for gift type range
 */
$attr['type'] = 'decimal';
$attr['input'] = 'price';
$attr['is_required'] = 1;
$attr['label'] = 'Minimum Gift Card value';
$attr['position'] = 4;
$attr['sort_order'] = 102;
$attr['class'] = 'validate-number';
$setup->addAttribute('catalog_product', 'gift_from', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_from'));
$attribute->addData($attr)->save();
$attr['type'] = 'decimal';
$attr['input'] = 'price';
$attr['label'] = 'Maximum Gift Card value';
$attr['position'] = 5;
$attr['sort_order'] = 103;
$attr['class'] = 'validate-number';
$setup->addAttribute('catalog_product', 'gift_to', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_to'));
$attribute->addData($attr)->save();
/**
 * add gift value attribute for gift type dropdown
 */
$attr['type'] = 'varchar';
$attr['input'] = 'text';
$attr['label'] = 'Gift Card values';
$attr['position'] = 6;
$attr['sort_order'] = 102;
$attr['backend_type'] = 'text';
$attr['class'] = '';
$attr['note'] = Mage::helper('giftvoucher')->__('Seperated by comma, e.g. 10,20,30');
$setup->addAttribute('catalog_product', 'gift_dropdown', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_dropdown'));
$attribute->addData($attr)->save();
//gift price type
$attr['type'] = 'int';
$attr['is_required'] = 0;
$attr['input'] = 'select';
$attr['source'] = 'giftvoucher/giftpricetype';
$attr['label'] = 'Type of Gift Card price';
$attr['position'] = 7;
$attr['sort_order'] = 104;
$attr['backend_type'] = 'text';
$attr['note'] = 'Gift Card price is the same as Gift Card value by default.';
$attr['class'] = '';
$setup->addAttribute('catalog_product', 'gift_price_type', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'gift_price_type'));
$attribute->addData($attr)->save();

/*
 * add more columns for sales/order table
 */
/* add base_gift_voucher_discount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'base_gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'base_gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Giftvoucher Discount ',
        )
    );
}
/* add gift_voucher_discount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Giftvoucher Discount ',
        )
    );
}
/* add base_use_gift_credit_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'base_use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'base_use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Use Giftcredit Amount',
        )
    );
}
/* add use_gift_credit_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Use Giftcredit Amount',
        )
    );
}
/* add giftvoucher_base_hidden_tax_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftvoucher_base_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftvoucher_base_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Base Hidden Tax Amount',
        )
    );
}
/* add giftvoucher_hidden_tax_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftvoucher_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftvoucher_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Hidden Tax Amount',
        )
    );
}
/* add giftcredit_base_hidden_tax_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftcredit_base_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftcredit_base_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Base Hidden Tax Amount',
        )
    );
}
/* add giftcredit_hidden_tax_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftcredit_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftcredit_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Hidden Tax Amount',
        )
    );
}
/*
 * Add more fields to sales/order_item table
 */
/* add giftvoucher_base_hidden_tax_amount to sales/order_item table */
if(!$connection->tableColumnExists($this->getTable('sales/order_item'), 'giftvoucher_base_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order_item'),
        'giftvoucher_base_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Base Hidden Tax Amount',
        )
    );
}
/* add giftvoucher_hidden_tax_amount to sales/order_item table */
if(!$connection->tableColumnExists($this->getTable('sales/order_item'), 'giftvoucher_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order_item'),
        'giftvoucher_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Hidden Tax Amount',
        )
    );
}
/* add giftcredit_base_hidden_tax_amount to sales/order_item table */
if(!$connection->tableColumnExists($this->getTable('sales/order_item'), 'giftcredit_base_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order_item'),
        'giftcredit_base_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Base Hidden Tax Amount',
        )
    );
}
/* add giftcredit_hidden_tax_amount to sales/order_item table */
if(!$connection->tableColumnExists($this->getTable('sales/order_item'), 'giftcredit_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order_item'),
        'giftcredit_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Hidden Tax Amount',
        )
    );
}
/*
 * add more fields to sales/invoice table
 */
/* add giftvoucher_base_hidden_tax_amount to sales/invoice table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'giftvoucher_base_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'giftvoucher_base_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Base Hidden Tax Amount',
        )
    );
}
/* add giftvoucher_hidden_tax_amount to sales/invoice table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'giftvoucher_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'giftvoucher_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Hidden Tax Amount',
        )
    );
}
/* add giftcredit_base_hidden_tax_amount to sales/invoice table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'giftcredit_base_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'giftcredit_base_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Base Hidden Tax Amount',
        )
    );
}
/* add giftcredit_hidden_tax_amount to sales/invoice table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'giftcredit_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'giftcredit_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Hidden Tax Amount',
        )
    );
}
/*
 * add more fields to sales/invoice table
 */
/* add base_gift_voucher_discount to sales/invoice table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'base_gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'base_gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Giftvoucher Discount ',
        )
    );
}
/* add gift_voucher_discount to sales/invoice table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'gift_voucher_discount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'gift_voucher_discount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Giftvoucher Discount ',
        )
    );
}
/* add base_use_gift_credit_amount to sales/invoice table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'base_use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'base_use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Base Use Giftcredit Amount',
        )
    );
}
/* add use_gift_credit_amount to sales/invoice table */
if(!$connection->tableColumnExists($this->getTable('sales/invoice'), 'use_gift_credit_amount')) {
    $connection->addColumn(
        $this->getTable('sales/invoice'),
        'use_gift_credit_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => true,
            'comment' => 'Use Giftcredit Amount',
        )
    );
}
/*
 * add more fields to sales/creditmemo table
 */
/* add giftvoucher_base_hidden_tax_amount to sales/creditmemo table */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'giftvoucher_base_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'giftvoucher_base_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Base Hidden Tax Amount',
        )
    );
}
/* add giftvoucher_hidden_tax_amount to sales/creditmemo table */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'giftvoucher_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'giftvoucher_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Hidden Tax Amount',
        )
    );
}
/* add giftcredit_base_hidden_tax_amount to sales/creditmemo table */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'giftcredit_base_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'giftcredit_base_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Base Hidden Tax Amount',
        )
    );
}
/* add giftcredit_hidden_tax_amount to sales/creditmemo table */
if(!$connection->tableColumnExists($this->getTable('sales/creditmemo'), 'giftcredit_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/creditmemo'),
        'giftcredit_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Hidden Tax Amount',
        )
    );
}
/*
 * add more fields to sales/order table
 */
/* add giftvoucher_base_shipping_hidden_tax_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftvoucher_base_shipping_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftvoucher_base_shipping_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Base Shipping Hidden Tax Amount',
        )
    );
}
/* add giftvoucher_shipping_hidden_tax_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftvoucher_shipping_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftvoucher_shipping_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'Giftvoucher Shipping Hidden Tax Amount',
        )
    );
}
/* add giftcredit_base_shipping_hidden_tax_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftcredit_base_shipping_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftcredit_base_shipping_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Base Shipping Hidden Tax Amount',
        )
    );
}
/* add giftcredit_shipping_hidden_tax_amount to sales/order table */
if(!$connection->tableColumnExists($this->getTable('sales/order'), 'giftcredit_shipping_hidden_tax_amount')) {
    $connection->addColumn(
        $this->getTable('sales/order'),
        'giftcredit_shipping_hidden_tax_amount',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => '12,4',
            'default' => '0.0000',
            'nullable' => false,
            'comment' => 'GiftCredit Shipping Hidden Tax Amount',
        )
    );
}
/* add giftcard_template_id to giftvoucher/template table */
if(!$connection->tableColumnExists($this->getTable('giftvoucher/template'), 'giftcard_template_id')) {
    $connection->addColumn(
        $this->getTable('giftvoucher/template'),
        'giftcard_template_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 11,
            'nullable' => false,
            'comment' => 'GiftCard Template Id',
        )
    );
}
/* add giftcard_template_image to giftvoucher/template table */
if(!$connection->tableColumnExists($this->getTable('giftvoucher/template'), 'giftcard_template_image')) {
    $connection->addColumn(
        $this->getTable('giftvoucher/template'),
        'giftcard_template_image',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'default' => null,
            'nullable' => true,
            'comment' => 'GiftCard Template Image',
        )
    );
}
/* add timezone_to_send to giftvoucher table */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'timezone_to_send')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'timezone_to_send',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 100,
            'default' => null,
            'nullable' => true,
            'comment' => 'Time Zone to Send',
        )
    );
}
/* add day_store to giftvoucher table */
if(!$connection->tableColumnExists($this->getTable('giftvoucher'), 'day_store')) {
    $connection->addColumn(
        $this->getTable('giftvoucher'),
        'day_store',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
            'length' => null,
            'default' => null,
            'nullable' => true,
            'comment' => 'Day Stpre',
        )
    );
}

$model = Mage::getModel('giftvoucher/gifttemplate');
//Simple template
$data = array();


$data[0]['template_name'] = Mage::helper('giftvoucher')->__('Amazon Gift Card Style');
$data[0]['style_color'] = '#DC8C71';
$data[0]['text_color'] = '#949392';
$data[0]['caption'] = Mage::helper('giftvoucher')->__('Gift Card');
$data[0]['notes'] = '';
$data[0]['images'] = 'default.png,giftcard_amazon_01.png,giftcard_amazon_02.png,giftcard_amazon_03.png,'
    . 'giftcard_amazon_04.png,giftcard_amazon_05.png,giftcard_amazon_06.png,giftcard_amazon_07.png,'
    . 'giftcard_amazon_08.png,giftcard_amazon_09.png,giftcard_amazon_10.png,giftcard_amazon_11.png,'
    . 'giftcard_amazon_12.png,giftcard_amazon_13.png,giftcard_amazon_14.png,giftcard_amazon_15.png,'
    . 'giftcard_amazon_16.png,giftcard_amazon_17.png,giftcard_amazon_18.png';
$data[0]['design_pattern'] = Magestore_Giftvoucher_Model_Designpattern::PATTERN_AMAZON;

foreach ($data as $template) {
    $model->setData($template);
    try {
        $model->save();
    } catch (Exception $exc) {

    }
}

/**
 * create giftvoucher_giftcodeset table
 */
$table  = $installer->getConnection()
    ->newTable($installer->getTable('giftvoucher_giftcodeset'))
    ->addColumn(
        'set_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Set Id'
    )
    ->addColumn(
        'set_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false,'default' => ''),
        'Set Name'
    )
    ->addColumn(
        'set_qty',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array('default' => 0),
        'Set Qty'
    );
$installer->getConnection()->createTable($table);

/* add set_id to giftvoucher table */
if(!$connection->tableColumnExists($this->getTable('giftvoucher/giftvoucher'), 'set_id')) {
    $connection->addColumn(
        $this->getTable('giftvoucher/giftvoucher'),
        'set_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 11,
            'default' => null,
            'nullable' => true,
            'comment' => 'Gift Code set Id',
        )
    );
}
/* add used to giftvoucher table */
if(!$connection->tableColumnExists($this->getTable('giftvoucher/giftvoucher'), 'used')) {
    $connection->addColumn(
        $this->getTable('giftvoucher/giftvoucher'),
        'used',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length' => 1,
            'default' => null,
            'nullable' => true,
            'comment' => 'Used',
        )
    );
}
$data = array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'select',
    'label' => 'Select The Gift Code Sets ',
    'backend' => '',
    'frontend' => '',
    'source' => 'Magestore_Giftvoucher_Model_GiftCodeSetOptions',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 100,
    'apply_to' => 'giftvoucher',
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
);
$setup->addAttribute('catalog_product', 'gift_code_sets', $data);

$installer->endSetup();