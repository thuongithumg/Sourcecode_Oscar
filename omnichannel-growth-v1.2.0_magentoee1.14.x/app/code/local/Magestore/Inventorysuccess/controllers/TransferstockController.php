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

/**
 * Adjuststock Index Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_TransferstockController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function indexAction()
    {

    }
    /**
     * download action
     */
    public function downloadAction(){
        $transfer_id    = $this->getRequest()->getParam('id');
        $fileName       = 'transfer_product_list'.Mage::getSingleton('core/date')->date('d-m-Y_H-i-s').'.csv';
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
        $csv = $this->prepareData($transfer_id,$csv);
        //now $csv varaible has csv data as string
        $this->_prepareDownloadResponse($fileName, $csv);
    }

    /**
     * @param $transfer_id
     * @param $csv
     * @return string
     */
    public function prepareData($transfer_id,$csv){
        $transfer_stocks = Mage::getModel('inventorysuccess/transferstock_product')->getCollection()
        ->addFieldToFilter('transferstock_id',$transfer_id);
        foreach($transfer_stocks as $stock){
            $data = array();
            $data[] = $stock->getProductName();
            $data[] = $stock->getProductSku();
            $data[] = $stock->getQty();
            $csv .= implode(',', $data)."\n";
        }
        return $csv;
    }
}