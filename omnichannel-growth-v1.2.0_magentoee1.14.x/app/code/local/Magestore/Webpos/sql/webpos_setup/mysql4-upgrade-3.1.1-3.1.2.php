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
if (!$webposHelper->columnExist($this->getTable('webpos_order_payment'), 'shift_id')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_order_payment')} ADD `shift_id` int(11);");
}
$installer->getConnection()->addColumn(
    $installer->getTable('webpos_cash_transaction'),
    'staff_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => true,
        'comment'   => 'staff_id'
    )
);
$installer->getConnection()->addColumn(
    $installer->getTable('webpos_cash_transaction'),
    'staff_name',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => '255',
        'nullable'  => true,
        'comment'   => 'staff_name'
    )
);
$installer->getConnection()->changeColumn(
    $installer->getTable('webpos_order_payment'),
    'shift_id',
    'shift_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => '255',
        'comment'   => 'Shift Id'
    )
);
$installer->getConnection()->changeColumn(
    $installer->getTable('sales/order'),
    'shift_id',
    'shift_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => '255',
        'comment'   => 'Shift Id'
    )
);
$installer->endSetup();
Mage::helper('webpos')->convertDatabase();