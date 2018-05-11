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

class Magestore_Webpos_Block_Adminhtml_Report_Filter_Form_Location extends Magestore_Webpos_Block_Adminhtml_Report_Filter_Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');
        $locationList = array(0 => __('Please select location'));
        $locationCollection = Mage::getSingleton('webpos/userlocation')->getCollection();

        foreach ($locationCollection as $location) {
            $locationList[$location->getLocationId()] = $location->getDisplayName();
        }

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            $fieldset->addField('period_type', 'select', array(
                'name'       => 'period_type',
                'options'    => $locationList,
                'label'      => Mage::helper('webpos')->__('Select Location'),
            ));

        }

        return $this;
    }
}
