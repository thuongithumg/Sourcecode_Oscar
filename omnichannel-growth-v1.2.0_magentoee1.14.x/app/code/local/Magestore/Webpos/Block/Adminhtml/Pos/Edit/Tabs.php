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

/**
 * Marketingautomation Edit Tabs Block
 *
 * @category    Magestore
 * @package     Magestore_Marketingautomation
 * @author      Magestore Developer
 */
class Magestore_Webpos_Block_Adminhtml_Pos_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('pos_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('webpos')->__('Pos Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Marketingautomation_Block_Adminhtml_Marketingautomation_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('webpos')->__('Pos Information'),
            'title'     => Mage::helper('webpos')->__('Pos Information'),
            'content'   => $this->getLayout()
                ->createBlock('webpos/adminhtml_pos_edit_tab_form')
                ->toHtml(),
        ));
        $this->addTab('closed_sessions', array(
            'label' => Mage::helper('webpos')->__('Closed Session'),
            'title' => Mage::helper('webpos')->__('Closed Session'),
            'url' => $this->getUrl('*/*/sessions', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
            'class' => 'ajax'
        ));
        $this->addTab(
            'webpos_current_session_detail',
            array(
                'label' => Mage::helper('webpos')->__('Current Sessions Detail'),
                'title' => Mage::helper('webpos')->__('Current Sessions Detail'),
                'content' => $this->getLayout()->createBlock('webpos/adminhtml_pos_edit_tab_detail')
                    ->setTemplate('webpos/detail.phtml')
                    ->toHtml()
            )
        );
        return parent::_beforeToHtml();
    }
}