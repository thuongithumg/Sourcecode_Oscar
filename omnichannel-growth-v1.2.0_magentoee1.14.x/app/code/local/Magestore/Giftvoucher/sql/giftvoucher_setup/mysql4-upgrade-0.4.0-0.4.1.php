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
$connection = $installer->getConnection();
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

$installer->endSetup();

