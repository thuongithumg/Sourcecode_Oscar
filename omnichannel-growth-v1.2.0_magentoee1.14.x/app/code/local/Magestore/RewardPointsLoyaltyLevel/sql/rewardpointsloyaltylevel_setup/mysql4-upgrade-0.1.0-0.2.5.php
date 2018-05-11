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
$installer->getConnection()->changeColumn($this->getTable('rewardpointsloyaltylevel/loyaltylevel'), 'minimum_points', 'condition_value','int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('rewardpointsloyaltylevel/loyaltylevel'), 'auto_join', 'smallint(3) UNSIGNED NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('rewardpointsloyaltylevel/loyaltylevel'), 'condition_type', 'smallint(3) UNSIGNED NOT NULL default 0 AFTER `description`');
$installer->getConnection()->addColumn($this->getTable('rewardpointsloyaltylevel/loyaltylevel'), 'priority', 'smallint(3) UNSIGNED NOT NULL default 0');

$installer->endSetup();