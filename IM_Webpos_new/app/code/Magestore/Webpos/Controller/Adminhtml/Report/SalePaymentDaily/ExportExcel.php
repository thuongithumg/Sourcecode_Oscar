<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Report\SalePaymentDaily;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportExcel extends \Magestore\Webpos\Controller\Adminhtml\Report\AbstractReport
{
    /**
     * Export sales report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'sale_payment_daily.xml';
        $grid = $this->_view->getLayout()->createBlock('Magestore\Webpos\Block\Adminhtml\Report\Salepaymentdaily\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), DirectoryList::VAR_DIR);
    }
}
