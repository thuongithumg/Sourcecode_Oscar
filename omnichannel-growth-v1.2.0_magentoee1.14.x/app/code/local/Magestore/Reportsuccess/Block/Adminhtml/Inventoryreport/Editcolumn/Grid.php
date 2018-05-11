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
 * Class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Editcolumn_Grid
 */
use Magestore_Reportsuccess_Helper_Variable as Variable;
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Editcolumn_Grid
    extends Mage_Core_Block_Template
{
    protected $_helper;
    /**
     * @var array
     */
    protected $_mapping_field_name_column;
    /**
     * @var array
     */
    protected $_mapping_filed_name_column_dimensions;

    /**
     * Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Editcolumn_Grid constructor.
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('reportsuccess/variable');
        $this->_mapping_field_name_column =  $this->_helper->mappingFieldsName();
        $this->_mapping_filed_name_column_dimensions = $this->_helper->mappingDimentsionName();
    }
}