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
 * Barcodesuccess Edit Form Content Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_History_Edit_Tab_Form extends
    Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_History_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        if ( Mage::registry('history_data') ) {
            $data = Mage::registry('history_data')->getData();
        } else {
            $historyId = $this->getRequest()->getParam('id');
            $data      = Mage::getModel('barcodesuccess/history')->load($historyId)->getData();
        }
        $data['created_by_username'] = Mage::getModel('admin/user')->load($data['created_by'])->getUsername();
        $fieldset                    = $form->addFieldset('barcodesuccess_form', array(
            'legend' => Mage::helper('barcodesuccess')->__('History Information'),
        ));
        $fieldset->addField('created_at', 'datetime', array(
            'label'  => Mage::helper('barcodesuccess')->__('Created At'),
            'name'   => 'created_at',
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ));
        $fieldset->addField('created_by_username', 'text', array(
            'label' => Mage::helper('barcodesuccess')->__('Created By'),
            'name'  => 'created_by_username',
        ));
        $fieldset->addField('reason', 'text', array(
            'label' => Mage::helper('barcodesuccess')->__('Reason'),
            'name'  => 'reason',
        ));
        $form->setReadonly(true, true);
        $form->setValues($data);
        return parent::_prepareForm();
    }
}