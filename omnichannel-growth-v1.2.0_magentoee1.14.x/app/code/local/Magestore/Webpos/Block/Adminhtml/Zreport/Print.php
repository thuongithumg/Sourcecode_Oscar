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

class Magestore_Webpos_Block_Adminhtml_Zreport_Print extends Mage_Adminhtml_Block_Template
{

    public function getZreportData(){
        $id = $this->getRequest()->getParam('id');
        $shiftRepository = Mage::getModel('webpos/shift')->load($id);
        $data = $shiftRepository->getInfo($id);
        $onlyprint = $this->getRequest()->getParam('onlyprint');
        $difference = $this->getRequest()->getParam('difference');
        $theoreticalClosingBalance = $this->getRequest()->getParam('theoretical_closing_balance');
        $realClosingBalance = $this->getRequest()->getParam('real_closing_balance');
        $closedAt = $this->getRequest()->getParam('closed_at');
        $data['onlyprint'] = ($onlyprint == 1)?true:false;
        if($data['status'] != 1){
            if(!empty($realClosingBalance)){
                $data['closed_amount'] = $realClosingBalance;
            }
            if(!empty($closedAt)){
                $data['closed_at'] = $closedAt;
            }
        }
        if(empty($theoreticalClosingBalance)){
            $theoreticalClosingBalance = floatval($data['float_amount']) + floatval($data['cash_sale']) + floatval($data['cash_added']) - floatval($data['cash_removed']);
        }
        if(empty($difference)){
            $difference = floatval($data['closed_amount']) - floatval($theoreticalClosingBalance);
        }
        $data['theoretical_closing_balance'] = $theoreticalClosingBalance;
        $data['difference'] = $difference;
        return $data;
    }

    public function formatReportPrice($price){
        return Mage::helper('core')->currency($price, true, false);
    }

    public function formatReportDate($date){
        return Mage::helper('webpos')->formatDate($date);
    }
}