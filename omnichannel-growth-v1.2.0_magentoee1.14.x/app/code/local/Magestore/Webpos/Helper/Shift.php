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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Helper_Shift extends Mage_Core_Helper_Abstract
{
    /**
     * @return int
     */
    public function getCurrentShiftId()
    {
        $staffId = Mage::helper('webpos/permission')->getCurrentUser();
        $staffModel = Mage::getModel('webpos/user')->load($staffId);
        $locationId = $staffModel->getLocationId();
        $shiftModel = Mage::getModel('webpos/shift');
        $shiftId = $shiftModel->getCurrentShiftId($staffId);

        return $shiftId;
    }

    /**
     * @param $shiftId
     * @return array
     */
    public function prepareOfflineShiftData($shiftId){
        $shiftModel = Mage::getModel('webpos/shift')->getCollection()->addFieldToFilter('shift_id', $shiftId)->getFirstItem();
        $shiftData = $shiftModel->getData();
        $shiftData = $shiftModel->updateShiftDataCurrency($shiftData);
        $saleSummaryModel = Mage::getModel('webpos/saleSummary');

        $cashTransactionModel = Mage::getModel('webpos/shift_cashtransaction');
        //get all sale summary data of the shift with id=$itemData['shift_id']
        $saleSummaryData = $saleSummaryModel->getSaleSummary($shiftId);
        //get all cash transaction data of the shift with id=$itemData['shift_id']
        $transactionData = $cashTransactionModel->getByShiftId($shiftId);
        //get data for zreport
        $zReportSalesSummary = $saleSummaryModel->getZReportSalesSummary($shiftId);

        $shiftData["sale_summary"] = $saleSummaryData;
        $shiftData["cash_transaction"] = $transactionData;
        $shiftData["zreport_sales_summary"] = $zReportSalesSummary;
        if(isset($shiftData['pos_id']) && $shiftData['pos_id']) {
            $shiftData["pos_name"] = Mage::getModel('webpos/pos')->load($shiftData['pos_id'])->getPosName();
        }

        $shiftModel->updateTotalSales($zReportSalesSummary['grand_total']);
        return $shiftData;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createTransaction($data){
        $transaction = Mage::getModel('webpos/shift_cashtransaction');
        if(!empty($data)){
            foreach ($data as $key => $value){
                $transaction->setData($key, $value);
            }
        }
        return $transaction;
    }
    /**
     *@param array $orderPayment
     * @param array $shiftModel
     * @return array $shiftModel
     */
    public function updateShiftWhenCreateOrder($orderPayment, $currentShiftId){
        $shiftModel = Mage::getModel('webpos/shift');
        $shiftModel->load($currentShiftId, 'shift_id');
        if(!$shiftModel->getShiftId()){
            return $this;
        }
        $cashSale = $shiftModel['cash_sale'];
        $balance = $shiftModel['balance'];
        $totalSales = $shiftModel['total_sales'];
        $baseCashSale = $shiftModel['base_cash_sales'];
        $baseBalance = $shiftModel['base_balance'];
        $baseTotalSales = $shiftModel['base_total_sales'];

        if($orderPayment->getMethod() == 'cashforpos'){
            $cashSale = $cashSale + $orderPayment->getRealAmount();
            $balance = $balance + $orderPayment->getRealAmount();
            $baseCashSale = $baseCashSale + $orderPayment->getBaseRealAmount();
            $baseBalance = $baseBalance + $orderPayment->getBaseRealAmount();
            $shiftModel->setCashSale($cashSale);
            $shiftModel->setBaseCashSale($baseCashSale);
            $shiftModel->setBalance($balance);
            $shiftModel->setBaseBalance($baseBalance);
        }
        $totalSales = $totalSales + $orderPayment->getRealAmount();
        $baseTotalSales = $baseTotalSales + $orderPayment->getBaseRealAmount();
        $shiftModel->setTotalSales($totalSales);
        $shiftModel->setBaseTotalSales($baseTotalSales);
        try {
            $shiftModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $shiftModel->getData();
    }

    /**
     *@param array $orderPayment
     * @param array $shiftModel
     * @return array $shiftModel
     */
    public function updateShiftWhenCreateCreditmemo($creditMemo, $currentShiftId){
        $shiftModel = Mage::getModel('webpos/shift');
        $shiftModel->load($currentShiftId, 'shift_id');
        if(!$shiftModel->getShiftId()){
            return $this;
        }
        $cashSale = $shiftModel['cash_sale'];
        $balance = $shiftModel['balance'];
        $totalSales = $shiftModel['total_sales'];
        $baseCashSale = $shiftModel['base_cash_sales'];
        $baseBalance = $shiftModel['base_balance'];
        $baseTotalSales = $shiftModel['base_total_sales'];

        $cashSale = $cashSale - $creditMemo->getGrandTotal();
        $balance = $balance - $creditMemo->getGrandTotal();
        $baseCashSale = $baseCashSale - $creditMemo->getBaseGrandTotal();
        $baseBalance = $baseBalance - $creditMemo->getBaseGrandTotal();
        $shiftModel->setCashSale($cashSale);
        $shiftModel->setBaseCashSale($baseCashSale);
        $shiftModel->setBalance($balance);
        $shiftModel->setBaseBalance($baseBalance);

        $totalSales = $totalSales + $creditMemo->getRealAmount();
        $baseTotalSales = $baseTotalSales + $creditMemo->getBaseRealAmount();
        $shiftModel->setTotalSales($totalSales);
        $shiftModel->setBaseTotalSales($baseTotalSales);
        try {
            $shiftModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $shiftModel->getData();
    }


}
