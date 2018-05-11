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

$installer->endSetup();
