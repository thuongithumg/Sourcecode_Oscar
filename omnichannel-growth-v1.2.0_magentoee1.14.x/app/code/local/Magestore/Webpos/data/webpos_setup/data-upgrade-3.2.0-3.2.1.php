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

/**
 * Webpos data install
 *
 * @category    Magestore_Mage
 * @package     Magestore_Webpos
 * @author      Magestore Core Team
 */

$installer = $this;

$_collection = Mage::getModel('admin/user')->getCollection();
$admin = $_collection->getFirstItem();

$data = array(
    'webpos_pos'    => array(
        'location_id' => '1',
        'pos_name' => 'Default Pos',
        'status' => '1'
    ),
);

foreach ($data as $key => $value) {
    $installer->getConnection()->insert($installer->getTable($key), $value);
}
$storeId = Mage::app()->getStore()->getId();
$installer->run(" UPDATE {$this->getTable('webpos_user_location')} SET `location_store_id` ='".$storeId."'  WHERE `location_id` = 1;");
