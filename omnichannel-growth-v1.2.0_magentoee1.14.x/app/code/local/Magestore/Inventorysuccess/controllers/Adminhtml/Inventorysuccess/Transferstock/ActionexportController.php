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
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
use Magestore_Inventorysuccess_Model_Transferstock_Activity as Activity;
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Transferstock_ActionexportController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * download csv action
     */
    public function exportAction(){
        $activityId    = $this->getRequest()->getParam('activity_id');
        $transferActivity = Mage::getModel('inventorysuccess/transferstock_activity')->load($activityId);
        $fileName       = 'transfer_product_list.csv';
        if( $transferActivity->getActivityType() == Activity::ACTIVITY_TYPE_RECEIVING ){
            $fileName       = 'received_product_list.csv';
        }elseif($transferActivity->getActivityType() == Activity::ACTIVITY_TYPE_RETURNING){
            $fileName       = 'returned_product_list.csv';
        }elseif($transferActivity->getActivityType() == Activity::ACTIVITY_TYPE_DELIVERY){
            $fileName       = 'delivered_product_list.csv';
        }
        //prepare csv contents
        #prepare header
        $csv = '';
        $_columns = array(
            "Name",
            "Sku",
            "Qty",
        );
        $data = array();
        foreach ($_columns as $column) {
            $data[] = '"'.$column.'"';
        }
        $csv .= implode(',', $data)."\n";
        $csv = $this->prepareData($activityId,$csv);
        //now $csv varaible has csv data as string
        $this->_prepareDownloadResponse($fileName, $csv);
    }

    /**
     * @param $transfer_id
     * @param $csv
     * @return string
     */
    public function prepareData($activityId,$csv){
        $collection = Mage::getModel('inventorysuccess/transferstock_activity_product')->getCollection()
            ->addFieldToFilter('activity_id', $activityId);
        foreach($collection as $stock){
            $data = array();
            $data[] = $stock->getProductName();
            $data[] = $stock->getProductSku();
            $data[] = $stock->getQty();
            $csv .= implode(',', $data)."\n";
        }
        return $csv;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/inventorysuccess/stockcontrol/stock_transfer');
    }
}
