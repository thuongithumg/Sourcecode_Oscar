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

$installer->startSetup();

$connection = $installer->getConnection();

/* add warehouse_id to sales_flat_order_item */
if(!$connection->tableColumnExists($installer->getTable('sales/order_item'), 'warehouse_id')) {
    $connection->addColumn($installer->getTable('sales/order_item'),'warehouse_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'length'    => 11,
        'comment'   => 'Warehouse Id'
    ));
}

/* add warehouse_id to sales_flat_shipment_item */
if(!$connection->tableColumnExists($installer->getTable('sales/shipment_item'), 'warehouse_id')) {
    $connection->addColumn($installer->getTable('sales/shipment_item'),'warehouse_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'length'    => 11,
        'comment'   => 'Warehouse Id'
    ));
}

/* add warehouse_id to sales_flat_creditmemo_item */
if(!$connection->tableColumnExists($installer->getTable('sales/creditmemo_item'), 'warehouse_id')) {
    $connection->addColumn($installer->getTable('sales/creditmemo_item'),'warehouse_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'length'    => 11,
        'comment'   => 'Warehouse Id'
    ));
}

$installer->endSetup();


