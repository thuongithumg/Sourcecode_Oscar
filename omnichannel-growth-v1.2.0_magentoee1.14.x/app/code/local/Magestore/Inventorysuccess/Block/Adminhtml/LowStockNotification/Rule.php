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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_lowStockNotification_rule';
        $this->_blockGroup = 'inventorysuccess';
        $this->_headerText = $this->__('Manage Low Stock Notification Rules');
        $this->_addButtonLabel = $this->__('Add New Rule');
        if (Mage::registry('inventorysuccess_not_applied_rule')) {
            $this->addButton('apply', array(
                'class' => 'save',
                'label' => $this->__('Apply Rules'),
                'onclick' => sprintf("deleteConfirm(
                    '" . $this->__('Are you sure you want to apply all active rule?') . "', 
                    '%s'
                )", $this->getUrl('*/*/applyRule', array('_current' => true))
                ),
            ));
        }
        
        parent::__construct();
    }
}