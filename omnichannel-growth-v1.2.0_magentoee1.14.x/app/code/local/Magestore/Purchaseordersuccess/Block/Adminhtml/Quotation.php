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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Quotation extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_quotation';
        $this->_blockGroup = 'purchaseordersuccess';
        $this->_headerText = $this->__('Manage Quotations');
        $this->_addButtonLabel = $this->__('Create Quotation');
        parent::__construct();
        if (!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/quotation/create'))
            $this->removeButton('add');
    }

    public function getCreateUrl()
    {
        return $this->getUrl(
            '*/*/new',
            array('type' => Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type::TYPE_QUOTATION)
        );
    }
}