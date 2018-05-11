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
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Edit Block
 *
 * @category     Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_History_Edit extends
    Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_History_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'barcodesuccess';
        $this->_controller = 'adminhtml_history';
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_removeButton('reset');

        $printUrl = Mage::helper('adminhtml')->getUrl('adminhtml/barcodesuccess_barcode_print/index',
                                                      array(
                                                          'historyId' => $this->getHistoryId(),
                                                      ));
        $this->_addButton('generate', array(
            'label'   => Mage::helper('barcodesuccess')->__('Print Barcode'),
            'onclick' => "setLocation('$printUrl')",
            'class'   => '',
        ));
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('barcodesuccess')->__('Barcode Creating History Details');
    }

    /**
     * @return mixed
     */
    protected function getHistoryId()
    {
        return $this->getRequest()->getParam('id');
    }
}