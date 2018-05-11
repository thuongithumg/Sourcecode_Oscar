<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Report;

/**
 * class \Magestore\Webpos\Block\Adminhtml\Report\SaleStaff
 * 
 * @category    Magestore
 * @package     Magestore\Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class SaleLocation extends \Magestore\Webpos\Block\Adminhtml\Report\Report
{
    /**
     * contructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_salelocation';
        $this->_blockGroup = 'Magestore_Webpos';
        $this->_headerText = __('Sales by location');
        return parent::_construct();
    }
}

