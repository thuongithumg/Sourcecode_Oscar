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
 * Adminhtml Giftvoucher Customer Tab Credit Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Adminhtml_Customer_Tab_Credit extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_customerCredit;

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('creditgiftcard_fieldset', array(
            'legend' => Mage::helper('giftvoucher')->__('Gift Card Credit Information')
        ));

        $fieldset->addField('credit_balance', 'note', array(
            'label' => Mage::helper('giftvoucher')->__('Balance'),
            'title' => Mage::helper('giftvoucher')->__('Balance'),
            'text' => $this->getBalanceCredit(),
        ));
        $fieldset->addField('change_balance', 'text', array(
            'label' => Mage::helper('giftvoucher')->__('Change Balance'),
            'title' => Mage::helper('giftvoucher')->__('Change Balance'),
            'name' => 'change_balance',
            'note' => Mage::helper('giftvoucher')->__('Add or subtract customer\'s balance. For ex: 99 or -99.'),
        ));

        $block = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('giftvoucher/balancehistory.phtml');

        $form->addFieldset('balance_history_fieldset', array(
            'legend' => Mage::helper('giftvoucher')->__('Balance History')
        ))->setRenderer($block);


        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getCredit()
    {
        if (is_null($this->_customerCredit)) {
            $customerId = Mage::registry('current_customer')->getId();
            $this->_customerCredit = Mage::getModel('giftvoucher/credit')->getCreditByCustomerId($customerId);
        }
        return $this->_customerCredit;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('giftvoucher')->__('Gift Card Credit');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('giftvoucher')->__('Gift Card Credit');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('customer/manage/giftcredittab')
            && Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('customer/manage/giftcredittab')
            && Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Returns formatted Gift Card credit balance
     *
     * @return string
     */
    public function getBalanceCredit()
    {
        $currency = Mage::getModel('directory/currency')->load($this->getCredit()->getCurrency());
        return $currency->format($this->getCredit()->getBalance());
    }

}
