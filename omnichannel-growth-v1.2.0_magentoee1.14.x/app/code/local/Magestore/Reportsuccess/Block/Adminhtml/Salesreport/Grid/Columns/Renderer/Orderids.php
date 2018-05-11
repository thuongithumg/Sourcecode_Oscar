<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
class Magestore_ReportSuccess_Block_Adminhtml_Salesreport_Grid_Columns_Renderer_Orderids
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if($row->getAction()){
            return;
        }
        //$orderIds = $row->getOrderId();
        $orderIds = $row->getOrderIdsView();
        return '<a data-toggle="modal" data-target="#report_salesreport_order_modal" onclick="showReportOrder(\'' . $orderIds . '\')">' .
        $this->__('View') .
        '</a>';
    }
}
