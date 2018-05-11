<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Adminhtml\Denomination\Edit\Tab;

/**
 * Class Form
 * @package Magestore\Webpos\Block\Adminhtml\Denomination\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_objectManager;

    protected $_eventManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = array()
    )
    {
        $this->_objectManager = $objectManager;
        $this->_eventManager = $context->getEventManager();
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

        $model = $this->_coreRegistry->registry('current_denomination');
        $data = array();
        if ($model->getId()) {
            $data = $model->getData();
        }
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Denomination Information')));

        if ($model->getData('denomination_id')) {
            $fieldset->addField('denomination_id', 'hidden', array('name' => 'denomination_id'));
        }
        $fieldset->addField('denomination_name', 'text', array(
            'label'     => __('Denomination Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'denomination_name',
            'disabled' => false,
        ));
        $fieldset->addField('denomination_value', 'text', array(
            'label'     => __('Denomination Value'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'denomination_value',
            'disabled' => false,
        ));
        $fieldset->addField('sort_order', 'text', array(
            'label'     => __('Sort Order'),
            'required'  => true,
            'name'      => 'sort_order',
            'disabled' => false,
        ));

        $this->_eventManager->dispatch('webpos_denomination_edit_form', ['form'=>$form,'field_set' => $fieldset,'model_data'=>$model]);
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getCurrentModel() {
        return $this->_coreRegistry->registry('current_denomination');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle() {
        return $this->getCurrentModel()->getId() ? __("Edit Denomination %1",
            $this->escapeHtml($this->getCurrentModel()->getData('denomination_name'))) : __('New Denomination');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Denomination Information');
    }


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Denomination Information');
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