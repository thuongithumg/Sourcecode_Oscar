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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 *
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Block_Adminhtml_Salesreport extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Reportsuccess_Block_Adminhtml_Salesreport constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_salesreport';
        $this->_blockGroup = 'salesreport';
        $this->_headerText = Mage::helper('reportsuccess')->__('Reports success');
        parent::__construct();
    }
}