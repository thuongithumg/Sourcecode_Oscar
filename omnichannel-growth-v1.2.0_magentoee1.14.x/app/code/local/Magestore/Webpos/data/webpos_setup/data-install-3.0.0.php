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
    'webpos_user_location'    => array(
        'display_name' => 'Default Location',
        'address' => 'Default Location Address',
        'description' => 'Default Location'
    ),
    'webpos_role'    => array(
        'display_name' => 'admin',
        'permission_ids' => '1',
        'maximum_discount_percent' => '',
        'description' => '',
        'active' => '1'
    ),
    'webpos_user' => array(
        'username'   => $admin->getUsername() ?  $admin->getUsername() : 'admin',
        'password'   => $admin->getPassword() ?  $admin->getPassword() : 'cd636a34eacb99ed2361afa89cf915a1:CWUAOOUfGuwxEIhokeINhyhKws774FnU', // admin123
        'display_name'   => $admin->getFirstname() ?  $admin->getFirstname() : 'Admin',
        'email'   => $admin->getEmail() ?  $admin->getEmail() : 'admin@example.com',
        'monthly_target'   => '1000',
        'customer_group'   => 'all',
        'location_id'   => '1',
        'till_ids'   => 'all',
        'role_id'   => '1',
        'status'   => '1',
        'auto_logout'   => '0',
        'can_use_sales_report'   => '1'
    ),
    'webpos_till' => array(
      'till_name' => 'Default Cash Drawer',
      'location_id' => '1',
      'status' => '1'
    )
);

foreach ($data as $key => $value) {
    $installer->getConnection()->insert($installer->getTable($key), $value);
}
