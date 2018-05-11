<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Adminhtml\Location\Edit\Tab;

/**
 * Class Form
 * @package Magestore\Webpos\Block\Adminhtml\Location\Edit\Tab
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

        $model = $this->_coreRegistry->registry('current_location');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Location Information')));

        if ($model->getData('location_id')) {
            $fieldset->addField('location_id', 'hidden', array('name' => 'location_id'));
        }

        $fieldset->addField('display_name', 'text', array(
            'label'     => __('Location Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'display_name',
            'disabled' => false,
        ));

        $fieldset->addField('store_id', 'select', array(
            'label' => __('Store View'),
            'required' => true,
            'class' => 'required-entry',
            'name' => 'store_id',
            'values' => $this->_objectManager->get('\Magento\Store\Model\System\Store')->getStoreValuesForForm()
        ));

        $fieldset->addField('address', 'text', array(
            'label'     => __('Address'),
            'name'      => 'address',
            'disabled' => false,
        ));

        $fieldset->addField('description', 'textarea', array(
            'label'     => __('Description'),
            'name'      => 'description',
            'disabled' => false,
        ));
        $this->_eventManager->dispatch('webpos_location_edit_form', ['form'=>$form,'field_set' => $fieldset,'model_data'=>$model]);
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getCurrentModel() {
        return $this->_coreRegistry->registry('current_location');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle() {
        return $this->getCurrentModel()->getId() ? __("Edit Location %1",
            $this->escapeHtml($this->getCurrentModel()->getData('display_name'))) : __('New Location');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Location Information');
    }


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Location Information');
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