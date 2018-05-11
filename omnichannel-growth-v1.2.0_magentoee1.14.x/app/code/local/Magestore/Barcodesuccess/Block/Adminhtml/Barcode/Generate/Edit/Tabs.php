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
 * Barcodesuccess Edit Tabs Block
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Generate_Edit_Tabs extends
    Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Generate_Edit_Tabs constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('barcode_generate_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('barcodesuccess')->__('Barcode Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'   => Mage::helper('barcodesuccess')->__('General Information'),
            'title'   => Mage::helper('barcodesuccess')->__('General Information'),
            'content' => $this->getLayout()
                              ->createBlock('barcodesuccess/adminhtml_barcode_generate_edit_tab_form')
                              ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}