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

/**
 * add more column for giftvoucher table
 */
$connection = $installer->getConnection();
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
            'length' => 127,
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
            'length' => 127,
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
        11,
        array('nullable' => false,'default' => ''),
        'Template Name'
    )->addColumn(
        'style_color',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        11,
        array('nullable' => false,'default' => ''),
        'Style Color'
    )->addColumn(
        'text_color',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        11,
        array('nullable' => false,'default' => ''),
        'Text Color'
    )->addColumn(
        'caption',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        11,
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
/*
 * add giftvoucher.giftcard_template_id foreign key to giftcard_template.giftcard_template_id
 */

    $connection->addIndex(
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



$model = Mage::getModel('giftvoucher/gifttemplate');
//template 1
$data = array();
$data[0]['template_name'] = Mage::helper('giftvoucher')->__('Default Template 1');
$data[0]['style_color'] = '#DC8C71';
$data[0]['text_color'] = '#949392';
$data[0]['caption'] = Mage::helper('giftvoucher')->__('Gift Card');
$data[0]['notes'] = '';
$data[0]['images'] = 'default.png';
$data[0]['background_img'] = 'default.png';
$data[0]['design_pattern'] = Magestore_Giftvoucher_Model_Designpattern::PATTERN_LEFT;
//template 2
$data[1]['template_name'] = Mage::helper('giftvoucher')->__('Default Template 2');
$data[1]['style_color'] = '#FFFFFF';
$data[1]['text_color'] = '#636363';
$data[1]['caption'] = Mage::helper('giftvoucher')->__('Gift Card');
$data[1]['notes'] = '';
$data[1]['images'] = 'default.png';
$data[1]['background_img'] = 'default.png';
$data[1]['design_pattern'] = Magestore_Giftvoucher_Model_Designpattern::PATTERN_TOP;
//template 3
$data[2]['template_name'] = Mage::helper('giftvoucher')->__('Default Template 3');
$data[2]['style_color'] = '#FFFFFF';
$data[2]['text_color'] = '#A9A7A7';
$data[2]['caption'] = Mage::helper('giftvoucher')->__('Gift Card');
$data[2]['notes'] = '';
$data[2]['images'] = 'default.png';
$data[2]['background_img'] = 'default.png';
$data[2]['design_pattern'] = Magestore_Giftvoucher_Model_Designpattern::PATTERN_CENTER;
foreach ($data as $template) {
    $model->setData($template);
    try {
        $model->save();
    } catch (Exception $exc) {
        
    }
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
$installer->endSetup();
