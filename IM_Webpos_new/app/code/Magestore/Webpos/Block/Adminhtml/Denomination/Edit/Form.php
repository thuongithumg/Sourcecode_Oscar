<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Adminhtml\Denomination\Edit;

/**
 * Class Form
 * @package Magestore\Webpos\Block\Adminhtml\Denomination\Edit
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            array(
                'data' => array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', ['store' => $this->getRequest()->getParam('store')]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                )
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}