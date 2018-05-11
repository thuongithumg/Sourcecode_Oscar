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
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('rewardpoints/customer'), 'loyalty_notification', "smallint(5) NOT NULL default 0");
$installer->getConnection()->addColumn($installer->getTable('rewardpoints/customer'), 'loyalty_expire', "datetime NULL");
/**
 * create rewardpointsloyaltylevel table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('rewardpointsloyaltylevel/loyaltylevel')};

CREATE TABLE {$this->getTable('rewardpointsloyaltylevel/loyaltylevel')} (
  `level_id` int(11) unsigned NOT NULL auto_increment,
  `customer_group_id` smallint(5) unsigned NOT NULL,
  `level_name` varchar(150) NOT NULL default '',
  `description` text(500) NOT NULL default '',
  `minimum_points` int(11) NOT NULL default 0,
  `demerit_points` int(11) NOT NULL default 0,
  `retention_period` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `status` smallint(1) NOT NULL default 2,
   KEY `FK_REWARDPOINTS_LOYALTY_GROUP` (`customer_group_id`),
   CONSTRAINT `FK_REWARDPOINTS_LOYALTY_GROUP` FOREIGN KEY (`customer_group_id`) REFERENCES {$this->getTable('customer/customer_group')}(`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
//save loyalty by groups
//$groups = Mage::getModel('customer/group')->getCollection();
//foreach ($groups as $group) {
//    $loyalty = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($group->getId(), 'customer_group_id');
//    $loyalty->setLevelName($group->getCustomerGroupCode());
//    $loyalty->setCustomerGroupId($group->getId());
//    if($group->getId() == 0 || $group->getId() == 1) $loyalty->setStatus(Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Status::STATUS_DISABLED);
//    try {
//        $loyalty->save();
//    } catch (Exception $exc) {
//        Mage::log($exc->getMessage());
//    }
//}

//   update rewardpoints_rate table
//$installer->getConnection()->addColumn($installer->getTable('rewardpoints_rate'), 'level_id', "int(11) NULL");
//$installer->getConnection()->addColumn($installer->getTable('rewardpoints_rate'), 'use_level', "smallint(5) NOT NULL default 0");
//$installer->getConnection()->addColumn($installer->getTable('rewardpoints_customer'), 'loyalty_notification', "smallint(5) NOT NULL default 0");
//$installer->getConnection()->addColumn($installer->getTable('rewardpoints_customer'), 'level_id', "int(11) NULL");
//$installer->getConnection()->addColumn($installer->getTable('rewardpoints_customer'), 'loyalty_expire', "datetime NULL");
//if (Mage::getConfig()->getModuleConfig('Magestore_RewardPointsRule')->is('active', 'true')) {
////   update rewardpoints_earning_catalog table
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_earning_product'), 'level_id', "int(11) NULL");
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_earning_catalog'), 'level_id', "int(11) NULL");
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_earning_catalog'), 'use_level', "smallint(5) NOT NULL default 0");
////    update rewardpoints_earning_sales table
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_earning_sales'), 'level_id', "int(11) NULL");
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_earning_sales'), 'use_level', "smallint(5) NOT NULL default 0");
////    update rewardpoints_spending_catalog table
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_spending_catalog'), 'level_id', "int(11) NULL");
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_spending_catalog'), 'use_level', "smallint(5) NOT NULL default 0");
////    update rewardpoints_spending_sales table
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_spending_sales'), 'level_id', "int(11) NULL");
//    $installer->getConnection()->addColumn($installer->getTable('rewardpoints_spending_sales'), 'use_level', "smallint(5) NOT NULL default 0");
//}
//$installer->getConnection()->addColumn($installer->getTable('catalogrule'), 'loyalty_level_use', "tinyint(1) NULL");
//$installer->getConnection()->addColumn($installer->getTable('catalogrule'), 'loyalty_level_id', "int(11) NULL");
//$installer->getConnection()->addColumn($installer->getTable('salesrule'), 'loyalty_level_use', "tinyint(1) NULL");
//$installer->getConnection()->addColumn($installer->getTable('salesrule'), 'loyalty_level_id', "int(11) NULL");
//// update customer group
//$installer->getConnection()->addColumn($this->getTable('customer/group'), 'level_id', 'int(11) NULL');

$installer->endSetup();

