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
 * Sales Adminhtml report filter form order
 *
 * @category   Magestore
 * @package    Webpos
 * @author     Magestore Team
 */

class Magestore_Webpos_Block_Adminhtml_Report_Filter_Form_Staff extends Magestore_Webpos_Block_Adminhtml_Report_Filter_Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');
        $staffList = array(0 => __('Please select a staff'));
        $staffCollection = Mage::getModel('webpos/user')->getCollection();
        if ($staffCollection->getSize() > 0) {
            foreach ($staffCollection as $staff) {
                $staffList[$staff->getId()] = $staff->getDisplayName();
            }
        }

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {

            $fieldset->addField('period_type', 'select', array(
                'name'       => 'period_type',
                'options'    => $staffList,
                'label'      => Mage::helper('webpos')->__('Select Staff'),
            ));

        }

        return $this;
    }
}
