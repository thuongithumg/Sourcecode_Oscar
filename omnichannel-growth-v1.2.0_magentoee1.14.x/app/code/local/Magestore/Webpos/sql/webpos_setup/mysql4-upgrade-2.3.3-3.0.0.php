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

$installer->run("
  DROP TABLE IF EXISTS {$this->getTable('webpos_admin')};
  DROP TABLE IF EXISTS {$this->getTable('webpos_survey')};
  DROP TABLE IF EXISTS {$this->getTable('webpos_order')};
  DROP TABLE IF EXISTS {$this->getTable('webpos_products')};
  DROP TABLE IF EXISTS {$this->getTable('webpos_xreport')};
");
if (!$webposHelper->columnExist($this->getTable('webpos_user'), 'till_ids')) {
    $installer->run(" ALTER TABLE {$this->getTable('webpos_user')} ADD `till_ids` VARCHAR( 255 ) default 'all'; ");
}
$webposHelper->addNewTables();
$webposHelper->addAdditionalFields();
$webposHelper->addWebposVisibilityAttribute();

$installer->endSetup();