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

/* add qty_returned to os_transferstock_product */
if(!$connection->tableColumnExists($installer->getTable('os_transferstock_product'), 'qty_returned')) {
    $connection->addColumn($installer->getTable('os_transferstock_product'),'qty_returned', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'nullable'  => false,
        'length'    => '12,4',
        'comment'   => 'Qty Returned',
        'default'   => 0
    ));
}
/* add qty_returned to os_transferstock */
if(!$connection->tableColumnExists($installer->getTable('os_transferstock'), 'qty_returned')) {
    $connection->addColumn($installer->getTable('os_transferstock'),'qty_returned', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'nullable'  => false,
        'length'    => '12,4',
        'comment'   => 'Qty Returned',
        'default'   => 0
    ));
}

$installer->endSetup();


