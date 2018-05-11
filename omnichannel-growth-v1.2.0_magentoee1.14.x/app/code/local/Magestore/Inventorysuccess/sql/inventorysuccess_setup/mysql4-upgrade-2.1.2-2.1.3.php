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

/* add stocktaking_reason to os_stocktaking_product */
if(!$connection->tableColumnExists($installer->getTable('os_stocktaking_product'), 'stocktaking_reason')) {
    $connection->addColumn($installer->getTable('os_stocktaking_product'),'stocktaking_reason', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 255,
        'unsigned' => true,
        'comment'   => 'Stocktaking reason'
    ));
}
/* add qty_returned to os_transferstock_product */
if(!$connection->tableColumnExists($installer->getTable('os_transferstock_product'), 'qty_returned')) {
    $connection->addColumn($installer->getTable('os_transferstock_product'),'qty_returned', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'nullable'  => false,
        'default' => '0.0000',
        'unsigned' => true,
        'comment'   => 'Qty Returned'
    ));
}
$installer->endSetup();

