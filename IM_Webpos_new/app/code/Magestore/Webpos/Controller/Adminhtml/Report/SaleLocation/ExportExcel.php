<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Report\SaleLocation;

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
        $fileName = 'sale_location.xml';
        $grid = $this->_view->getLayout()->createBlock('Magestore\Webpos\Block\Adminhtml\Report\Salelocation\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), DirectoryList::VAR_DIR);
    }
}
