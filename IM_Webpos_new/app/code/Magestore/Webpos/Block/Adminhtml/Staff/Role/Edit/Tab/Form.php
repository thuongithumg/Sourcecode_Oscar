<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Staff\Role\Edit\Tab;

/**
 * Class Form
 * @package Magestore\Giftwrap\Block\Adminhtml\Staff\Role\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = array()
    )
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
    }


    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('current_role');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Role Information')));

        if ($model->getRoleId()) {
            $fieldset->addField('role_id', 'hidden', array('name' => 'role_id','value' => $model->getRoleId()));
        }

        $fieldset->addField('display_name', 'text', array(
            'label'     => __('Role Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'display_name',
            'disabled' => false,
        ));

        $fieldset->addField('maximum_discount_percent', 'text', array(
            'label'     => __('Maximum discount percent(%)'),
            'name'      => 'maximum_discount_percent',
            'disabled' => false,
            'class' => 'validate-number',
            'note' => __(' Maximum discount percent cannot be higher than 100.')
        ));

        $fieldset->addField('description', 'textarea', array(
            'label'     => __('Description'),
            'name'      => 'description',
            'disabled' => false,
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getRole() {
        return $this->_coreRegistry->registry('current_role');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle() {
        return $this->getRole()->getId() ? __("Edit Role %1",
            $this->escapeHtml($this->getRole()->getDisplayName())) : __('New Role');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Role Information');
    }


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Role Information');
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
