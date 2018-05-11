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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$setup->addAttribute('catalog_product', 'credit_rate', $arrayName = array(
    'group' => 'Prices',
    'type' => 'decimal',
    'input' => 'text',
    'label' => 'Credit Rate',
    'frontend_class' => 'validate-number',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '1.0',
    'sort_order' => 102,
    'note' => 'For example: 1.5'
));

$creditRate = Mage::getModel('catalog/resource_eav_attribute')->load($setup->getAttributeId('catalog_product', 'credit_rate'));

$creditRate->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'apply_to' => array('customercredit'),
    'is_configurable' => 1,
    'is_searchable' => 1,
    'is_visible_in_advanced_search' => 1,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
    'backend_type' => 'text',
))->save();

$setup->addAttribute('catalog_product', 'credit_value', array(
    'group' => 'Prices',
    'type' => 'text',
    'input' => 'text',
    'frontend_class' => 'disabled',
    'label' => 'Credit Value',
    'apply_to' => array('customercredit'),
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 0,
    'is_visible_on_front' => 0,
    'required' => 0,
    'user_defined' => 0,
    'is_searchable' => 1,
    'is_filterable' => 0,
    'is_comparable' => 0,
    'position' => 2,
    'unique' => 0,
    'is_global' => '',
));
$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'credit_value');
$attribute->save();
$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'credit_amount');

if ($attributeId) {
    $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
    $attribute->setFrontendLabel('Price')->save();
}
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'customercredit_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_customercredit_discount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'customercredit_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_customercredit_discount', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'customercredit_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_customercredit_discount', 'decimal(12,4) NULL');

$installer->endSetup();
