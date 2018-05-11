<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Staff\Staff\Edit\Tab;

/**
 * Class Form
 * @package Magestore\Webpos\Block\Adminhtml\Staff\Staff\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    )
    {
        $this->_objectManager = $objectManager;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
    }


    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('current_staff');

        $data = array();
        if ($model->getId()) {
            $data = $model->getData();
            if ($data['pos_ids']) {
                $data['pos_ids'] = explode(',', $data['pos_ids']);
            }

            if ($data['location_id']) {
                $data['location_id'] = explode(',', $data['location_id']);
            }
        } else {
            $data['pin'] = '0000';
        }
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Staff Information')));

        if ($model->getId()) {
            $fieldset->addField('staff_id', 'hidden', array('name' => 'staff_id'));
        }

        $fieldset->addField('username', 'text', array(
            'label' => __('User Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'username'
        ));
        if ((isset($data['staff_id']) && $data['staff_id']) || $this->getRequest()->getParam('id')) {
            $fieldset->addField('password', 'password', array(
                'label' => __('New Password'),
                'name' => 'new_password',
                'class' => 'input-text validate-admin-password',
            ));
            $fieldset->addField('password_confirmation', 'password', array(
                'label' => __('Password Confirmation'),
                'name' => 'password_confirmation',
                'class' => 'input-text validate-cpassword',
            ));
        } else {
            $fieldset->addField('password', 'password', array(
                'label' => __('Password'),
                'required' => true,
                'name' => 'password',
                'class' => 'input-text required-entry validate-admin-password',
            ));
            $fieldset->addField('password_confirmation', 'password', array(
                'label' => __('Password Confirmation'),
                'name' => 'password_confirmation',
                'required' => true,
                'class' => 'input-text required-entry validate-cpassword',
            ));
        }

        $fieldset->addField('display_name', 'text', array(
            'label' => __('Display Name'),
            'required' => true,
            'name' => 'display_name'
        ));

        $fieldset->addField('email', 'text', array(
            'label' => __('Email Address'),
            'class' => 'required-entry validate-email',
            'required' => true,
            'name' => 'email'
        ));

        $fieldset->addField('pin', 'text', array(
            'label' => __('PIN Code (App only)'),
            'class' => 'required-entry validate-number validate-length minimum-length-3 maximum-length-4',
            'required' => true,
            'name' => 'pin',
            'note' => __('must be 4 numbers')
        ));

        $fieldset = $form->addFieldset('User_setting_form', array(
            'legend' => __('User Settings')
        ));



        $fieldset->addField('customer_group', 'multiselect', array(
            'label' => __('Customer Group'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'customer_group',
            'values' => $this->_objectManager->get('Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup')->toOptionArray()
        ));

        $fieldset->addField('location_id', 'multiselect', array(
            'label' => __('Location'),
            'required' => true,
            'class' => 'required-entry',
            'name' => 'location_id',
            'values' => $this->_objectManager->get('Magestore\Webpos\Model\Location\Location')->getValuesForForm()
        ));

        $fieldset->addField('role_id', 'select', array(
            'label' => __('Role'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'role_id',
            'values' => $this->_objectManager->get('Magestore\Webpos\Model\Staff\Role')->getValuesForForm()
        ));

        $fieldset->addField('status', 'select', array(
            'label' => __('Status'),
            'name' => 'status',
            'values' => $this->_objectManager->get('Magestore\Webpos\Model\Source\Adminhtml\Status')->toOptionArray()
        ));

        $fieldset->addField('pos_ids', 'multiselect', array(
            'label' => __('POS'),
            'name' => 'pos_ids',
            'values' => $this->_objectManager->get('Magestore\Webpos\Model\Pos\Pos')->toOptionArray()
        ));
        unset($data['password']);
        unset($data['password_confirmation']);

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getStaff()
    {
        return $this->_coreRegistry->registry('current_staff');
    }


    /**
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->getStaff()->getId() ? __("Edit Staff %1",
            $this->escapeHtml($this->getStaff()->getDisplayName())) : __('New Staff');
    }


    /**
     * @return mixed
     */
    public function getTabLabel()
    {
        return __('Staff Information');
    }


    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return __('Staff Information');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
