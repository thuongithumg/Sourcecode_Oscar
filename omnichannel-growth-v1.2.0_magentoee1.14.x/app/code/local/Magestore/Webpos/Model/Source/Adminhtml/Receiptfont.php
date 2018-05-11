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

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Model_Source_Adminhtml_Receiptfont {

    protected $_allowFonts = array();

    public function __construct() {
        $this->_allowFonts = array('monospace' => Mage::helper('webpos')->__('Monospace'), 'sans-serif'=> Mage::helper('webpos')->__('Sans-serif'));
    }

    public function toOptionArray() {
        if (!count($this->_allowFonts))
            return;

        $options = array();
        foreach ($this->_allowFonts as $value => $label) {
            $options[] = array('value' => $value, 'label' => $label);
        }

        return $options;
    }

    public function getAllowFonts() {
        return $this->_allowFonts;
    }

}