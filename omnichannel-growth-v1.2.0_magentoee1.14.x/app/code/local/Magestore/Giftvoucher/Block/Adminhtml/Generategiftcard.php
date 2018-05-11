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

/**
 * Adminhtml Giftvoucher Generategiftcard Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Block_Adminhtml_Generategiftcard extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Generategiftcard constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_generategiftcard';
        $this->_blockGroup = 'giftvoucher';
        $this->_headerText = Mage::helper('giftvoucher')->__('Gift Code Pattern Manager');
        $this->_addButtonLabel = Mage::helper('giftvoucher')->__('Add Gift Code Pattern');
        parent::__construct();
    }

}
