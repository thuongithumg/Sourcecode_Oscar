<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Report\SalePaymentDaily;

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
            __('Sales by payment method (Daily)'),
            __('Sales by payment method (Daily)')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Sales by payment method (Daily)'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_report_salepaymentdaily.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
