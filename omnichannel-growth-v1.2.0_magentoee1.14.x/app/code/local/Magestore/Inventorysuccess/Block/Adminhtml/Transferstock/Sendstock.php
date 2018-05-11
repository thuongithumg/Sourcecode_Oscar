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
 * Inventorysuccess Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_transferstock_sendstock';
        $this->_blockGroup = 'inventorysuccess';
        $this->_headerText = Mage::helper('inventorysuccess')->__('Manage Stock Sending');
        $this->_addButtonLabel = Mage::helper('inventorysuccess')->__('New Stock Sending');
        parent::__construct();
    }
}