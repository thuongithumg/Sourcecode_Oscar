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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

$installer = $this;

$installer->startSetup();

$installer->run("

	DROP TABLE IF EXISTS {$this->getTable('storepickup_order')};

	CREATE TABLE {$this->getTable('storepickup_order')}  (
	  `storeorder_id` int(11) NOT NULL auto_increment,
	  `order_id` int(11) NOT NULL default '0',
	  `store_id` int(11) NOT NULL default '0',
	  `shipping_date` date NOT NULL,
	  `shipping_time` time NULL,
	  PRIMARY KEY  (`storeorder_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

");



$installer->endSetup(); 