<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_Template extends
    Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Template constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_template';
        $this->_blockGroup = 'barcodesuccess';
        $this->_headerText = Mage::helper('barcodesuccess')->__('Manage Barcode Label Templates');
        $this->_addButtonLabel = Mage::helper('barcodesuccess')->__('Add New Template');
        parent::__construct();
    }
}