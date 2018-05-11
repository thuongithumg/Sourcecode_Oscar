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

$installer->getConnection()->addColumn(
    $installer->getTable('webpos_shift'),
    'refund_amount',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'nullable'  => false,
        'default'   => 0,
        'comment'   => 'Refund Amount'
    )
);

$installer->getConnection()->addColumn(
    $installer->getTable('webpos_shift'),
    'base_refund_amount',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length'    => '12,4',
        'nullable'  => false,
        'default'   => 0,
        'comment'   => 'Base Refund Amount'
    )
);
$installer->endSetup();