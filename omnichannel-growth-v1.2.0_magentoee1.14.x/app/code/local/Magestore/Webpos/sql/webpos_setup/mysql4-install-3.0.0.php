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
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('webpos_user')};

CREATE TABLE {$this->getTable('webpos_user')} (
    `user_id` int(11) unsigned NOT NULL auto_increment,
    `store_ids` varchar(255) NOT NULL default '',
    `username` varchar(255) NOT NULL default '',
    `password` varchar(255) NOT NULL default '',
    `display_name` text,
    `email` text,
    `monthly_target` text,
    `customer_group` VARCHAR( 255 ) default 'all',
    `location_id` int(11) NULL,
    `till_ids` VARCHAR( 255 ) default 'all',
    `role_id` int(11) NULL,
    `seller_id` int(11) NULL,
    `status` smallint(6) NOT NULL default '1',
    `auto_logout` tinyint(2) DEFAULT 0,
    `can_use_sales_report` smallint(6) NULL,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('webpos_user_location')};

CREATE TABLE {$this->getTable('webpos_user_location')} (
    `location_id` int(11) unsigned NOT NULL auto_increment,
    `display_name` text,
    `address` text,
    `description` text,
    PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('webpos_role')};

CREATE TABLE {$this->getTable('webpos_role')} (
    `role_id` int(11) unsigned NOT NULL auto_increment,
    `display_name` text,
    `permission_ids` text,
    `description` text,
    `maximum_discount_percent` VARCHAR( 255 )  default '',
    `active` tinyint(1) default 1,
    PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('webpos_order_payment')};
  CREATE TABLE {$this->getTable('webpos_order_payment')} (
  `payment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `till_id` varchar(20) DEFAULT '0',
  `base_payment_amount` float DEFAULT '0',
  `payment_amount` float DEFAULT '0',
  `base_real_amount` float DEFAULT '0',
  `real_amount` float DEFAULT '0',
  `method` varchar(255),
  `method_title` varchar(255),
  `invoice_id` varchar(255),
  `reference_number` varchar(255),
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;


DROP TABLE IF EXISTS {$this->getTable('webpos_api_session')};
  CREATE TABLE {$this->getTable('webpos_api_session')} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL DEFAULT 0,
  `current_quote_id` int(11) NULL DEFAULT 0,
  `current_store_id` int(11) NULL DEFAULT 0,
  `current_till_id` int(11) NULL DEFAULT 0,
  `logged_date` datetime DEFAULT NULL,
  `session_id` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

DROP TABLE IF EXISTS {$this->getTable('webpos_till')};
  
    CREATE TABLE {$this->getTable('webpos_till')} (
      `till_id` int(11) unsigned NOT NULL auto_increment,
      `till_name` varchar(255) NOT NULL default '',
      `location_id` int(11) NULL,
      `status` smallint(6) NOT NULL default '1',
      PRIMARY KEY (`till_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('webpos_till_transaction')};
  
    CREATE TABLE {$this->getTable('webpos_till_transaction')} (
      `id` int(11) unsigned NOT NULL auto_increment,
      `till_id` int(11) unsigned NOT NULL default '0',
      `staff_id` int(11) unsigned NOT NULL default '0',
      `order_increment_id` varchar(255) NULL default '0',
      `created_at` datetime NULL default NULL,
      `amount` float DEFAULT '0',
      `base_amount` float DEFAULT '0',
      `transaction_currency_code` varchar(255) NOT NULL,
      `base_currency_code` varchar(255) NOT NULL,
      `note` text NULL,
      `is_manual` smallint(6) NOT NULL default '1',
      `is_opening` smallint(6) NOT NULL default '0',
      `status` smallint(6) NOT NULL default '1',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('webpos_zreport')};
  
    CREATE TABLE {$this->getTable('webpos_zreport')} (
      `id` int(11) unsigned NOT NULL auto_increment,
      `till_id` int(11) unsigned NOT NULL default '0',
      `staff_id` int(11) unsigned NOT NULL default '0',
      `opened_at` datetime DEFAULT NULL,
      `closed_at` datetime DEFAULT NULL,
      `opening_amount` float DEFAULT '0',
      `base_opening_amount` float DEFAULT '0',
      `closed_amount` float DEFAULT '0',
      `base_closed_amount` float DEFAULT '0',
      `cash_left` float DEFAULT '0',
      `base_cash_left` float DEFAULT '0',
      `cash_added` float DEFAULT '0',
      `base_cash_added` float DEFAULT '0',
      `cash_removed` float DEFAULT '0',
      `base_cash_removed` float DEFAULT '0',
      `cash_sale` float DEFAULT '0',
      `base_cash_sale` float DEFAULT '0',
      `report_currency_code` varchar(255) NOT NULL,
      `base_currency_code` varchar(255) NOT NULL,
      `sale_by_payments` text NULL,
      `sales_summary` text NULL,
      `note` text NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$webposHelper = Mage::helper("webpos");

$webposHelper->addAdditionalFields();

Mage::helper("webpos/config")->generateGuestCustomerAccount();

$webposHelper->addWebposVisibilityAttribute();

$installer->endSetup();
