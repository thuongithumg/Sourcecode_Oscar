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
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsloyaltylevel Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getRewardPointsLoyaltyLevelData()) {
            $data = Mage::getSingleton('adminhtml/session')->getRewardPointsLoyaltyLevelData();
            Mage::getSingleton('adminhtml/session')->setRewardPointsLoyaltyLevelData(null);
        } elseif (Mage::registry('rewardpointsloyaltylevel_data')) {
            $data = Mage::registry('rewardpointsloyaltylevel_data')->getData();
        }
        $fieldset = $form->addFieldset('rewardpointsloyaltylevel_form', array(
            'legend' => Mage::helper('rewardpointsloyaltylevel')->__('General information')
        ));

        if (Mage::registry('rewardpointsloyaltylevel_new')) {
            $form->addField('level_new', 'hidden', array(
                'name' => 'level_new',
                'value' => '1',
                    )
            );
            $optionHash = Mage::getSingleton('rewardpointsloyaltylevel/form_existornew')->getOptionHash();
            $levelFrom = $fieldset->addField('level_from', 'select', array(
                'label' => Mage::helper('rewardpointsloyaltylevel')->__('Create Level From'),
                'name' => 'level_from',
                'values' => $optionHash,
            ));
            if (count($optionHash) == 1) {
                $levelFrom->setDisabled(true);
                $form->addField('level_create_new', 'hidden', array(
                    'name' => 'level_create_new',
                    'value' => '1',
                        )
                );
            }
            $levelExist = $fieldset->addField('customer_group_id', 'select', array(
                'label' => Mage::helper('rewardpointsloyaltylevel')->__('Select Group'),
                'name' => 'customer_group_id',
                'required' => true,
                'values' => Mage::getSingleton('rewardpointsloyaltylevel/form_exist')->getOptionHash(),
            ));
			// 32 should be Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH ( fix for 1.6 ) 
            $validateClass = sprintf('required-entry validate-length maximum-length-%d', 32);
            $levelGroupCode = $fieldset->addField('customer_group_code', 'text', array(
                'name' => 'code',
                'label' => Mage::helper('customer')->__('Group Name'),
                'title' => Mage::helper('customer')->__('Group Name'),
                'note' => Mage::helper('customer')->__('Maximum length is %s characters', 32),
                'class' => $validateClass,
                'required' => true,
                    )
            );
            $levelTaxClass = $fieldset->addField('tax_class_id', 'select', array(
                'name' => 'tax_class',
                'label' => Mage::helper('customer')->__('Tax Class'),
                'title' => Mage::helper('customer')->__('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('tax/class_source_customer')->toOptionArray()
                    )
            );
            $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                            ->addFieldMap($levelFrom->getHtmlId(), $levelFrom->getName())
                            ->addFieldMap($levelExist->getHtmlId(), $levelExist->getName())
                            ->addFieldMap($levelGroupCode->getHtmlId(), $levelGroupCode->getName())
                            ->addFieldMap($levelTaxClass->getHtmlId(), $levelTaxClass->getName())
                            ->addFieldDependence(
                                    $levelExist->getName(), $levelFrom->getName(), Magestore_RewardPointsLoyaltyLevel_Model_Form_Existornew::FORM_EXIST)
                            ->addFieldDependence(
                                    $levelGroupCode->getName(), $levelFrom->getName(), Magestore_RewardPointsLoyaltyLevel_Model_Form_Existornew::FORM_NEW)
                            ->addFieldDependence(
                                    $levelTaxClass->getName(), $levelFrom->getName(), Magestore_RewardPointsLoyaltyLevel_Model_Form_Existornew::FORM_NEW)
            );
        } else {
            $fieldset->addField('level_name', 'text', array(
                'label' => Mage::helper('rewardpointsloyaltylevel')->__('Group Name'),
                'class' => 'required-entry',
                'required' => false,
                'name' => 'level_name',
                'disabled' => true,
            ));
        }
        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Description'),
            'title' => Mage::helper('rewardpointsloyaltylevel')->__('Description'),
            'required' => true,
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets' => false, 'add_images' => false))
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Status'),
            'name' => 'status',
            'values' => Mage::getSingleton('rewardpointsloyaltylevel/system_config_source_status')->getOptionHash(),
//            'disabled' => $data['customer_group_id'] == 0 ? 'disabled' : '',
        ));

        $fieldset->addField('auto_join', 'select', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Auto-join group'),
            'class' => 'required-entry',
            'name' => 'auto_join',
            'values' => Mage::getSingleton('rewardpointsloyaltylevel/system_config_source_status')->getOptionHash()
        ));

        $fieldset->addField('condition_type', 'select', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Condition Type'),
            'class' => 'required-entry',
            'name' => 'condition_type',
            'values' => Mage::getSingleton('rewardpointsloyaltylevel/system_config_source_Conditiontype')->getOptionHash()
        ));

        $fieldset->addField('condition_value', 'text', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Condition Value'),
            'class' => 'required-entry validate-number',
            'required' => true,
            'name' => 'condition_value',
//            'disabled' => $data['customer_group_id'] == 0 ? 'disabled' : '',
            'note' => 'The minimum number of earning points required to join group'
        ));

        $fieldset->addField('demerit_points', 'text', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Exchange points'),
            'class' => 'required-entry validate-number',
            'required' => true,
            'name' => 'demerit_points',
//            'disabled' => $data['customer_group_id'] == 0 ? 'disabled' : '',
            'note' => 'The number of points subtracted in exchange to join group'
        ));
        $fieldset->addField('retention_period', 'text', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Duration'),
            'class' => 'validate-number',
            'required' => false,
            'name' => 'retention_period',
            'note' => 'day(s). If empty or zero, there is no limitation.',
//            'disabled' => $data['customer_group_id'] == 0 ? 'disabled' : '',
        ));
        
        $fieldset->addField('priority', 'text', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Priority'),
            'class' => 'validate-number',
            'required' => false,
            'name' => 'priority',
            'note' => 'Higher priority Rate will be applied first',
        ));



        $form->setValues($data);
        return parent::_prepareForm();
    }

}
