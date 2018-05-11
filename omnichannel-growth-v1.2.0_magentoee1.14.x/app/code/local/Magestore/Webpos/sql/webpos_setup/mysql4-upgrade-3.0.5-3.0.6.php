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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

$installer = $this;

$installer->startSetup();

$webposHelper = Mage::helper("webpos");

if (!$webposHelper->columnExist($this->getTable('webpos_till'), 'user_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_till')} ADD `user_id` int(11) default NULL ; ");
}

if (!$webposHelper->columnExist($this->getTable('webpos_till'), 'store_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_till')} ADD `store_id` smallint(5) default 0 ; ");
}

if (!$webposHelper->columnExist($this->getTable('webpos_user'), 'pin')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_user')} ADD `pin` VARCHAR (6) default '0000' ; ");
}

if (!$webposHelper->columnExist($this->getTable('sales/quote_item'), 'ordered_warehouse_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/quote_item')} ADD `ordered_warehouse_id` int(11) default 0 ; ");
}

if (!$webposHelper->columnExist($this->getTable('sales/order_item'), 'ordered_warehouse_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/order_item')} ADD `ordered_warehouse_id` int(11) default 0 ; ");
}

if (!$webposHelper->columnExist($this->getTable('sales/quote_address'), 'rewardpoints_earn')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/quote_address')} ADD `rewardpoints_earn` int(11) ;");
}

if (!$webposHelper->columnExist($this->getTable('sales/quote_address'), 'rewardpoints_spent')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/quote_address')} ADD `rewardpoints_spent` int(11) ;");
}

if (!$webposHelper->columnExist($this->getTable('sales/quote_address'), 'rewardpoints_base_discount')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/quote_address')} ADD `rewardpoints_base_discount` decimal(12,4) default 0 ;");
}

if (!$webposHelper->columnExist($this->getTable('sales/quote_address'), 'rewardpoints_base_amount')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/quote_address')} ADD `rewardpoints_base_amount` decimal(12,4) default 0 ;");
}

if (!$webposHelper->columnExist($this->getTable('sales/quote_address'), 'rewardpoints_discount')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/quote_address')} ADD `rewardpoints_discount` decimal(12,4)default 0 ;");
}

if (!$webposHelper->columnExist($this->getTable('sales/quote_address'), 'rewardpoints_amount')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/quote_address')} ADD `rewardpoints_amount` decimal(12,4)default 0 ;");
}

if (!$webposHelper->columnExist($this->getTable('webpos_zreport'), 'status')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_zreport')} ADD `status` smallint(1) default 0 ;");
}

if (!$webposHelper->columnExist($this->getTable('webpos_zreport'), 'closed_at')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_zreport')} ADD `closed_at` datetime;");
}

if (!$webposHelper->columnExist($this->getTable('webpos_zreport'), 'opened_note')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_zreport')} ADD `opened_note` text;");
}

if (!$webposHelper->columnExist($this->getTable('webpos_zreport'), 'shift_code')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_zreport')} ADD `shift_code` varchar(255);");
}

if (!$webposHelper->columnExist($this->getTable('sales/order'), 'shift_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/order')} ADD `shift_id` int(11);");
}

if (!$webposHelper->columnExist($this->getTable('sales/quote'), 'shift_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('sales/quote')} ADD `shift_id` int(11);");
}

if (!$webposHelper->columnExist($this->getTable('webpos_order_payment'), 'shift_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_order_payment')} ADD `shift_id` int(11);");
}

if (!$webposHelper->columnExist($this->getTable('webpos_till_transaction'), 'shift_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_till_transaction')} ADD `shift_id` int(11);");
}

$installer->endSetup();