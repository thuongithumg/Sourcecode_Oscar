<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Dataflow_Catalog_Rewrite_Convert_Gui_Edit_Tab_Wizard extends Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard
{

    public function getAttributes($entityType)
    {
        if (!isset($this->_attributes[$entityType])) {
            switch ($entityType) {
                case 'product':
                    $attributes = Mage::getSingleton('catalog/convert_parser_product')
                        ->getExternalAttributes();

                    /** add more qty and location field */
                    /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
                    $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
                    $warehouseIds = array();
                    /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
                    foreach ($warehouseCollection as $warehouse) {
                        $attributes['qty_'.$warehouse->getId()] = 'qty_'.$warehouse->getId();
                        $attributes['location_'.$warehouse->getId()] = 'location_'.$warehouse->getId();
                    }

                    break;

                case 'customer':
                    $attributes = Mage::getSingleton('customer/convert_parser_customer')
                        ->getExternalAttributes();
                    break;
            }

            array_splice($attributes, 0, 0, array(''=>$this->__('Choose an attribute')));
            $this->_attributes[$entityType] = $attributes;
        }
        return $this->_attributes[$entityType];
    }

}

