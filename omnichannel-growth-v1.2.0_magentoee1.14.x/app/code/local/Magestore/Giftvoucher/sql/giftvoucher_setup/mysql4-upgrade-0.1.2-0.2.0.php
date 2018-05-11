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

$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$installer->startSetup();

$weight = Mage::getModel('catalog/resource_eav_attribute')->load($setup->getAttributeId('catalog_product', 'weight'));
$applyTo = explode(',', $weight->getData('apply_to'));
$applyTo[] = 'giftvoucher';
$weight->addData(array('apply_to' => $applyTo))->save();

$installer->endSetup();
