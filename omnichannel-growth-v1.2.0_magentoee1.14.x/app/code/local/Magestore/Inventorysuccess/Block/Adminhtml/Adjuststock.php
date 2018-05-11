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
class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_adjuststock';
        $this->_blockGroup = 'inventorysuccess';
        $this->_headerText = Mage::helper('inventorysuccess')->__('Manage Stock Adjustments');
        $this->_addButtonLabel = Mage::helper('inventorysuccess')->__('Add Stock Adjustment');
        parent::__construct();
    }
}