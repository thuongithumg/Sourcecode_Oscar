<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Adminhtml\Denomination\Edit;

/**
 * Class Tabs
 * @package Magestore\Webpos\Block\Adminhtml\Denomination\Edit
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
        $this->setTitle(__('Denomination Information'));
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
                'content' => $this->getLayout()->createBlock('Magestore\Webpos\Block\Adminhtml\Denomination\Edit\Tab\Form')
                    ->toHtml(),
                'active' => true
            ]
        );

//        $this->addTab(
//            'user_section',
//            [
//                'label' => __('User List'),
//                'title' => __('User List'),
//                'content' => $this->getLayout()->createBlock('Magestore\Webpos\Block\Adminhtml\Role\Edit\Tab\User')
//                    ->toHtml(),
//                'active' => true
//            ]
//        );

        return parent::_beforeToHtml();
    }

}