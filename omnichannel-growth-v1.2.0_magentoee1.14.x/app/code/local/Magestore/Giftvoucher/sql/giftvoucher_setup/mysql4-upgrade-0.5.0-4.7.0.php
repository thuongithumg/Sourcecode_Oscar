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

$installer->getConnection()->changeColumn(
    $installer->getTable('giftvoucher/gifttemplate'),
    'design_pattern',
    'design_pattern',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => false,
        'comment' => 'Design Pattern'
    )
);

$giftCardTemplateArray = array(
    1 => 'left-gift-card',
    2 => 'top-gift-card',
    3 => 'center-gift-card',
    4 => 'simple-gift-card',
    5 => 'amazon-giftcard-01'
);

$giftCardTemplateCollection = Mage::getModel('giftvoucher/gifttemplate')->getCollection();

foreach ($giftCardTemplateCollection as $giftCardTemplate) {
    $oldPattern = $giftCardTemplate->getDesignPattern();
    if ($oldPattern) {
        if (isset($giftCardTemplateArray[$oldPattern])) {
            $giftCardTemplate->setDesignPattern($giftCardTemplateArray[$oldPattern]);
            $giftCardTemplate->save();
        }
    }
}
$installer->endSetup();
