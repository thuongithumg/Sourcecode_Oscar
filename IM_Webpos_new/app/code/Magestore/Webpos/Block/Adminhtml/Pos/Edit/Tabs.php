<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Adminhtml\Pos\Edit;

/**
 * Class Tabs
 * @package Magestore\Webpos\Block\Adminhtml\Pos\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('webpos_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('POS Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'webpos_form',
            [
                'label' => __('General Information'),
                'title' => __('General Information'),
                'content' => $this->getLayout()->createBlock('Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab\Form')
                    ->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'webpos_cash_denomination',
            [
                'label' => __('Cash Denominations'),
                'title' => __('Cash Denominations'),
                'class' => 'ajax',
                'url' => $this->getUrl('*/*/denominations', array('_current' => true, 'id' => $this->getRequest()->getParam('id')))
            ]
        );

        $this->addTab(
            'webpos_closed_session_detail',
            [
                'label' => __('Closed Sessions'),
                'title' => __('Closed Sessions'),
                'class' => 'ajax',
                'url' => $this->getUrl('*/*/sessions', array('_current' => true, 'id' => $this->getRequest()->getParam('id')))
            ]
        );

        $this->addTab(
            'webpos_current_session_detail',
            [
                'label' => __('Current Sessions Detail'),
                'title' => __('Current Sessions Detail'),
                'content' => $this->getLayout()->createBlock('Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab\Detail')
                    ->setTemplate('Magestore_Webpos::pos/detail.phtml')
                    ->toHtml(),
            ]
        );

        return parent::_beforeToHtml();
    }

}