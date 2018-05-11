<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Report;

/**
 * class \Magestore\Webpos\Block\Adminhtml\Report\SaleStaffDaily
 * 
 * @category    Magestore
 * @package     Magestore\Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class SaleStaffDaily extends \Magestore\Webpos\Block\Adminhtml\Report\Report
{
    /**
     * contructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_salestaffdaily';
        $this->_blockGroup = 'Magestore_Webpos';
        $this->_headerText = __('Sales by staff (Daily)');
        return parent::_construct();
    }
}

