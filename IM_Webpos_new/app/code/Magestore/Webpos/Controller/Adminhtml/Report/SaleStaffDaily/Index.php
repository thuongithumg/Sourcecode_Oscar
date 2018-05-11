<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Report\SaleStaffDaily;

class Index extends \Magestore\Webpos\Controller\Adminhtml\Report\AbstractReport
{
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute()
    {        
        $this->_initAction()->_setActiveMenu(
            'Magestore_Webpos::reports'
        )->_addBreadcrumb(
            __('Sales by staff (Daily)'),
            __('Sales by staff (Daily)')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Sales by staff (Daily)'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_report_salestaffdaily.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
